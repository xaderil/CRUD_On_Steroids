<?php

namespace App\Service;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;


class LibrarianService extends AbstractController
{

    public function makeBookObjectInDatabase(FormInterface $form) {

        $authorsArrayCollection = $form['authors']->getData();

        // Сначала объект книги закидываем
        $book = new Book();
        $book->setTitle($form['title']->getData());
        $book->setDescription($form['description']->getData());
        $book->setPublicationYear($form['publicationYear']->getData());
        $book->setAuthorsCount(count($authorsArrayCollection));

        $em = $this->getDoctrine()->getManager();
        $em->persist($book);
        $em->flush();

        // Потом перебираем каждого автора книги и чекаем его существование. Если нету - закидываем в БД
        foreach ($authorsArrayCollection as $author) {
            if ($em->getRepository(Author::class)->findOneBy(array('name' => $author->getName()))) {

                $author = $em->getRepository(Author::class)->findOneBy(array('name' => $author->getName()));
                $author->setBooksCount($author->getBooksCount() + 1);
                $author->addBook($book);

            } else {

                $author->addBook($book);
                $author->setBooksCount(1);
                $em->persist($author);

            }

            $em->flush();
        }

    }

    public function takeAllBooksFromShelves() {

        $em = $this->getDoctrine()->getManager();
        $books = $em->getRepository(Book::class)->findAll();
        return $books;
    }

}