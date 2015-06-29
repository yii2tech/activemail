<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace yii2tech\activemail;

use Yii;

/**
 * TemplateStorageDb
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class TemplateStorageDb extends TemplateStorage
{
    /**
     * @var string|CDbConnection database connection component.
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
    public $templateDataFields = array('subject', 'bodyHtml');
    /**
     * @var string name of the mail template table field, which stores the template name.
     */
    public $templateNameField = 'name';

    /**
     * @return CDbConnection database connection instance.
     */
    public function getDbConnection()
    {
        if (is_object($this->db)) {
            return $this->db;
        } else {
            return Yii::app()->getComponent($this->db);
        }
    }

    /**
     * @inheritdoc
     */
    protected function findTemplate($name)
    {
        $template = $this->getDbConnection()->createCommand()
            ->select($this->templateDataFields)
            ->from($this->templateTable)
            ->where($this->templateNameField . ' = :templateName', array('templateName' => $name))
            ->queryRow();
        return $template;
    }
}