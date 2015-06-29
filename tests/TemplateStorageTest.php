<?php

namespace yii2tech\tests\unit\activemail;

use yii2tech\activemail\TemplateStorage;

/**
 * Unit test for [[TemplateStorage]]
 * @see TemplateStorage
 */
class TemplateStorageTest extends TestCase
{
    public function testGetTemplate()
    {
        $storage = new TestTemplateStorage();

        $templateData = $storage->getTemplate('existing');
        $this->assertNotEmpty($templateData, 'Unable to get existing template');

        $templateData = $storage->getTemplate('notExisting');
        $this->assertEmpty($templateData, 'Unable to get not existing template');
    }

    /**
     * @depends testGetTemplate
     */
    public function testGetTemplateCached()
    {
        $storage = new TestTemplateStorage();

        $templateName = 'existing';

        $templateData = $storage->getTemplate($templateName);
        $this->assertNotEmpty($templateData, 'Unable to get existing template!');

        $templateDataCached = $storage->getTemplate($templateName);
        $this->assertEquals($templateData, $templateDataCached, 'Template not cached!');

        $templateDataRefreshed = $storage->getTemplate($templateName, true);
        $this->assertNotEquals($templateData, $templateDataRefreshed, 'Template not refreshed!');
    }
}

/**
 * Mock up for TemplateStorage
 */
class TestTemplateStorage extends TemplateStorage
{
    const EXISTING_TEMPLATE_NAME = 'existing';

    /**
     * @inheritdoc
     */
    protected function findTemplate($name)
    {
        if ($name == self::EXISTING_TEMPLATE_NAME) {
            return [
                'subject' => 'Test subject ' . uniqid(),
                'body' => 'Test body' . uniqid(),
            ];
        } else {
            return null;
        }
    }
}