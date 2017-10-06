<?php
declare(strict_types=1);

namespace App\Api;

use Github\Client;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Yaml\Yaml;

class Github
{
    private $client;
    private $projectDir;
    private $cache;

    public function __construct($projectDir, $cacheDir)
    {
        $this->client = new Client();
        $this->projectDir = $projectDir;
        $this->cache = new FilesystemAdapter('', 3600, $cacheDir);
        $this->client->addCache($this->cache);
    }

    public function getDeveloperList(): array
    {
        $this->client->authenticate(getenv('GITHUB_AUTH_TOKEN'), Client::AUTH_HTTP_TOKEN);

        $prs = [];

        foreach ($this->getTeamsList() as $team => $developers) {
            $prs[$team] = [];
            foreach ($developers as $author) {

                $cache = $this->cache->getItem($author);

                if ($cache->isHit()) {
                    $issues = $cache->get()['issues'];
                    $user = $cache->get()['user'];
                } else {
                    $params = [
                        '-label' => 'invalid',
                        'created' => '2017-09-30T00:00:00-12:00..2017-10-31T23:59:59-12:00',
                        'type' => 'pr',
                        'is' => 'public',
                        'author' => $author,
                    ];

                    $issues = $this->client->api('search')->issues(implode(' ', array_map(function ($k, $v) {
                        return "$k:$v";
                    }, array_keys($params), array_values($params))));

                    $user = $this->client->users()->show($author);

                    $cache->set(['issues' => $issues, 'user' => $user]);
                    $this->cache->saveDeferred($cache);
                }

                $prs[$team][$author] = [
                    'total' => $issues['total_count'],
                    'avatar' => $user['avatar_url'],
                    'name' => $user['name'],
                ];
            }
        }

        $this->cache->commit();

        return $prs;
    }

    private function getTeamsList(): array
    {
        return Yaml::parse(file_get_contents($this->projectDir.'/config/teams.yaml'))['teams'];
    }
}