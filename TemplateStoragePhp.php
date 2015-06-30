<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace yii2tech\activemail;

use Yii;
use yii\base\Exception;

/**
 * TemplateStoragePhp is an active mail template storage based on PHP files.
 * All template files should be stored under [[templatePath]] directory.
 * File name should match the template name.
 * File should return an array with template data, for example:
 *
 * ```php
 * <?php
 * // file 'contact.php'
 * return [
 *     'subject' => 'Contact message',
 *     'htmlBody' => 'Contact inquiry:<br>{message}',
 * ];
 * ```
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class TemplateStoragePhp extends TemplateStorage
{
    /**
     * @var string template file path.
     * By default "@app/mail/templates" is used.
     */
    public $templatePath = '@app/mail/templates';


    /**
     * @inheritdoc
     */
    protected function findTemplate($name)
    {
        $templateFile = Yii::getAlias($this->templatePath) . DIRECTORY_SEPARATOR . $name . '.php';
        if (file_exists($templateFile)) {
            $template = require $templateFile;
            if (!is_array($template)) {
                throw new Exception("Unable to get template from file '{$templateFile}': file should return array, '" . gettype($template) . "' returned instead.");
            }
            return $template;
        }
        return null;
    }
}