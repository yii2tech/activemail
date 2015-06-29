<?php

namespace yii2tech\tests\unit\activemail;

use yii\helpers\ArrayHelper;
use Yii;
use yii2tech\tests\unit\activemail\data\Mailer;
use yii2tech\tests\unit\activemail\data\TemplateStorage;
use yii2tech\tests\unit\activemail\data\View;

/**
 * Base class for the 'ActiveMail' test cases.
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->mockApplication();
    }

    protected function tearDown()
    {
        $this->destroyApplication();
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    protected function mockApplication($config = [], $appClass = '\yii\console\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => $this->getVendorPath(),
            'components' => [
                'mailer' => $this->createTestMailComponent(),
                'mailTemplateStorage' => $this->createTestMailTemplateStorageComponent(),
            ],
        ], $config));
    }

    /**
     * @return string vendor path
     */
    protected function getVendorPath()
    {
        return dirname(__DIR__) . '/vendor';
    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    protected function destroyApplication()
    {
        Yii::$app = null;
    }

    /**
     * @return Mailer test mail component instance.
     */
    protected function createTestMailComponent()
    {
        $component = new Mailer();
        $component->view = new View();
        return $component;
    }

    /**
     * @return TemplateStorage test mail template storage component instance.
     */
    protected function createTestMailTemplateStorageComponent()
    {
        $component = new TemplateStorage();
        return $component;
    }
}
