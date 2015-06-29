<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace yii2tech\activemail;

use yii\base\ModelEvent;

/**
 * ActiveMessageEvent represents the event parameters needed by events raised by an [[ActiveMessage]].
 *
 * @see ActiveMessage
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class ActiveMessageEvent extends ModelEvent
{
    /**
     * @var \yii\mail\MessageInterface mail message instance.
     */
    public $mailMessage;
}