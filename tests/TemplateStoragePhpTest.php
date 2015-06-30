<?php

namespace yii2tech\tests\unit\activemail;

use Yii;
use yii\helpers\FileHelper;
use yii2tech\activemail\TemplateStoragePhp;

/**
 * Unit test for [[TemplateStoragePhp]]
 * @see TemplateStoragePhp
 */
class TemplateStoragePhpTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $testFilePath = $this->getTestFilePath();
        FileHelper::createDirectory($testFilePath);
    }

    protected function tearDown()
    {
        $testFilePath = $this->getTestFilePath();
        FileHelper::removeDirectory($testFilePath);

        parent::tearDown();
    }

    /**
     * Returns the test file path.
     * @return string test file path.
     */
    protected function getTestFilePath()
    {
        return Yii::getAlias('@yii2tech/tests/unit/activemail/runtime') . DIRECTORY_SEPARATOR . getmypid();
    }

    // Tests :

    public function testGetTemplate()
    {
        $testFilePath = $this->getTestFilePath();

        $templateData = [
            'subject' => 'test subject',
            'htmlBody' => 'test html body',
        ];
        $content = '<?php return ' . var_export($templateData, true) . ';';
        $templateName = 'testTemplate';
        file_put_contents("{$testFilePath}/{$templateName}.php", $content);

        $storage = new TemplateStoragePhp();
        $storage->templatePath = $testFilePath;

        $template = $storage->getTemplate($templateName);
        $this->assertEquals($templateData, $template);

        $this->assertNull($storage->getTemplate('unexistingTemplate'));
    }
} 