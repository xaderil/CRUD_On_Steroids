<?php

namespace App\Service;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Persistence\ManagerRegistry;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;


class LibrarianService extends AbstractController
{
    private $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
        $this->logger = new Logger('name');
        $this->logger->pushHandler(new StreamHandler('php://stdout', Logger::WARNING)); // <<< uses a stream
    }

    public function makeBookObjectInDatabase(FormInterface $form) {

        $authorsArrayCollection = $form['authors']->getData();

        // Сначала объект книги закидываем
        $book = new Book();
        $book->setTitle($form['title']->getData());
        $book->setDescription($form['description']->getData());
        $book->setPublicationYear($form['publicationYear']->getData());
        $book->setAuthorsCount(count($authorsArrayCollection));

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        // Потом перебираем каждого автора книги и чекаем его существование. Если нету - закидываем в БД
        foreach ($authorsArrayCollection as $author) {
            if ($this->entityManager->getRepository(Author::class)->findOneBy(array('name' => $author->getName()))) {

                $author = $this->entityManager->getRepository(Author::class)->findOneBy(array('name' => $author->getName()));
                $author->setBooksCount($author->getBooksCount() + 1);
                $author->addBook($book);

            } else {

                $author->addBook($book);
                $author->setBooksCount(1);
                $this->entityManager->persist($author);

            }

            $this->entityManager->flush();
        }

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