<?php
declare(strict_types=1);

namespace App\Api;

use Github\Client;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Yaml\Yaml;

class Github
{
    const AUTHOR_ASSOCIATION = [
        'COLLABORATOR',
        'MEMBER',
        'OWNER'
    ];

    private $client;

    private $projectDir;

    private $cache;

    public function __construct($projectDir, $cacheDir)
    {
        $this->client = new Client();
        $this->projectDir = $projectDir;
        $this->cache = new FilesystemAdapter('', 300, $cacheDir);
        $this->client->addCache($this->cache);
    }

    public function getTeamsList(): array
    {
        return Yaml::parse(file_get_contents($this->projectDir.'/config/teams.yaml'))['teams'];
    }

    public function getContributions($user)
    {
        $this->client->authenticate(getenv('GITHUB_AUTH_TOKEN'), Client::AUTH_HTTP_TOKEN);

        $cache = $this->cache->getItem($user);

        if ($cache->isHit()) {
            $issues = $cache->get()['issues'];
            $user = $cache->get()['user'];
        } else {
            $params = [
                '-label' => 'invalid',
                'created' => '2017-09-30T00:00:00-12:00..2017-10-31T23:59:59-12:00',
                'type' => 'pr',
                'is' => 'public',
                'author' => $user,
            ];

            $issues = $this->client->api('search')->issues(implode(' ', array_map(function ($k, $v) {
                return "$k:$v";
            }, array_keys($params), array_values($params))));

            $issues['items'] = array_filter($issues['items'], function ($issue) {
                return !in_array($issue['author_association'], self::AUTHOR_ASSOCIATION);
            });

            $issues['total_count'] = count($issues['items']);

            $user = $this->client->users()->show($user);

            $cache->set(['issues' => $issues, 'user' => $user]);
            $this->cache->save($cache);
        }

        return [
            'total' => $issues['total_count'],
            'avatar' => $user['avatar_url'],
            'name' => $user['name'],
            'list' => array_column($issues['items'],  'title', 'html_url')
        ];
    }
}