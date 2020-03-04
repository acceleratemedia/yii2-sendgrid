<?php

namespace bvb\sendgrid;

use yii\mail\BaseMessage;

/**
 * Message implements a message class based on SendGrid.
 *
 * @see https://github.com/sendgrid
 */
class Message extends BaseMessage
{
    /**
     * {@inheritdoc}
     */
    public function getCharset();

    /**
     * {@inheritdoc}
     */
    public function setCharset($charset);

    /**
     * {@inheritdoc}
     */
    public function getFrom();

    /**
     * {@inheritdoc}
     */
    public function setFrom($from);

    /**
     * {@inheritdoc}
     */
    public function getTo();

    /**
     * {@inheritdoc}
     */
    public function setTo($to);

    /**
     * {@inheritdoc}
     */
    public function getReplyTo();

    /**
     * {@inheritdoc}
     */
    public function setReplyTo($replyTo);

    /**
     * {@inheritdoc}
     */
    public function getCc();

    /**
     * {@inheritdoc}
     */
    public function setCc($cc);

    /**
     * {@inheritdoc}
     */
    public function getBcc();

    /**
     * {@inheritdoc}
     */
    public function setBcc($bcc);

    /**
     * {@inheritdoc}
     */
    public function getSubject();

    /**
     * {@inheritdoc}
     */
    public function setSubject($subject);

    /**
     * {@inheritdoc}
     */
    public function setTextBody($text);

    /**
     * {@inheritdoc}
     */
    public function setHtmlBody($html);

    /**
     * {@inheritdoc}
     */
    public function attach($fileName, array $options = []);

    /**
     * {@inheritdoc}
     */
    public function attachContent($content, array $options = []);

    /**
     * {@inheritdoc}
     */
    public function embed($fileName, array $options = []);

    /**
     * {@inheritdoc}
     */
    public function embedContent($content, array $options = []);

    /**
     * {@inheritdoc}
     */
    public function send(MailerInterface $mailer = null);

    /**
     * {@inheritdoc}
     */
    public function toString();
}
