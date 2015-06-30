<?php

namespace yii2tech\tests\unit\activemail\data;

use yii2tech\activemail\ActiveMessage;

class TestActiveMessage extends ActiveMessage
{
    public $userId = 10;

    public function defaultSubject()
    {
        return 'Test default subject';
    }

    public function defaultBodyHtml()
    {
        return 'Test default body HTML';
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