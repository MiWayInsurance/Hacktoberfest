<?php
declare(strict_types=1);

namespace App\Api;

use Github\Client;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Yaml\Yaml;

class Github
{
    const CACHE_LIFETIME = 300; // 5 minutes

    const AUTHOR_ASSOCIATION = [
        'COLLABORATOR',
        'MEMBER',
        'OWNER',
    ];

    private $client;

    private $projectDir;

    private $cache;

    public function __construct(string $projectDir, string $cacheDir)
    {
        $this->client = new Client();
        $this->projectDir = $projectDir;
        $this->cache = new FilesystemAdapter('', self::CACHE_LIFETIME, $cacheDir);
        $this->client->addCache($this->cache);
    }

    public function getTeamsList(): array
    {
        return Yaml::parse(file_get_contents($this->projectDir.'/config/teams.yaml'))['teams'];
    }

    public function getContributions(string $username): array
    {
        $this->client->authenticate(getenv('GITHUB_AUTH_TOKEN'), Client::AUTH_HTTP_TOKEN);

        $cache = $this->cache->getItem($username);

        if ($cache->isHit()) {
            $prList = $cache->get()['prlist'];
            $user = $cache->get()['user'];
        } else {
            $prList = $this->getPRList($username);
            $prList['list'] = array_map($this->getPRDetails($username), $prList['items']);

            $user = $this->client->users()->show($username);

            $cache->set(['prlist' => $prList, 'user' => $user]);
            $this->cache->save($cache);
        }

        return [
            'total' => $prList['total_count'],
            'total_invalid' => $prList['total_invalid'],
            'list' => $prList['list'],
            'user' => [
                'avatar' => $user['avatar_url'],
                'name' => $user['name'],
                'followers' => $user['followers'],
                'public_repos' => $user['public_repos'],
                'following' => $user['following'],
                'profile' => $user['html_url'],

            ],
        ];
    }

    /**
     * @param string $username
     *
     * @return callable
     */
    protected function getPRDetails(string $username): callable
    {
        return function (array $pr) use ($username): array {
            $status = $pr['state'];

            if ('open' !== $status) {
                [$project, $repo] = explode('/', substr($pr['html_url'], 19));

                $merged = $this->client->pullRequest()->show($project, $repo, $pr['number'])['merged'];
                $status = $merged ? 'merged' : $status;
            }

            return [
                'title' => $pr['title'],
                'url' => $pr['html_url'],
                'status' => $status,
                'valid' => $pr['valid'],
                'invalid_reason' => $pr['invalid_reason'],
            ];
        };
    }

    protected function getPRList(string $username): array
    {
        $params = [
            '-label' => 'invalid',
            'created' => '2017-09-30T00:00:00-12:00..2017-10-31T23:59:59-12:00',
            'type' => 'pr',
            'is' => 'public',
            'author' => $username,
        ];

        $list = $this->client
            ->api('search')
            ->issues(
                implode(
                    ' ',
                    array_map(function ($k, $v) { return "$k:$v"; }, array_keys($params), array_values($params))
                )
            );

        array_walk($list['items'], function (&$pr) {
            $pr['valid'] = true;
            $pr['invalid_reason'] = '';
        });

        $list = $this->filterInvalidPRs($this->filterOwnPRs($list, $username));
        $list['total_count'] = count(array_filter($list['items'], function ($pr) { return $pr['valid']; }));
        $list['total_invalid'] = count(array_filter($list['items'], function ($pr) { return !$pr['valid']; }));

        return $list;
    }

    protected function filterOwnPRs(array $prList, $username): array
    {
        array_walk($prList['items'], function (&$pr) use($username) {
            if (in_array($pr['author_association'], self::AUTHOR_ASSOCIATION)) {
                $pr['valid'] = false;
                $pr['invalid_reason'] = sprintf('%s is a %s on the project', $username, strtolower($pr['author_association']));
            }
        });

        return $prList;
    }

    protected function filterInvalidPRs(array $prList): array
    {
        $invalidPRList = Yaml::parse(file_get_contents($this->projectDir.'/config/teams.yaml'))['invalid_prs'];
        $invalidPRs = array_column($invalidPRList, 'reason', 'url');

        array_walk($prList['items'], function (&$pr) use ($invalidPRs) {
            if (isset($invalidPRs[$pr['html_url']])) {
                $pr['valid'] = false;
                $pr['invalid_reason'] = $invalidPRs[$pr['html_url']];
            }
        });

        return $prList;
    }
}