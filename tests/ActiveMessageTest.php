<?php

namespace yii2tech\tests\unit\activemail;

use Yii;
use yii2tech\activemail\ActiveMessage;
use yii2tech\tests\unit\activemail\data\TestActiveMessage;

/**
 * Unit test for {@link ActiveMessage}
 * @see ActiveMessage
 */
class ActiveMessageTest extends TestCase
{
    public function testSetGet()
    {
        $message = new TestActiveMessage();

        $subject = 'Test subject';
        $message->setSubject($subject);
        $this->assertEquals($subject, $message->getSubject(), 'Unable to setup subject!');

        $bodyText = 'Test body text';
        $message->setBodyText($bodyText);
        $this->assertEquals($bodyText, $message->getBodyText(), 'Unable tp setup text body!');

        $bodyHtml = 'Test <b>body</b> HTML';
        $message->setBodyHtml($bodyHtml);
        $this->assertEquals($bodyHtml, $message->getBodyHtml(), 'Unable to setup HTML body!');
    }

    public function testDefaults()
    {
        $message = new TestActiveMessage();

        $this->assertNotEmpty($message->getSubject(), 'Unable to get default subject!');
        $this->assertNotEmpty($message->getBodyText(), 'Unable to get default text body!');
        $this->assertNotEmpty($message->getBodyHtml(), 'Unable to get default HTML body!');
    }

    /**
     * @depends testSetGet
     */
    public function testSend()
    {
        $message = new TestActiveMessage();
        $message->send();

        $subject = $message->getSubject();
        $this->assertContains('Template', $subject, 'Unable to assign subject from template!');
        $this->assertNotContains('{', $subject, 'Unable to parse subject!');
        $bodyHtml = $message->getBodyHtml();
        $this->assertContains('Template', $bodyHtml, 'Unable to assign body from template!');
        $this->assertNotContains('{', $bodyHtml, 'Unable to parse body!');
    }

    public function testSerialize()
    {
        $message = new TestActiveMessage();

        $serializedMessage = serialize($message);
        $this->assertEquals($message, unserialize($serializedMessage), 'Unable to serialize/unserialize message!');
    }
}