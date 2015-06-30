<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace yii2tech\activemail;

use Yii;
use yii\db\Connection;
use yii\db\Query;
use yii\di\Instance;

/**
 * TemplateStorageDb is an active mail template storage based on relational database.
 * It stores template data into the database table named [[templateTable]].
 * Migration code for such table creation could be following:
 *
 * ```php
 * $this->createTable('EmailTemplate', [
 *     'name' => 'string',
 *     'subject' => 'string',
 *     'bodyHtml' => 'text',
 *     'PRIMARY KEY (name)',
 * ]);
 * ```
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class TemplateStorageDb extends TemplateStorage
{
    /**
     * @var Connection|array|string the DB connection object or the application component ID of the DB connection.
     * After the TemplateStorageDb object is created, if you want to change this property, you should only assign it
     * with a DB connection object.
     */
    public $db = 'db';
    /**
     * @var string name of the table, which stores email templates.
     */
    public $templateTable = 'EmailTemplate';
    /**
     * @var array list of mail template table fields, which should compose the template data.
     * Only these fields will be selected while querying template row.
     * You may adjust fields list according to the actual table schema.
     */
    public $templateDataFields = ['subject', 'bodyHtml'];
    /**
     * @var string name of the mail template table field, which stores the template name.
     */
    public $templateNameField = 'name';


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
    }

    /**
     * @inheritdoc
     */
    protected function findTemplate($name)
    {
        $query = new Query();
        $template = $query
            ->select($this->templateDataFields)
            ->from($this->templateTable)
            ->where([$this->templateNameField => $name])
            ->one();

        if ($template === false) {
            return null;
        }
        return $template;
    }
}