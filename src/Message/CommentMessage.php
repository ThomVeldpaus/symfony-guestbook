<?php

namespace App\Message;

/**
 * Class CommentMessage
 *
 * This class will represent a Comment Message and will be added to a queue by
 * a Messenger Bus. He does this so when some operations can execute asynchronously.
 * The messenger bus will return as soon as the Message is in the queue.
 *
 * A consumer always runs to read the Messages. The consumer will execute the logic in the
 * Message.
 *
 * A Message can't contain any logic. It is a data object. It will also be serialised when added
 * to the queue, so you can only add serializable data in the class.
 *
 * @package App\Message
 */
class CommentMessage
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var array
     */
    private $context;

    /**
     * CommentMessage constructor.
     *
     * @param int $id
     * @param array $context
     */
    public function __construct(int $id, array $context = [])
    {
        $this->id = $id;
        $this->context = $context;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}