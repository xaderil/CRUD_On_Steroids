<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Service\LibrarianService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, LibrarianService $librarian): Response
    {

        // Отправляем на страницу форму объекта книги с одним указателем на автора и сразу все книги
        $book = new Book();
        $book->addAuthor(new Author());

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $this->createForm(BookType::class, $book)->createView(),
            'books' => $librarian->getAllBooksFromShelves()
        ]);

    }

    /**
     * @Route("/createBook", name="createBook")
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
     * @Route("/deleteBook/{bookID}", name="delete")
     */
    public function deleteBook(LibrarianService $librarian, int $bookID): RedirectResponse
    {

        $librarian->burnTheBookInTheBonfire($bookID);
        return $this->redirectToRoute('home');

    }
}


