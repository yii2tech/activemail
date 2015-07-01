<?php

namespace yii2tech\tests\unit\activemail;

use yii\mongodb\Connection;
use yii2tech\activemail\TemplateStorageMongoDb;

class TemplateStorageMongoDbTest extends TestCase
{
    /**
     * @var Connection database connection used for the test running.
     */
    protected $_db;

    public function setUp()
    {
        if (!extension_loaded('mongo')) {
            $this->markTestSkipped('mongo PHP extension required.');
        }
        if (!class_exists('yii\mongodb\Connection')) {
            $this->markTestSkipped('"yiisoft/yii2-mongodb" extension required.');
        }

        $this->mockApplication([
            'components' => [
                'mongodb' => $this->getDb()
            ],
        ]);
        $this->setupTestData();
    }

    protected function tearDown()
    {
        $this->getDb()->getCollection('EmailTemplate')->drop();
        parent::tearDown();
    }

    /**
     * @return Connection test database connection
     */
    protected function getDb()
    {
        if ($this->_db === null) {
            $this->_db = new Connection([
                'dsn' => 'mongodb://travis:test@localhost:27017',
                'defaultDatabaseName' => 'yii2test',
                'options' => [],
            ]);
            $this->_db->open();
        }
        return $this->_db;
    }

    /**
     * Sets up test data.
     */
    protected function setupTestData()
    {
        $db = $this->getDb();
        $db->getCollection('EmailTemplate')->insert([
            'name' => 'test',
            'subject' => 'test subject',
            'bodyHtml' => 'test body HTML',
        ]);
    }

    /**
     * @return TemplateStorageMongoDb storage instance.
     */
    protected function createTestStorage()
    {
        $storage = new TemplateStorageMongoDb();
        $storage->db = $this->getDb();
        return $storage;
    }

    // Tests :

    public function testGetTemplate()
    {
        $storage = $this->createTestStorage();

        $template = $storage->getTemplate('test');
        $this->assertNotEmpty($template);

        $this->assertNull($storage->getTemplate('unexistingTemplate'));
    }
} 