<?php
namespace ImagePresenter\View\Helper;

use Cake\Routing\Router;
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
    public function variant($imagePath, $variantName, array $options = null)
    {
        if ($imagePath[0] === '/') {
            $originalFile = WWW_ROOT . substr($imagePath, 1);
        } else {
            $originalFile = WWW_ROOT . "img/{$imagePath}";
        }
        
        $dirname = dirname($originalFile);
        $variantFile = $dirname . DS . $variantName . DS . basename($originalFile);
        
        if (is_file($variantFile)) {
            return str_replace(WWW_ROOT, '/', $variantFile);
        } else {
            return Router::url([
                'controller' => 'Presenter',
                'action' => 'variant',
                'plugin' => 'ImagePresenter',
                '?' => ['image' => $imagePath, 'variant' => $variantName]
            ]);
        }
    }
}
