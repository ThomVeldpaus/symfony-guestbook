<?php


namespace App\MessageHandler;

use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Class CommentMessageHandler
 *
 * This MessageHandler knows how to handle the CommentMessages.
 *
 * @package App\MessageHandler
 */
class CommentMessageHandler implements MessageHandlerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \App\Repository\CommentRepository
     */
    private $commentRepository;

    /**
     * CommentMessageHandler constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Repository\CommentRepository $commentRepository
     */
    public function __construct(EntityManagerInterface $entityManager, CommentRepository $commentRepository)
    {
        $this->entityManager = $entityManager;
        $this->commentRepository = $commentRepository;
    }

    /**
     * The __invoke() method
     *
     * The most conventional thing to do is to have all logic, inside of the __invoke() method.
     *
     * The CommentMessage type hints on this methods CommentMessage argument and tells Messenger which
     * class is going to be handled.
     *
     * @param \App\Message\CommentMessage $message
     */
    public function __invoke(CommentMessage $message)
    {
        // Get comment entity by id from CommentRepository with CommentMessage getId() method
        $comment = $this->commentRepository->find($message->getId());
        $context = $message->getContext();
        $comment->setPhotoFilename($context['photoFilename']);
        $comment->setClientIp($context['clientIp']);

        if (!$comment) {
            return;
        }

        // set the state of the Comment Entity object to 'submitted'
        $comment->setState('submitted');

        // Use the Entity Manager to queue a persist task to create a new comment row in the database
        $this->entityManager->persist($comment);
        // All queued tasks of the EntityManager are written to te database in Bulk
        $this->entityManager->flush();
    }
}