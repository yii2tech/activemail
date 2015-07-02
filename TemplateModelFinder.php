<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace yii2tech\activemail;

use yii\base\Component;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\helpers\FileHelper;

/**
 * TemplateModelFinder allows finding active messages and template models.
 * It could be useful while creating administration panel for the mail template management.
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
     * @var TemplateStorage|string|array the mail template storage object or the application component ID.
     * After the TemplateModelFinder object is created, if you want to change this property, you should only assign it
     * with an object.
     */
    public $mailTemplateStorage = 'mailTemplateStorage';
    /**
     * @var string name of the ActiveRecord class, which should be used for template finding.
     * This class should match [[\yii\db\ActiveRecordInterface]] interface.
     * If not set and [[mailTemplateStorage]] refers to [[TemplateStorageActiveRecord]], this value as well as
     * [[templateDataAttributes]] and [[templateNameAttribute]] will be copied from the storage instance.
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
    public function init()
    {
        parent::init();
        $this->mailTemplateStorage = Instance::ensure($this->mailTemplateStorage, TemplateStorage::className());

        if ($this->mailTemplateStorage instanceof TemplateStorageActiveRecord) {
            if ($this->activeRecordClass === null) {
                $this->activeRecordClass = $this->mailTemplateStorage->activeRecordClass;
                $this->templateDataAttributes = $this->mailTemplateStorage->templateDataAttributes;
                $this->templateNameAttribute = $this->mailTemplateStorage->templateNameAttribute;
            }
        }
    }

    /**
     * Finds all active messages available for this application.
     * @return ActiveMessage[] list of active messages.
     */
    public function findAllActiveMessages()
    {
        $activeMessageNamespace = trim($this->activeMessageNamespace, '\\');
        if (empty($this->activeMessageFilePath)) {
            $activeMessageFilePath = Yii::getAlias('@' . str_replace('\\', DIRECTORY_SEPARATOR, $this->activeMessageNamespace));
        } else {
            $activeMessageFilePath = Yii::getAlias($this->activeMessageFilePath);
        }

        $files = FileHelper::findFiles($activeMessageFilePath, ['only' => ['*.php']]);
        $activeMessages = [];
        foreach ($files as $file) {
            $className = $activeMessageNamespace . '\\' . basename($file, '.php');
            try {
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

    /**
     * Finds active message by template name.
     * @param string $templateName template name.
     * @return ActiveMessage|null active message instance.
     */
    public function findActiveMessage($templateName)
    {
        $activeMessages = $this->findAllActiveMessages();
        foreach ($activeMessages as $activeMessage) {
            if ($activeMessage->templateName() === $templateName) {
                return $activeMessage;
            }
        }
        return null;
    }

    /**
     * Finds template models for all available template names.
     * If existing model not found, new one will be created.
     * @return \yii\db\ActiveRecordInterface[] list of template models.
     * @throws InvalidConfigException on invalid configuration.
     */
    public function findAllTemplateModels()
    {
        $activeMessages = $this->findAllActiveMessages();
        if (empty($activeMessages)) {
            return [];
        }

        $existingMailTemplateModels = $this->createQuery()->all();

        return $this->combineActiveMessagesWithTemplates($activeMessages, $existingMailTemplateModels);
    }

    /**
     * Finds template model for specified template name.
     * If existing model not found, new one will be created.
     * @param string $templateName mail template name.
     * @return \yii\db\ActiveRecordInterface|null template model instance.
     */
    public function findTemplateModel($templateName)
    {
        $activeMessage = $this->findActiveMessage($templateName);
        if (!is_object($activeMessage)) {
            return null;
        }
        $existingMailTemplateModels = $this->createQuery()->where([$this->templateNameAttribute => $templateName])->all();
        $models = $this->combineActiveMessagesWithTemplates([$activeMessage], $existingMailTemplateModels);
        return array_shift($models);
    }

    /**
     * Creates ActiveQuery for [[activeRecordClass]].
     * @return \yii\db\ActiveQueryInterface active query instance.
     * @throws InvalidConfigException on invalid configuration.
     */
    protected function createQuery()
    {
        /* @var $activeRecordClass \yii\db\ActiveRecordInterface */
        $activeRecordClass = $this->activeRecordClass;
        if (empty($activeRecordClass)) {
            throw new InvalidConfigException('"' . get_class($this) . '::activeRecordClass" should be specified.');
        }
        return $activeRecordClass::find();
    }

    /**
     * Combines active messages with template models, ensuring template model for each active message.
     * @param ActiveMessage[] $activeMessages active messages.
     * @param \yii\db\ActiveRecordInterface[] $templateModels existing template models.
     * @return \yii\db\ActiveRecordInterface[] list of template models.
     * @throws InvalidConfigException on invalid configuration.
     */
    protected function combineActiveMessagesWithTemplates(array $activeMessages, array $templateModels)
    {
        $templateNameAttribute = $this->templateNameAttribute;
        if (empty($templateNameAttribute)) {
            throw new InvalidConfigException('"' . get_class($this) . '::templateNameAttribute" should be specified.');
        }
        $result = [];
        foreach ($activeMessages as $activeMessage) {
            $matchFound = false;
            foreach ($templateModels as $existingTemplateModelKey => $existingTemplateModel) {
                if ($existingTemplateModel->{$templateNameAttribute} == $activeMessage->templateName()) {
                    $result[] = $existingTemplateModel;
                    unset($templateModels[$existingTemplateModelKey]);
                    $matchFound = true;
                    break;
                }
            }
            if (!$matchFound) {
                $newTemplateModel = new $this->activeRecordClass();
                foreach ($this->templateDataAttributes as $attribute) {
                    $newTemplateModel->$attribute = $activeMessage->$attribute;
                }
                $result[] = $newTemplateModel;
            }
        }
        return $result;
    }
}