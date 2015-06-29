<?php

namespace yii2tech\tests\unit\activemail\data;


class TemplateStorage extends \yii2tech\activemail\TemplateStorage
{
    /**
     * @inheritdoc
     */
    protected function findTemplate($name)
    {
        if ($name === 'TestActiveMessage') {
            return [
                'subject' => 'Template subject {subjectPlaceholder}',
                'bodyHtml' => 'Template body {bodyPlaceholder}',
            ];
        } else {
            return null;
        }
    }
}