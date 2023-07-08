<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public const SORT_UUID = 'uuid';
    public const FILE_PATH = '/temp/message/';

    public SerializerInterface $serializer;

    public function __construct(
        ManagerRegistry $registry,
        SerializerInterface $serializer
    )
    {
        parent::__construct($registry, Message::class);
        $this->serializer = $serializer;
    }

    public function save(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllSorted(string $sortBy)
    {
        $qb = $this->createQueryBuilder('m');

        switch ($sortBy) {
            case self::SORT_UUID:
                $qb->orderBy('m.uuid', 'ASC');
                break;
            default:
                $qb->orderBy('m.createDate', 'DESC');
                break;
        }

        return $qb->getQuery()->getResult();
    }

    public function saveMessageToFile(Message $message): void
    {
        $messageFilePath = self::FILE_PATH . 'msg' . $message->getId() . '.json';

        $messageData = [
            'id' => $message->getId(),
            'content' => $message->getContent(),
            'createDate' => $message->getCreateDate()->format('Y-m-d H:i:s'),
        ];

        $messageJson = $this->serializer->serialize($messageData, 'json', [
            'json_encode_options' => JSON_PRETTY_PRINT,
        ]);

        file_put_contents($messageFilePath, $messageJson);
    }

}
