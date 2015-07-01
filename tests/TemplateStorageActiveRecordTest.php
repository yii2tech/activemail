<?php

namespace yii2tech\tests\unit\activemail;

use yii\db\Connection;
use yii2tech\activemail\TemplateStorageActiveRecord;
use yii2tech\tests\unit\activemail\data\EmailTemplateActiveRecord;

class TemplateStorageActiveRecordTest extends TestCase
{
    /**
     * @var Connection database connection used for the test running.
     */
    protected $_db;

    public function setUp()
    {
        $this->mockApplication([
            'components' => [
                'db' => $this->getDb()
            ],
        ]);
        $this->createTestTables();
    }

    /**
     * @return Connection test database connection
     */
    protected function getDb()
    {
        if ($this->_db === null) {
            $this->_db = new Connection(['dsn' => 'sqlite::memory:']);
            $this->_db->open();
        }
        return $this->_db;
    }

    /**
     * Creates test database tables.
     */
    protected function createTestTables()
    {
        $db = $this->getDb();

        $table = 'EmailTemplateAr';
        $columns = [
            'id' => 'pk',
            'name' => 'string',
            'subject' => 'string',
            'bodyHtml' => 'text',
        ];
        $db->createCommand()->createTable($table, $columns)->execute();

        $columns = [
            'name' => 'test',
            'subject' => 'test subject',
            'bodyHtml' => 'test body HTML',
        ];
        $db->createCommand()->insert($table, $columns)->execute();
    }

    /**
     * @return TemplateStorageActiveRecord storage instance.
     */
    protected function createTestStorage()
    {
        $storage = new TemplateStorageActiveRecord();
        $storage->activeRecordClass = EmailTemplateActiveRecord::className();
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