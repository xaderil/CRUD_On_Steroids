<?php

namespace App\Controller\Books;

use App\Entity\Author;
use App\Entity\Book;
use App\Event\CreateBookEvent;
use App\Form\BookType;
use App\Service\LibrarianService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;



class BooklistController extends AbstractController
{

    private $logger;

    public function __construct()
    {
        $this->logger = new Logger('log');
        $this->logger->pushHandler(new StreamHandler('php://stdout', Logger::WARNING));
    }

    /**
     * @Route("/books", name="books")
     */
    public function index(Request $request, LibrarianService $librarian): Response
    {

        // Отправляем на страницу форму объекта книги с одним указателем на автора и сразу все книги
        $book = new Book();
        $book->addAuthor(new Author());


        return $this->render('Books/index.html.twig', [
            'form' => $this->createForm(BookType::class, $book)->createView(),
            'books' => $librarian->getAllBooks()
        ]);

    }

    /**
     * @Route("/books/createBook", name="createBook", methods={"POST"})
     */
    public function createBook(Request $request, LibrarianService $librarian): RedirectResponse
    {

        if ($request->get('title') and $request->get('description') and $request->get('publicationYear') and $request->get('authors')) {

            $librarian->makeBookObjectInDatabase($request);
        } else {
            $this->logger->warning('Беда с башкой'); // Сделать валидацию формы на стороне клиента
        }

        return $this->redirectToRoute('books');

    }

    /**
     * @Route("/books/deleteBook/{bookID}", name="deleteBook")
     */
    public function deleteBook(LibrarianService $librarian, int $bookID): RedirectResponse
    {

        $librarian->burnTheBookInTheBonfire($bookID);
        return $this->redirectToRoute('books');

    }
}