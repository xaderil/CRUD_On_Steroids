<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function index(Request $request): Response
    {
        $book = new Book();
        for ($i = 0; $i < HomeController::$authorsCount; $i++) {
            $book->addAuthor(new Author());
        }

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //        $authors = ['Dicker Dickers', 'Dostoevsky'];
            //        $em = $this->getDoctrine()->getManager();
            //
            //        $book = new Book();
            //        $book->setTitle('War and peace');
            //        $book->setDescription('Voevali');
            //        $book->setPublicationYear(2020);
            //        $book->setAuthorsCount(count($authors));
            //        $em->persist($book);
            //        $em->flush();
            //
            //        foreach ($authors as $authorName) {
            //            if ($em->getRepository(Author::class)->findOneBy(array('name' => $authorName))) {
            //                $author = $em->getRepository(Author::class)->findOneBy(array('name' => $authorName));
            //                $author->setBooksCount($author->getBooksCount() + 1);
            //                $author->addBook($book);
            //            } else {
            //                $author = new Author();
            //                $author->setName($authorName);
            //                $author->addBook($book);
            //                $author->setBooksCount(1);
            //                $em->persist($author);
            //            }
            //            $em->flush();
            //
            //        }
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form->createView(),
            'auths' => HomeController::$authorsCount
        ]);
    }

    /**
     * @Route("/inc", name="checkAuthorsCount")
     */
    public function incrementAuthorsCount(): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        HomeController::$authorsCount++;
        return $this->redirectToRoute('home');
    }
}
