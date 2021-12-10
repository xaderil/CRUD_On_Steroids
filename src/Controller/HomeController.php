<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Service\GlobalVariables;
use Symfony\Component\Finder\Glob;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function index(Request $request): Response
    {
        // Делаем форму и чекаем запрос
        $book = new Book();
        $book->addAuthor(new Author());

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        // Если в отправленной форме лежит что-то идеологически верное, то закидываем в БД
        if ($form->isSubmitted() && $form->isValid()) {

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
            return $this->redirectToRoute('home');
        }


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form->createView(),
        ]);
    }
}
