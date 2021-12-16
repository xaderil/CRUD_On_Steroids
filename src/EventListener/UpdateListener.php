<?php

namespace App\EventListener;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Event\RequestEvent;


class UpdateListener extends EventDispatcher
{

    public function onKernelRequest(RequestEvent $event)
    {

//        $log = new Logger('name');
//        $log->pushHandler(new StreamHandler('php://stdout', Logger::WARNING));
//
//        $book = $event->getRequest()->get('book');
//        $authorNames = $book['authors'];
//        if ($book) {
//            $log->warning($book['title']);
//        } else {
//            $log->warning('Request does not contain book object');
//        }

    }

}