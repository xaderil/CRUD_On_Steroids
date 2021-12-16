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
    private $dispatcher;
    private $logger;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();

        $this->logger = new Logger('name');
        $this->logger->pushHandler(new StreamHandler('php://stdout', Logger::WARNING));

        $this->dispatcher = new EventDispatcher();
    }

    public function makeBookObjectInDatabase(FormInterface $form, Request $request) {


        $requestObject = $request->request;
        foreach ($requestObject->all() as $data) {
            if(is_array($data)) {
                foreach ($data as $string) {
                    if (is_array($string)) {
                        foreach ($string as $elem) {
                            if (is_array($elem)) {
                                foreach ($elem as $hui) {
                                    $this->logger->warning($hui);
                                }
                            } else {
                                $this->logger->warning($elem);
                            }
                        }
                    } else {
                        $this->logger->warning($string);
                    }
                }
            } else {
                $this->logger->warning($data);
            }

        }
        ;
        // Сначала объект книги закидываем
        $book = new Book();
        $book->setTitle($form['title']->getData());
        $book->setDescription($form['description']->getData());
        $book->setPublicationYear($form['publicationYear']->getData());
        $book->setAuthorsCount(count($authors));
        foreach ($authors as $author) {
            if ($this->entityManager->getRepository(Author::class)->findOneBy(array('name' => $author->getName()))) {
                // Т.к. автор в БД уже лежит нужно получить указатель на него, иначе хрен велосипед поедет
                $author = $this->entityManager->getRepository(Author::class)->findOneBy(array('name' => $author->getName()));
            } else {
                $this->entityManager->persist($author);
                $this->entityManager->flush();
            }
            $book->addAuthor($author);

        }
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        // Потом перебираем каждого автора книги и чекаем его существование. Если нету - закидываем в БД
//        foreach ($authorsArrayCollection as $author) {
//            if ($this->entityManager->getRepository(Author::class)->findOneBy(array('name' => $author->getName()))) {
//
////
////                $author = $this->entityManager->getRepository(Author::class)->findOneBy(array('name' => $author->getName()));
////                $author->addBook($book);
//
//            } else {
//
////                $author->addBook($book);
//                $this->entityManager->persist($author);
//
//            }
//
//            $this->entityManager->flush();
//        }

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