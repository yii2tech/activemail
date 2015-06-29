<?php

namespace yii2tech\tests\unit\activemail\data;
use yii\helpers\VarDumper;

/**
 * Test mail view component.
 */
class View extends \yii\web\View
{
    public function render($view, $data = null, $return = false)
    {
        return VarDumper::export($data);
    }
}