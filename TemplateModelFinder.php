<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace yii2tech\activemail;

use yii\base\Component;
use Yii;
use yii\helpers\FileHelper;

/**
 * TemplateModelFinder
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class TemplateModelFinder extends Component
{
    /**
     * @var string namespace which under all active message classes declared.
     */
    public $activeMessageNamespace = 'app\mail\active';
    /**
     * @var string path to directory, which contains active message classes source files.
     * If not set path composed from [[activeMessageNamespace]] will be used.
     */
    public $activeMessageFilePath;

    /**
     * Finds all active messages available for this application.
     * @return ActiveMessage[] list of active messages.
     */
    public function findAllActiveMessages()
    {
        $activeMessageNamespace = trim($this->activeMessageNamespace, '\\');
        if (empty($this->activeMessageFilePath)) {
            $activeMessageFilePath = Yii::getAlias('@' . str_replace('\\', '/', $this->activeMessageNamespace));
        } else {
            $activeMessageFilePath = Yii::getAlias($this->activeMessageFilePath);
        }

        $files = FileHelper::findFiles($activeMessageFilePath, ['only' => ['*.php']]);
        $activeMessages = [];
        foreach ($files as $file) {
            $className = $activeMessageNamespace . '\\' . basename($file, '.php');
            try {
                require_once $file;
                $reflection = new \ReflectionClass($className);
            } catch (\Exception $exception) {
                continue;
            }
            if ($reflection->isAbstract() || !$reflection->isSubclassOf(ActiveMessage::className())) {
                continue;
            }
            $activeMessages[] = $reflection->newInstance();
        }
        return $activeMessages;
    }
}