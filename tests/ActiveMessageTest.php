<?php

namespace yii2tech\tests\unit\activemail;

use Yii;
use yii2tech\activemail\ActiveMessage;
use yii2tech\activemail\template\BaseStorage;

/**
 * Unit test for {@link ActiveMessage}
 * @see ActiveMessage
 */
class ActiveMessageTest extends TestCase
{
    /**
     * @var array backup for application components
     */
    protected $componentsBackup = array();

    public function setUp()
    {
        foreach (array('mail', 'mailTemplateStorage') as $componentName) {
            if (Yii::app()->hasComponent($componentName)) {
                $this->componentsBackup[$componentName] = Yii::app()->getComponent($componentName, false);
            }
        }
        Yii::app()->setComponent('mail', $this->createTestMailComponent(), false);
        Yii::app()->setComponent('mailTemplateStorage', $this->createTestMailTemplateStorageComponent(), false);
    }

    public function tearDown()
    {
        foreach ($this->componentsBackup as $name => $component) {
            Yii::app()->setComponent($name, $component, false);
        }
    }

    /**
     * @return Mailer test mail component instance.
     */
    protected function createTestMailComponent()
    {
        $component = new Mailer();
        $component->view = new TestMailView();
        return $component;
    }

    /**
     * @return BaseStorage test mail template storage component instance.
     */
    protected function createTestMailTemplateStorageComponent()
    {
        $component = new TestMailTemplateStorage();
        return $component;
    }

    // Tests :

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

class TestActiveMessage extends ActiveMessage
{
    public $userId = 10;

    public function defaultSubject()
    {
        return 'Test default subject';
    }

    public function defaultBodyHtml()
    {
        return 'Test default body HTML';
    }

    protected function composeTemplateData()
    {
        return array(
            'subjectPlaceholder' => 'subjectParsed',
            'bodyPlaceholder' => 'bodyParsed',
        );
    }

    public function defaultFrom()
    {
        return 'noreply@testdomain.com';
    }

    public function defaultTo()
    {
        return 'noreply@testdomain.com';
    }
}

class TestMailTemplateStorage extends BaseStorage
{
    protected function findTemplate($name)
    {
        if ($name == 'TestActiveMessage') {
            return array(
                'subject' => 'Template subject {subjectPlaceholder}',
                'bodyHtml' => 'Template body {bodyPlaceholder}',
            );
        } else {
            return null;
        }
    }
}

class TestMailView extends View
{
    public function render($view, $data = null, $return = false)
    {
        return var_export($data, true);
    }
}