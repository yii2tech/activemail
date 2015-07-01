<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace yii2tech\activemail;

/**
 * TemplateStorageActiveRecord is an active mail template storage based on ActiveRecord.
 * It uses ActiveRecord class for the actual template finding.
 *
 * @see \yii\db\ActiveRecordInterface
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class TemplateStorageActiveRecord extends TemplateStorage
{
    /**
     * @var string name of the ActiveRecord class, which should be used for template finding.
     * This class should match [[\yii\db\ActiveRecordInterface]] interface.
     */
    public $activeRecordClass;
    /**
     * @var array list of ActiveRecord attributes, which should compose the template data.
     * Only these fields will be selected while querying template row.
     * You may adjust fields list according to the actual ActiveRecord class.
     */
    public $templateDataAttributes = ['subject', 'bodyHtml'];
    /**
     * @var string name of the ActiveRecord attribute, which stores the template name.
     */
    public $templateNameAttribute = 'name';


    /**
     * @inheritdoc
     */
    protected function findTemplate($name)
    {
        /* @var $activeRecordClass \yii\db\ActiveRecordInterface */
        $activeRecordClass = $this->activeRecordClass;
        $templateModel = $activeRecordClass::findOne([$this->templateNameAttribute => $name]);

        if (!is_object($templateModel)) {
            return null;
        }
        $template = [];
        foreach ($this->templateDataAttributes as $attribute) {
            $template[$attribute] = $templateModel->$attribute;
        }

        return $template;
    }
} 