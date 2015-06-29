<?php

namespace yii2tech\tests\unit\activemail;

use yii2tech\activemail\TemplateStorageDb;

/**
 * Unit test for {@link StorageDb}
 * @see StorageDb
 */
class TemplateStorageDbTest extends TestCase
{
    /**
     * @var CDbConnection database connection used for the test running.
     */
    protected $_db;

    public function setUp()
    {
        $db = $this->getDbConnection();
        $this->createTestTables($db);
    }

    /**
     * @return CDbConnection test database connection
     */
    protected function getDbConnection()
    {
        if ($this->_db === null) {
            $this->_db = new CDbConnection('sqlite::memory:');
            $this->_db->active = true;
        }
        return $this->_db;
    }

    /**
     * Creates test database tables.
     * @param CDbConnection $db database connection
     */
    protected function createTestTables($db)
    {
        $table = 'EmailTemplate';
        $columns = array(
            'id' => 'pk',
            'name' => 'string',
            'subject' => 'string',
            'bodyHtml' => 'text',
        );
        $db->createCommand()->createTable($table, $columns);

        $columns = array(
            'name' => 'test',
            'subject' => 'test subject',
            'bodyHtml' => 'test body HTML',
        );
        $db->createCommand()->insert($table, $columns);
    }

    /**
     * @return TemplateStorageDb storage instance.
     */
    protected function createTestStorage()
    {
        $storage = new TemplateStorageDb();
        $storage->db = $this->getDbConnection();
        return $storage;
    }

    // Tests :

    public function testGetTemplate()
    {
        $storage = $this->createTestStorage();

        $template = $storage->getTemplate('test');
        $this->assertNotEmpty($template);
    }
} 