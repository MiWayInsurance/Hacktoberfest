<?php
declare(strict_types=1);

namespace App\Action;

use App\Api\Github;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Api
{
    public function __invoke(Github $github, $user): Response
    {
        return new JsonResponse($github->getContributions($user));
    }
}