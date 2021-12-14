<?php

namespace App\Controller\Main;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Service\LibrarianService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, LibrarianService $librarian): Response
    {

        return $this->render('Main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);

    }

}