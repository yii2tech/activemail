<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace yii2tech\activemail;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\StringHelper;

/**
 * ActiveMessage represents particular mail sending process.
 * It combines the data and the logic for the particular mail content composition and sending.
 *
 * For each mail sending event, which appears in the application, the child class of ActiveMessage
 * should be created:
 *
 * ```php
 * namespace app\mail\active;
 *
 * use yii2tech\activemail\ActiveMessage;
 * use Yii;
 *
 * class ContactUs extends ActiveMessage
 * {
 *     public function defaultFrom()
 *     {
 *         return Yii::$app->params['applicationEmail'];
 *     }
 *
 *     public function defaultTo()
 *     {
 *         return Yii::$app->params->mail['adminEmail'];
 *     }
 *
 *     public function defaultSubject()
 *     {
 *         return 'Contact message on ' . Yii::$app->name;
 *     }
 *
 *     public function defaultBodyHtml()
 *     {
 *         return 'Contact message';
 *     }
 * }
 * ```
 *
 * Once message created and populated it can be sent via [[send()]] method:
 *
 * ```php
 * use app\mail\active\Notification;
 *
 * $message = new Notification();
 * $message->to = 'some@domain.com';
 * $message->message = 'Notification message';
 * $message->send();
 * ```
 *
 * ActiveMessage supports using of the mail templates provided by [[\yii2tech\activemail\TemplateStorage]].
 *
 * @see yii2tech\activemail\TemplateStorage
 *
 * @property string|array $from message sender email address.
 * @property string|array $replyTo the reply-to address.
 * @property array $to message recipients.
 * @property string $subject message subject.
 * @property string $bodyText message plain text content.
 * @property string $bodyHtml message HTML content.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
abstract class ActiveMessage extends Model
{
    /**
     * @event Event an event that is triggered before message is sent.
     */
    const EVENT_BEFORE_SEND = 'beforeSend';

    /**
     * @var string|array message sender email address.
     */
    private $_from;
    /**
     * @var string|array the reply-to address.
     */
    private $_replyTo;
    /**
     * @var array message recipients.
     */
    private $_to;
    /**
     * @var string message subject.
     */
    private $_subject;
    /**
     * @var string message plain text content.
     */
    private $_bodyText;
    /**
     * @var string message HTML content.
     */
    private $_bodyHtml;


    /**
     * @param string|array $from message sender email address.
     */
    public function setFrom($from)
    {
        $this->_from = $from;
    }

    /**
     * @return string|array message sender email address.
     */
    public function getFrom()
    {
        if (empty($this->_from)) {
            $this->_from = $this->defaultFrom();
        }
        return $this->_from;
    }

    /**
     * @param array|string $replyTo the reply-to address.
     */
    public function setReplyTo($replyTo)
    {
        $this->_replyTo = $replyTo;
    }

    /**
     * @return array|string the reply-to address.
     */
    public function getReplyTo()
    {
        if (empty($this->_replyTo)) {
            $this->_replyTo = $this->defaultReplyTo();
        }
        return $this->_replyTo;
    }

    /**
     * @param array $to message recipients.
     */
    public function setTo($to)
    {
        $this->_to = $to;
    }

    /**
     * @return array message recipients.
     */
    public function getTo()
    {
        if (empty($this->_to)) {
            $this->_to = $this->defaultTo();
        }
        return $this->_to;
    }

    /**
     * @param string $subject message subject.
     */
    public function setSubject($subject)
    {
        $this->_subject = $subject;
    }

    /**
     * @return string message subject.
     */
    public function getSubject()
    {
        if (empty($this->_subject)) {
            $this->_subject = $this->defaultSubject();
        }
        return $this->_subject;
    }

    /**
     * @param string $bodyHtml message HTML content.
     */
    public function setBodyHtml($bodyHtml)
    {
        $this->_bodyHtml = $bodyHtml;
    }

    /**
     * @return string message HTML content.
     */
    public function getBodyHtml()
    {
        if (empty($this->_bodyHtml)) {
            $this->_bodyHtml = $this->defaultBodyHtml();
        }
        return $this->_bodyHtml;
    }

    /**
     * @param string $bodyText message plain text content.
     */
    public function setBodyText($bodyText)
    {
        $this->_bodyText = $bodyText;
    }

    /**
     * @return string message plain text content.
     */
    public function getBodyText()
    {
        if (empty($this->_bodyText)) {
            $this->_bodyText = $this->defaultBodyText();
        }
        return $this->_bodyText;
    }

    /**
     * @return \yii\mail\MailerInterface mailer instance.
     */
    public function getMailer()
    {
        return Yii::$app->getMailer();
    }

    /**
     * @return TemplateStorage template storage instance.
     */
    public function getTemplateStorage()
    {
        return Yii::$app->get('mailTemplateStorage');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [$this->attributes(), 'required'],
        ];
    }

    /**
     * @return string default sender
     */
    abstract public function defaultFrom();

    /**
     * @return string the default reply-to address.
     */
    public function defaultReplyTo()
    {
        return $this->getFrom();
    }

    /**
     * @return string default receiver email address.
     */
    abstract public function defaultTo();

    /**
     * @return string default message subject
     */
    abstract public function defaultSubject();

    /**
     * @return string default message HTML content.
     */
    abstract public function defaultBodyHtml();

    /**
     * @return string default message plain text content.
     */
    public function defaultBodyText()
    {
        return 'You need email client with HTML support to view this message.';
    }

    /**
     * @return string message view name.
     */
    public function viewName()
    {
        return '@yii2tech/activemail/views/activeMessage.php';
    }

    /**
     * @return string message template name.
     */
    public function templateName()
    {
        return StringHelper::basename(get_class($this));
    }

    /**
     * Returns the hints for template placeholders.
     * Hints are can be used, while composing edit form for the mail template.
     * @return array template placeholder hints in format: placeholderName => hint
     */
    public function templatePlaceholderHints()
    {
        return [];
    }

    /**
     * Returns all this model error messages as single summary string.
     * @param string $glue messages separator.
     * @return string error summary.
     */
    public function getErrorSummary($glue = "\n")
    {
        $errors = $this->getErrors();
        $summaryParts = [];
        foreach ($errors as $attributeErrors) {
            $summaryParts = array_merge($summaryParts, $attributeErrors);
        }
        return implode($glue, $summaryParts);
    }

    /**
     * Parses template string.
     * @param string $template template string.
     * @param array $data parsing data.
     * @return string parsing result.
     */
    protected function parseTemplate($template, array $data = [])
    {
        $replacePairs = [];
        foreach ($data as $name => $value) {
            $replacePairs['{' . $name . '}'] = $value;
        }
        return strtr($template, $replacePairs);
    }

    /**
     * Sends this message
     * @param boolean $runValidation whether to perform validation before sending the message.
     * @return boolean success.
     * @throws InvalidConfigException on failure
     */
    public function send($runValidation = true)
    {
        if ($runValidation && !$this->validate()) {
            throw new InvalidConfigException('Unable to send message: ' . $this->getErrorSummary());
        }
        $data = $this->templatePlaceholders();

        //$this->beforeCompose($mailMessage, $data);

        $this->applyTemplate();
        $this->applyParse($data);

        $data['activeMessage'] = $this;

        $mailMessage = $this->getMailer()
            ->compose($this->viewName(), $data)
            ->setSubject($this->getSubject())
            ->setTo($this->getTo())
            ->setFrom($this->getFrom())
            ->setReplyTo($this->getReplyTo());

        if ($this->beforeSend($mailMessage)) {
            return $this->getMailer()->send($mailMessage);
        } else {
            return false;
        }
    }

    /**
     * Composes placeholders, which should be used to parse template.
     * Those placeholders will also be passed to the mail view, while composition.
     * By default this method returns all current message model attributes.
     * Child classes may override this method to customize template placeholders.
     * @return array template placeholders in format: placeholderName => value.
     */
    protected function templatePlaceholders()
    {
        return $this->getAttributes();
    }

    /**
     * Applies corresponding template to the message if it exist.
     */
    protected function applyTemplate()
    {
        $templateAttributes = $this->getTemplateStorage()->getTemplate($this->templateName());
        if (!empty($templateAttributes)) {
            foreach ($templateAttributes as $name => $value) {
                $setter = 'set' . $name;
                if (method_exists($this, $setter)) {
                    $this->$setter($value);
                } else {
                    $this->$name = $value;
                }
            }
        }
    }

    /**
     * Applies parsing to this message internal fields.
     * @param array $data template parse data.
     */
    protected function applyParse(array $data)
    {
        $propertyNames = [
            'subject',
            'bodyText',
            'bodyHtml',
            'bodyHtml',
        ];
        foreach ($propertyNames as $propertyName) {
            $getter = 'get' . $propertyName;
            $setter = 'set' . $propertyName;
            $content = $this->$getter();
            $content = $this->parseTemplate($content, $data);
            $this->$setter($content);
        }
    }

    // Events :

    /**
     * This method is invoked before mail message sending.
     * The default implementation raises a `beforeSend` event.
     * You may override this method to do preliminary checks or adjustments before sending.
     * Make sure the parent implementation is invoked so that the event can be raised.
     * @param \yii\mail\MessageInterface $mailMessage mail message instance.
     * @return boolean whether message should be sent. Defaults to true.
     * If false is returned, no message sending will be performed.
     */
    protected function beforeSend($mailMessage)
    {
        $event = new ActiveMessageEvent(['mailMessage' => $mailMessage]);
        $this->trigger(self::EVENT_BEFORE_SEND, $event);
        return $event->isValid;
    }
}