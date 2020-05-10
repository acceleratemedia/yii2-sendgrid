<?php

namespace bvb\sendGrid;

use yii\base\NotSupportedException;
use yii\mail\BaseMessage;

/**
 * Message implements a message class based on SendGrid.
 *
 * @see https://github.com/sendgrid
 */
class Message extends BaseMessage
{
    /**
     * The piece of mail that is being composed using SendGrid's php
     * library for their API
     * @var \SendGrid\Mail\Mail
     */
    private $_sendGridMail;

    /**
     * Get the instance of the sendgrid mail message
     * @return \SendGrid\Mail\Mail
     */
    public function getSendGridMail()
    {
        if(empty($this->_sendGridMail)){
            $this->_sendGridMail = new \SendGrid\Mail\Mail();
        }
        return $this->_sendGridMail;
    }

    /**
     * {@inheritdoc}
     */
    public function getCharset()
    {
        $headers = $this->getSendGridMail()->getHeaders();
        if($headers === null){
            return null;
        }
        // --- do something once we figure out when it'd be useful
    }

    /**
     * {@inheritdoc}
     */
    public function setCharset($charset)
    {
        throw new NotSupportedException('setCharset() has not been implemented');
    }

    /**
     *
     * @return string|array the source
     */
    protected function getSource($sourceType)
    {
        $functionCall = "get" . ucfirst($sourceType);
        $source = $this->getSendGridMail()->$functionCall();
        if($source === null){
            return $source;
        }
        $name = $source->getName();
        $email = $source->getEmail();

        // --- If email/name combo set as such otherwise just add email value
        if(!empty($name)){
            return [$email => $name];
        } else {
            return $email;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFrom(){
        return $this->getSource('from');
    }

    /**
     * Sets the message sender.
     * @param string|array $from sender email address.
     * You may also specify sender name in addition to email address using format:
     * `[email => name]`.
     * @return $this self reference.
     */
    public function setFrom($from)
    {
        if(is_array($from)){
            $this->getSendGridMail()->setFrom(array_key_first($from), $from[array_key_first($from)]);
        } else {
            $this->getSendGridMail()->setFrom($from);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReplyTo()
    {
        return $this->getSource('replyTo');
    }

    /**
     * {@inheritdoc}
     */
    public function setReplyTo($replyTo)
    {
        if(is_array($replyTo)){
            $this->getSendGridMail()->setReplyTo(array_key_first($replyTo), $replyTo[array_key_first($replyTo)]);
        } else {
            $this->getSendGridMail()->setReplyTo($replyTo);
        }
        return $this;
    }

    /**
     * Gets a simple array of email/name data from SendGrid's personalizations
     *  @return array|string
     */
    public function getRecipientsFromPersonalizations($personalizationType)
    {
        $functionCall = "get" . ucfirst($personalizationType).'s';
        $recipients = [];
        foreach($this->getSendGridMail()->getPersonalizations() as $personalization){
            $recipientsOfType = $personalization->$functionCall();
            if($recipientsOfType === null){
                return null;
            }
            foreach($recipientsOfType as $recipientOfType){
                $name = $recipientOfType->getName();
                $email = $recipientOfType->getEmail();
                // --- If email/name combo set as such otherwise just add email value
                if(!empty($name)){
                    $recipients[$email] = $name;
                } else {
                    $recipients[] = $email;
                }
            }
        }
        
        // --- If we have a single recipient that is only an email address return
        // --- it as a string otherwise return the whole array
        return (count($recipients) === 1 && array_key_first($recipients) === 0) ? $recipients[0] : $recipients;
    }

    /**
     * Set the recipients for 'to','cc','bcc' to the sendgrid mail object
     * @return $this
     */
    public function setRecipientsFromPersonalizations($recipientAddressInfo, $personalizationType)
    {
        $functionCall = "add" . ucfirst($personalizationType);
        $pluralFunctionCall = "add" . ucfirst($personalizationType).'s';
        if(is_array($recipientAddressInfo)){
            if(count($recipientAddressInfo) === 1){
                if(array_key_first($recipientAddressInfo) !== 0){
                    $this->getSendGridMail()->$functionCall(array_key_first($recipientAddressInfo), $recipientAddressInfo[array_key_first($recipientAddressInfo)]);
                } else {
                    $this->getSendGridMail()->$functionCall($recipientAddressInfo[0]);
                }
                
            } else {
                $this->getSendGridMail()->$pluralFunctionCall($recipientAddressInfo);
            }
        } else {
            $this->getSendGridMail()->$functionCall($recipientAddressInfo);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTo()
    {
        // $recipients = [];
        // foreach($this->getSendGridMail()->getPersonalizations() as $personalization){
        //     $tos = $personalization->getTos();
        //     foreach($tos as $to){
        //         $name = $to->getName();
        //         $email = $to->getEmail();
        //         // --- If email/name combo set as such otherwise just add email value
        //         if(!empty($name)){
        //             $recipients[$email] = $name;
        //         } else {
        //             $recipients[] = $email;
        //         }
        //     }
        // }
        
        // // --- If we have a single recipient that is only an email address return
        // // --- it as a string otherwise return the whole array
        // return (count($recipients) === 1 && array_key_first($recipients) === 0) ? $recipients[0] : $recipients;
        return $this->getRecipientsFromPersonalizations('to');
    }

    /**
     * {@inheritdoc}
     */
    public function setTo($to)
    {
        // if(is_array($to)){
        //     if(count($to) === 1){
        //         $this->getSendGridMail()->addTo(array_key_first($to), $to[array_key_first($to)]);
        //     } else {
        //         $this->getSendGridMail()->addTos($to);
        //     }
        // } else {
        //     $this->getSendGridMail()->addTo($to);
        // }
        // return $this;
        return $this->setRecipientsFromPersonalizations($to, 'to');
    }

    /**
     * {@inheritdoc}
     */
    public function getCc()
    {
        return $this->getRecipientsFromPersonalizations('cc');
    }

    /**
     * {@inheritdoc}
     */
    public function setCc($cc)
    {
        // if(is_array($cc)){
        //     if(count($cc) === 1){
        //         $this->getSendGridMail()->addCc($cc[0], $cc[1]);
        //     } else {
        //         $this->getSendGridMail()->addCcs($cc);
        //     }
        // } else {
        //     $this->getSendGridMail()->addCcs($cc);
        // }
        // return $this;
        return $this->setRecipientsFromPersonalizations($cc, 'cc');
    }

    /**
     * {@inheritdoc}
     */
    public function getBcc()
    {
        return $this->getRecipientsFromPersonalizations('cc');
    }

    /**
     * {@inheritdoc}
     */
    public function setBcc($bcc)
    {
        return $this->setRecipientsFromPersonalizations($bcc, 'cc');
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->getSendGridMail()->getGlobalSubject()->getSubject();
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject($subject)
    {
        $this->getSendGridMail()->setSubject($subject);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setTextBody($text)
    {
        $this->getSendGridMail()->addContent("text/plain", $text);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setHtmlBody($html)
    {
        $this->getSendGridMail()->addContent("text/html", $html);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function attach($fileName, array $options = [])
    {
        throw new NotSupportedException('attach() has not been implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function attachContent($content, array $options = [])
    {
        throw new NotSupportedException('attachContent() has not been implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function embed($fileName, array $options = [])
    {
        throw new NotSupportedException('embed() has not been implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function embedContent($content, array $options = [])
    {
        throw new NotSupportedException('embedContent() has not been implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return json_encode($this->getSendGridMail()->jsonSerialize(), JSON_PRETTY_PRINT);
    }
}
