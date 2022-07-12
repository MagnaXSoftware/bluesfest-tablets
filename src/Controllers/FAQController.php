<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;

class FAQController extends Controller
{

    public function faq(ResponseInterface $response): ResponseInterface
    {
        return $this->twig->render($response, 'faq.html.twig', [
            'title' => 'FAQ',
        ]);
    }

}