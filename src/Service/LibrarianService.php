<?php

namespace App\Service;

use App\Entity\Author;
use App\Entity\Book;
use App\Event\CreateBookEvent;
use App\EventListener\CreateBookListener;
use App\EventSubscriber\CreateBookSubscriber;
use Doctrine\Persistence\ManagerRegistry;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;


class LibrarianService extends AbstractController
{
    private $entityManager;
    private $logger;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();

        $this->logger = new Logger('name');
        $this->logger->pushHandler(new StreamHandler('php://stdout', Logger::WARNING));

    }

    public function makeBookObjectInDatabase(Request $request) {

        $book = new Book();
        $book->setTitle($request->get('title'));
        $book->setDescription($request->get('description'));
        $book->setPublicationYear($request->get('publicationYear'));
        $book->setAuthorsCount($request->get('authors'));

        // Каждого автора книги проверяем на присутствие в БД
        $authorsNames = $request->get('authors');
        foreach ($authorsNames as $authorName) {

            if ($this->entityManager->getRepository(Author::class)->findOneBy(array('name' => $authorName))) {

                // Т.к. автор в БД уже лежит нужно получить указатель на него, иначе хрен велосипед поедет
                $author = $this->entityManager->getRepository(Author::class)->findOneBy(array('name' => $authorName));

            } else {

                // Если автора в БД нет, то закинем его туда
                $author = new Author();
                $author->setName($authorName);
                $this->entityManager->persist($author);
                $this->entityManager->flush();

            }
            $book->addAuthor($author);

        }
        $this->entityManager->persist($book);
        $this->entityManager->flush();

    }

    public function getAllBooks(): array
    {
        return $this->entityManager->getRepository(Book::class)->findAll();
    }

    public function getAllAuthors(): array
    {
        return $this->entityManager->getRepository(Author::class)->findAll();
    }

    public function burnTheBookInTheBonfire(int $id) {

        $this->entityManager->remove($this->entityManager->getRepository(Book::class)->find($id));
        $this->entityManager->flush();

    }

}