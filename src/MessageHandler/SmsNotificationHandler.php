<?php

namespace App\MessageHandler;

use App\Message\SmsNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Class SmsNotificationHandler
 *
 * This is a MessageHandler class, the logical functionality should be
 * in the __invoke() method.
 *
 * @package App\MessageHandler
 */
class SmsNotificationHandler implements MessageHandlerInterface
{
    /**
     * Because of autoconfiguration and SmsNotification type-hint
     * Symfony knows this is the handler for the SmsNotification message
     *
     * @param \App\Message\SmsNotification $message
     */
    public function __invoke(SmsNotification $message)
    {
        // doing work, like sending an SMS
    }
}
