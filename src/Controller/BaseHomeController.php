<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseHomeController extends AbstractController
{
    public function defaultRender(): array
    {
        return [
            'title' => 'CRUD'
        ];

    }
}
