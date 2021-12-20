<?php

namespace App\Controller\Books;

use App\Entity\Book;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditBookController extends AbstractController
{
    /**
     * @Route("/editBook", name="editBook", methods={"POST"})
     */
    public function ajaxGetFieldsOfBook(Request $request)
    {

        if ($request->isXMLHttpRequest()) {

            $em = $this->getDoctrine()->getManager();
            $book = $em->getRepository(Book::class)->find($request->get('id'));
            return new JsonResponse(array('book' => $book));


        }

        return new Response('This is not ajax!', 400);
    }
}