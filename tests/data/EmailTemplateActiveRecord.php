<?php

namespace yii2tech\tests\unit\activemail\data;

use yii\db\ActiveRecord;

class EmailTemplateActiveRecord extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'EmailTemplateAr';
    }
}