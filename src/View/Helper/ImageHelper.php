<?php
namespace ImagePresenter\View\Helper;

use Cake\Core\Plugin;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\View\Helper;

/**
 * Image helper
 */
class ImageHelper extends Helper
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Return the webroot path to the image generated variant if this exist or to the controller if not.
     *
     * @param string $imagePath         Path to the original image file from webroot if absolute, or relative to img/
     * @param string|array $variantName Name of the variant configuration key or options array
     * @param array $options            options
     * @return string
     */
    public function variant($imagePath, $variantName, array $options = [])
    {
        if (!array_key_exists('plugin', $options) || $options['plugin'] !== false) {
            list($plugin, $imagePath) = $this->_View->pluginSplit($imagePath, false);
        }

        $url = false;
        $imagePath = ($imagePath[0] === '/') ? substr($imagePath, 1) : $imagePath;
        
        if (!isset($plugin)) {
            $originalFile = WWW_ROOT . $imagePath;
            $variantFile = dirname($originalFile) . DS . $variantName . DS . basename($originalFile);
            if (is_file($variantFile)) {
                $url = str_replace(WWW_ROOT, '/', $variantFile);
            }
        } else {
            $originalFile = WWW_ROOT . Inflector::underscore($plugin) . DS . $imagePath;
            $variantFile = dirname($originalFile) . DS . $variantName . DS . basename($originalFile);
            
            if (is_file($variantFile)) {
                $url = str_replace(WWW_ROOT, '/', $variantFile);
            } else {
                $originalFile = Plugin::path($plugin) . 'webroot' . DS . $imagePath;
                $variantFile = dirname($originalFile) . DS . $variantName . DS . basename($originalFile);
                if (is_file($variantFile)) {
                    $url = str_replace(
                        Plugin::path($plugin) . 'webroot' . DS,
                        '/' . Inflector::underscore($plugin) . '/',
                        $variantFile
                    );
                }
            }
        }
        
        if ($url === false) {
            $url = [
                'controller' => 'Presenter',
                'action' => 'variant',
                'plugin' => 'ImagePresenter',
                'prefix' => false,
                '?' => [
                    'image' => isset($plugin) ? "{$plugin}.{$imagePath}" : $imagePath,
                    'variant' => $variantName
                ]
            ];
        }
        
        return Router::url($url);
    }
}
