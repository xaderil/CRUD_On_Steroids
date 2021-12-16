<?php

namespace App\EventSubscriber;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Query\ResultSetMapping;
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

        if ($entity instanceof Book) {

            $entityManager = $args->getObjectManager();
            $entityManager->flush();
            $authors = $entity->getAuthors()->getValues();
            $logger->warning(count($authors));
            foreach ($authors as $author) {
                $logger->warning('work');
                $author = $entityManager->getRepository(Author::class)->findOneBy(array('name' => $author->getName()));
                $authorID = $author->getId();
                $query = $entityManager->createNativeQuery(
                    "UPDATE author
                         SET books_count = (SELECT count(*) 
                                            FROM book_author
                                            WHERE author_id = :id) 
                         WHERE id = :id", new ResultSetMapping());
                $query->setParameter('id', $authorID);
                $query->execute();
                $logger->warning('Dick');

            }
        }
    }
}
