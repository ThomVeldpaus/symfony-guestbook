<?php


namespace App\Message;

/**
 * Class SmsNotification
 *
 * This is a message class, it has to be serializable
 *
 * @package App\Message
 */
class SmsNotification
{
    /**
     * @var string
     */
    private $content;

    /**
     * SmsNotification constructor.
     *
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}
