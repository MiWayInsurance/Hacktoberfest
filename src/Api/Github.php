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

    public function __construct($projectDir, $cacheDir)
    {
        $this->client = new Client();
        $this->projectDir = $projectDir;
        $cache = new FilesystemAdapter('', 60 * 60, $cacheDir);
        $this->client->addCache($cache);
    }

    public function getDeveloperList(): array
    {
        $this->client->authenticate(getenv('GITHUB_AUTH_TOKEN'), Client::AUTH_HTTP_TOKEN);

        $prs = [];

        foreach ($this->getTeamsList() as $team => $developers) {
            $prs[$team] = [];
            foreach ($developers as $author) {
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
                $prs[$team][$author] = [
                    'total' => $issues['total_count'],
                    'avatar' => $user['avatar_url'],
                    'name' => $user['name'],
                ];
            }
        }

        return $prs;
    }

    private function getTeamsList(): array
    {
        return Yaml::parse(file_get_contents($this->projectDir.'/config/teams.yaml'))['teams'];
    }
}