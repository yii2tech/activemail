<?php

namespace yii2tech\tests\unit\activemail;

use yii2tech\activemail\ActiveMessage;
use yii2tech\activemail\TemplateModelFinder;

class TemplateModelFinderTest extends TestCase
{
    public function testFindAllActiveMessages()
    {
        $finder = new TemplateModelFinder();
        $finder->activeMessageNamespace = __NAMESPACE__ . '\\data';

        $activeMessages = $finder->findAllActiveMessages();
        $this->assertNotEmpty($activeMessages);
        $this->assertTrue($activeMessages[0] instanceof ActiveMessage);
    }
}