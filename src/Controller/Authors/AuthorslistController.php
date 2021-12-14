<?php

namespace App\Controller\Authors;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Service\LibrarianService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorslistController extends AbstractController
{

    /**
     * @Route("/authors", name="authors")
     */
    public function index(Request $request, LibrarianService $librarian): Response
    {

        return $this->render('Authors/index.html.twig', [
            'authors' => $librarian->getAllAuthors()
        ]);

    }

    /**
     * @Route("/authors/createAuthor", name="createAuthor")
     */
    public function createBook(Request $request, LibrarianService $librarian): RedirectResponse
    {

        $form = $this->createForm(BookType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $librarian->makeBookObjectInDatabase($form);
        }

        return $this->redirectToRoute('home');

    }

    /**
     * @Route("/authors/deleteAuthor/{authorID}", name="deleteBook")
     */
    public function deleteBook(LibrarianService $librarian, int $bookID): RedirectResponse
    {

        $librarian->burnTheBookInTheBonfire($bookID);
        return $this->redirectToRoute('home');

    }
}