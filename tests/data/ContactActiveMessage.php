<?php

namespace yii2tech\tests\unit\activemail\data;

use yii2tech\activemail\ActiveMessage;

class ContactActiveMessage extends ActiveMessage
{
    public function defaultSubject()
    {
        return 'Test contact subject';
    }

    public function defaultBodyHtml()
    {
        return 'Test contact body HTML';
    }

    protected function templatePlaceholders()
    {
        return array(
            'subjectPlaceholder' => 'subjectParsed',
            'bodyPlaceholder' => 'bodyParsed',
        );
    }

    public function defaultFrom()
    {
        return 'noreply@testdomain.com';
    }

    public function defaultTo()
    {
        return 'noreply@testdomain.com';
    }
} 