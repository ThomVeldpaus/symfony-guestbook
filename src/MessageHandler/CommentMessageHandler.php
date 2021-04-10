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

        if (!$comment) {
            return;
        }

        // set the state of the Comment Entity object to 'published'
        $comment->setState('published');
        // Flushes data of all entity objects that have queued up before to database
        $this->entityManager->flush();
    }
}