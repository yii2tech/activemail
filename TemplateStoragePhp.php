<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace yii2tech\activemail;

use Yii;

/**
 * TemplateStoragePhp
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class TemplateStoragePhp extends TemplateStorage
{
    /**
     * @var string template file path.
     * If not set - "application.mail.template" will be used.
     */
    private $_templatePath;

    /**
     * @param string $templatePath
     */
    public function setTemplatePath($templatePath)
    {
        $this->_templatePath = $templatePath;
    }

    /**
     * @return string
     */
    public function getTemplatePath()
    {
        if ($this->_templatePath === null) {
            $this->_templatePath = $this->defaultTemplatePath();
        }
        return $this->_templatePath;
    }

    /**
     * @return string default template file path.
     */
    protected function defaultTemplatePath()
    {
        return Yii::getPathOfAlias('application.mail.templates');
    }

    /**
     * @inheritdoc
     */
    protected function findTemplate($name)
    {
        $templateFile = $this->getTemplatePath() . DIRECTORY_SEPARATOR . $name . '.php';
        if (file_exists($templateFile)) {
            return $templateFile;
        }
        return null;
    }
} 