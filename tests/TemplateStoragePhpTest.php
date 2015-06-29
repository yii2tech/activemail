<?php

namespace yii2tech\tests\unit\activemail;

use yii2tech\activemail\TemplateStoragePhp;

/**
 * Unit test for {@link StoragePhp}
 * @see StoragePhp
 */
class TemplateStoragePhpTest extends TestCase
{
    public function testSetGet()
    {
        $storage = new TemplateStoragePhp();

        $templatePath = '/test/template/path';
        $storage->setTemplatePath($templatePath);
        $this->assertEquals($templatePath, $storage->getTemplatePath(), 'Unable to setup template path!');
    }
} 