<?php

namespace yii2tech\tests\unit\activemail\data;

use yii\mail\BaseMailer;
use yii\mail\MessageInterface;

/**
 * Test mailer, which stores messages inside instead of sending them.
 */
class Mailer extends BaseMailer
{
    /**
     * @inheritdoc
     */
    public $messageClass = 'yii2tech\tests\unit\activemail\data\Message';
    /**
     * @var MessageInterface[] list of sent messages.
     */
    public $sentMessages = [];

    /**
     * @inheritdoc
     */
    protected function sendMessage($message)
    {
        $this->sentMessages = $message;
        return true;
    }
}