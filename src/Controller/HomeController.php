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
        // Делаем форму и чекаем запрос
        $book = new Book();
        $book->addAuthor(new Author());

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        // Если в отправленной форме лежит что-то идеологически верное, то закидываем в БД книгу с авторами
        if ($form->isSubmitted() && $form->isValid()) {

            $librarian->makeBookObjectInDatabase($form);
            return $this->redirectToRoute('home');

        }

        $books = $librarian->getAllBooksFromShelves();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form->createView(),
            'books' => $books
        ]);
    }

    /**
     * @Route("/delete/{bookID}", name="delete")
     */
    public function delete(LibrarianService $librarian, int $bookID): RedirectResponse
    {
        $librarian->burnTheBookInTheBonfire($bookID);
        return $this->redirectToRoute('home');
    }
}


