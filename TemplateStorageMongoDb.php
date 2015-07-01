<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace yii2tech\activemail;

use yii\di\Instance;
use yii\mongodb\Connection;
use yii\mongodb\Query;

/**
 * TemplateStorageMongoDb is an active mail template storage based on MongoDB.
 * It stores template data into the collection named [[templateCollection]].
 *
 * This storage requires [yiisoft/yii2-mongodb](https://github.com/yiisoft/yii2-mongodb) extension installed.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class TemplateStorageMongoDb extends TemplateStorage
{
    /**
     * @var Connection|array|string the MongoDB connection object or the application component ID of the MongoDB connection.
     * After the TemplateStorageMongoDb object is created, if you want to change this property, you should only assign it
     * with a MongoDB connection object.
     */
    public $db = 'mongodb';
    /**
     * @var string|array name of the MongoDB collection, which stores email templates.
     */
    public $templateCollection = 'EmailTemplate';
    /**
     * @var array list of mail template collection fields, which should compose the template data.
     * Only these fields will be selected while querying template row.
     * You may adjust fields list according to the actual collection data.
     */
    public $templateDataFields = ['subject', 'bodyHtml'];
    /**
     * @var string name of the mail template collection field, which stores the template name.
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
            ->from($this->templateCollection)
            ->where([$this->templateNameField => $name])
            ->one();

        if ($template === false) {
            return null;
        }
        return $template;
    }
}