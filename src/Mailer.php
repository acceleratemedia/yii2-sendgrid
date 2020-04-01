<?php

namespace bvb\sendGrid;

use SendGrid;
use Yii;
use yii\mail\BaseMailer;

/**
 * Mailer implements a mailer based on SendGrid.
 * @see https://github.com/sendgrid
 */
class Mailer extends BaseMailer
{
    /**
     * Key needed to authenticate with SendGrid API
     * @var string
     */
    public $apiKey;

    /**
     * {@inheritdoc}
     */
    public $messageClass = Message::class;

    /**
     * SendGrid instance for working with their API
     * @var \SendGrid
     */
    private $_sendGrid;

    /**
     * Return the SendGrid instance for working with their API
     * @return \SendGrid
     */
    protected function getSendGrid()
    {
        if(empty($this->_sendGrid)){
            if(empty($this->apiKey)){
                throw new InvalidConfigException(__CLASS__.' requires the `apiKey` property to have a value.');
            }
            $this->_sendGrid = new SendGrid($this->apiKey);
        }
        return $this->_sendGrid;
    }

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

        return $this->getSendGrid()->send($message->getSendGridMail());
    }
}
