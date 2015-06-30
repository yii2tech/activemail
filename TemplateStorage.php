<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace yii2tech\activemail;

use yii\base\Component;

/**
 * TemplateStorage is a base class for active message template storages.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
abstract class TemplateStorage extends Component
{
    /**
     * @var array found templates cache in format: templateName => templateData
     */
    private $_templates = [];

    /**
     * Returns the template data fro the given name.
     * @param string $name template name.
     * @param boolean $refresh whether to ignore cache.
     * @return array|null template data.
     */
    public function getTemplate($name, $refresh = false)
    {
        if ($refresh || !array_key_exists($name, $this->_templates)) {
            $this->_templates[$name] = $this->findTemplate($name);
        }
        return $this->_templates[$name];
    }

    /**
     * Finds the actual template data in the storage.
     * @param string $name template name.
     * @return array|null template data.
     */
    abstract protected function findTemplate($name);
}