<?php

namespace bvb\sendgrid;

use yii\mail\BaseMailer;

/**
 * Mailer implements a mailer based on SendGrid.
 * @see https://github.com/sendgrid
 */
class Mailer extends BaseMailer
{
    /**
     * {@inheritdoc}
     */
    public $messageClass = Message::class;

    /**
     * {@inheritdoc}
     */
    protected function sendMessage($message)
    {
        /* @var $message Message */
        $address = $message->getTo();
        if (is_array($address)) {
            $address = implode(', ', array_keys($address));
        }
        Yii::info('Sending email "' . $message->getSubject() . '" to "' . $address . '"', __METHOD__);

        return $this->getSwiftMailer()->send($message->getSwiftMessage()) > 0;
    }
}
