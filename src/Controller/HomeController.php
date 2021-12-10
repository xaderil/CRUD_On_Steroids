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
    public function index(Request $request, GlobalVariables $globalVariables): Response
    {

        $book = new Book();
        $book->addAuthor(new Author());

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

//        if ($form->isSubmitted() && $form->isValid()) {
                    $authors = ['Dicker Dickers', 'Dostoevsky'];
                    $em = $this->getDoctrine()->getManager();

                    $book = new Book();
                    $book->setTitle('War and peace');
                    $book->setDescription('Voevali');
                    $book->setPublicationYear(2020);
                    $book->setAuthorsCount(count($authors));
                    $em->persist($book);
                    $em->flush();

                    foreach ($authors as $authorName) {
                        if ($em->getRepository(Author::class)->findOneBy(array('name' => $authorName))) {
                            $author = $em->getRepository(Author::class)->findOneBy(array('name' => $authorName));
                            $author->setBooksCount($author->getBooksCount() + 1);
                            $author->addBook($book);
                        } else {
                            $author = new Author();
                            $author->setName($authorName);
                            $author->addBook($book);
                            $author->setBooksCount(1);
                            $em->persist($author);
                        }
                        $em->flush();
            //
            //        }
        }
        $globalVariables->counter++;
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form->createView(),
            'auths' => $globalVariables->counter
        ]);
    }

    /**
     * @Route("/inc", name="checkAuthorsCount")
     */
    public function incrementAuthorsCount(GlobalVariables $globalVariables): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $globalVariables->setCounter($globalVariables->getCounter() + 1);
        return $this->redirectToRoute('home');
    }
}
