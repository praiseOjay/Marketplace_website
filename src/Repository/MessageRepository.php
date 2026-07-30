<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Find thread between two users sorted by creation date ASC
     */
    public function findThread(User $user1, User $user2): array
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :user1 AND m.recipient = :user2) OR (m.sender = :user2 AND m.recipient = :user1)')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get recent conversation partners for a user
     */
    public function findConversationsForUser(User $user): array
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.sender = :user OR m.recipient = :user')
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'DESC');

        /** @var Message[] $messages */
        $messages = $qb->getQuery()->getResult();
        $conversations = [];

        foreach ($messages as $msg) {
            $partner = ($msg->getSender() === $user) ? $msg->getRecipient() : $msg->getSender();
            $partnerId = $partner->getId();

            if (!isset($conversations[$partnerId])) {
                $conversations[$partnerId] = [
                    'partner' => $partner,
                    'lastMessage' => $msg,
                    'unreadCount' => 0,
                ];
            }
        }

        $unreads = $this->createQueryBuilder('m')
            ->select('IDENTITY(m.sender) as senderId, COUNT(m.id) as unreadCount')
            ->where('m.recipient = :user AND m.isRead = false')
            ->setParameter('user', $user)
            ->groupBy('m.sender')
            ->getQuery()
            ->getResult();

        foreach ($unreads as $unread) {
            $sId = $unread['senderId'];
            if (isset($conversations[$sId])) {
                $conversations[$sId]['unreadCount'] = (int) $unread['unreadCount'];
            }
        }

        return array_values($conversations);
    }

    public function countUnreadForUser(User $user): int
    {
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.recipient = :user AND m.isRead = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
