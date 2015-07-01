<?php

namespace yii2tech\tests\unit\activemail;

use yii\db\Connection;
use yii2tech\activemail\ActiveMessage;
use yii2tech\activemail\TemplateModelFinder;
use yii2tech\tests\unit\activemail\data\EmailTemplateActiveRecord;

class TemplateModelFinderTest extends TestCase
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
            'name' => 'TestActiveMessage',
            'subject' => 'test subject',
            'bodyHtml' => 'test body HTML',
        ];
        $db->createCommand()->insert($table, $columns)->execute();
    }

    /**
     * @return TemplateModelFinder finder instance.
     */
    protected function createTemplateModelFinder()
    {
        $finder = new TemplateModelFinder();
        $finder->activeMessageNamespace = __NAMESPACE__ . '\\data';
        $finder->activeRecordClass = EmailTemplateActiveRecord::className();
        return $finder;
    }

    // Tests :

    public function testFindAllActiveMessages()
    {
        $finder = $this->createTemplateModelFinder();

        $activeMessages = $finder->findAllActiveMessages();
        $this->assertCount(2, $activeMessages);
        $this->assertTrue($activeMessages[0] instanceof ActiveMessage);
    }

    /**
     * @depends testFindAllActiveMessages
     */
    public function testFindActiveMessage()
    {
        $finder = $this->createTemplateModelFinder();

        $activeMessage = $finder->findActiveMessage('TestActiveMessage');
        $this->assertTrue($activeMessage instanceof ActiveMessage);

        $activeMessage = $finder->findActiveMessage('unExistingActiveMessage');
        $this->assertNull($activeMessage);
    }

    /**
     * @depends testFindAllActiveMessages
     */
    public function testFindAllTemplateModels()
    {
        $finder = $this->createTemplateModelFinder();

        $models = $finder->findAllTemplateModels();

        $this->assertCount(2, $models);
        $this->assertTrue($models[0] instanceof EmailTemplateActiveRecord);

        $newRecordCount = 0;
        foreach ($models as $model) {
            if ($model->isNewRecord) {
                $newRecordCount++;
            }
        }
        $this->assertEquals(1, $newRecordCount);
    }

    /**
     * @depends testFindAllActiveMessages
     */
    public function testFindTemplateModel()
    {
        $finder = $this->createTemplateModelFinder();

        $model = $finder->findTemplateModel('TestActiveMessage');
        $this->assertTrue($model instanceof EmailTemplateActiveRecord);
        $this->assertFalse($model->isNewRecord);

        $model = $finder->findTemplateModel('ContactActiveMessage');
        $this->assertTrue($model instanceof EmailTemplateActiveRecord);
        $this->assertTrue($model->isNewRecord);

        $model = $finder->findTemplateModel('unExistingActiveMessage');
        $this->assertNull($model);
    }
}