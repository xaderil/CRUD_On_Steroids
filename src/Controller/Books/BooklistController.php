<?php

namespace App\Controller\Books;

use App\Entity\Book;
use App\Service\LibrarianService;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class BooklistController extends AbstractController
{

    private $logger;
    private $librarian;

    public function __construct(LibrarianService $librarian)
    {
        $this->logger = new Logger('log');
        $this->logger->pushHandler(new StreamHandler('php://stdout', Logger::WARNING));

        $this->librarian = $librarian;
    }

    /**
     * @Route("/books", name="books")
     */
    public function index(Request $request): Response
    {

        return $this->render('Books/index.html.twig', [
            'books' => $this->librarian->getAllBooks()
        ]);

    }


    /**
     * @Route("/books/createBook", name="createBook", methods={"POST"})
     */
    public function createBook(Request $request): RedirectResponse
    {

        if ($request->get('title') and $request->get('description') and $request->get('publicationYear') and $request->get('authors')) {

            $this->librarian->makeBookObjectInDatabase($request);

        } else {

            $this->logger->warning('Беда с башкой'); // Сделать валидацию формы на стороне клиента

        }

        return $this->redirectToRoute('books');

    }

    /**
     * @Route("/books/editBook/", name="editBook")
     */
    public function editBook(Request $request)
    {

        if($request->get('title')) {
            $this->logger->warning("Dick");
            $this->librarian->editBook($request);

        }
        return $this->redirectToRoute('books');

    }


    /**
     * @Route("/books/deleteBook/{bookID}", name="deleteBook")
     */
    public function deleteBook(int $bookID): RedirectResponse
    {

        $this->librarian->burnTheBookInTheBonfire($bookID);
        return $this->redirectToRoute('books');

    }




    /**
     * @Route("/books/{query}", name="filterQuery")
     */
    public function filterQuery($query): Response
    {
        if ($query == "sql") {

            $books = $this->librarian->getRequiredBooksUsingSQL();
            $this->logger->warning(gettype($books));
            foreach ($books as $book) {
                $this->logger->warning($book);
            }
            return $this->forward('App\Controller\Books\BooklistController::showBooks', ['books' => $books]);

        } elseif ($query == "orm") {

            $books = $this->librarian->getRequiredBooksUsingORM();
            return $this->forward('App\Controller\Books\BooklistController::showBooks', ['books' => $books]);

        }
        return $this->redirectToRoute('books');

    }


    public function showBooks($books): Response
    {

        return $this->render('Books/index.html.twig', [
            'books' => $books
        ]);

    }

}