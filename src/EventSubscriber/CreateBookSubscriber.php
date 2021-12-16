<?php

namespace App\EventSubscriber;

use App\Entity\Book;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;



class CreateBookSubscriber implements EventSubscriber
{

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    public function index(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        $logger = new Logger('log');
        $logger->pushHandler(new StreamHandler('php://stdout', Logger::WARNING));

        $logger->warning('Dick');

        if ($entity instanceof Book) {
            $entityManager = $args->getObjectManager();

        }
    }
}
