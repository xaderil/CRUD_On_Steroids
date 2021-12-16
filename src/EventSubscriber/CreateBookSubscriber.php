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

// Подписчик на postPersist событие
class CreateBookSubscriber implements EventSubscriber
{

    private $logger;

    public function __construct()
    {
        $this->logger = new Logger('log');
        $this->logger->pushHandler(new StreamHandler('php://stdout', Logger::WARNING));
    }

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

        // Проверяем загруженный в БД объект на соответствие объекту книги
        $entity = $args->getObject();
        if ($entity instanceof Book) {

            $entityManager = $args->getObjectManager();
            $entityManager->flush(); // Обновить БД надо, иначе ошибки в подсчетах будут

            // Пробегаемся по каждому автору и обновляем у него количество книг
            foreach ($entity->getAuthors()->getValues() as $author) {

                // Получаем сам объект
                $author = $entityManager->getRepository(Author::class)->findOneBy(array('name' => $author->getName()));

                // Делаем запрос вроде как на MySQL (но это не точно)
                $query = $entityManager->createNativeQuery(
                    "UPDATE author
                         SET books_count = (SELECT count(*) 
                                            FROM book_author
                                            WHERE author_id = :id) 
                         WHERE id = :id",
                    new ResultSetMapping());
                $query->setParameter('id', $author->getId());
                $query->execute();

            }
        }
    }
}
