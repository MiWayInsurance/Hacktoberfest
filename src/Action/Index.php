<?php
declare(strict_types=1);

namespace App\Action;

use App\Api\Github;
use Symfony\Component\HttpFoundation\Response;

class Index
{
    public function __invoke(\Twig_Environment $twig, Github $github): Response
    {
        return new Response($twig->render('index.html.twig', ['data' =>  $github->getTeamsList()]));
    }
}