<?php
/**
 * Created by javier
 * Date: 17/02/16
 * Time: 08:20
 */

namespace ImagePresenter\Controller;

use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Network\Response;
use ImagePresenter\Exception\MissingConfigurationException;
use ImagePresenter\Exception\MissingParametersException;
use ImagePresenter\Exception\NotImplementedOperationException;
use ImagePresenter\Exception\OriginalFileNotFoundException;
use ImagePresenter\Exception\VariantConfigurationNotFoundException;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Imagick\Imagine;

/**
 * Class PresenterController
 * @package Linked\ImagePresenter\Controller
 */
class PresenterController extends AppController
{
    const OPERATION_THUMBNAIL = 'thumbnail';
    const OPERATION_CLOSURE = 'closure';

    /**
     * @return Response|null
     * @throws NotImplementedOperationException
     */
    public function variant()
    {
        $imagePath = $this->request->query('image');
        $variantName = $this->request->query('variant');
        if (!$imagePath || !$variantName) {
            throw new MissingParametersException(__d('image-presenter', 'Faltan parámetros'));
        }
        if ($imagePath[0] === '/') {
            $originalFile = WWW_ROOT . substr($imagePath, 1);
        } else {
            $originalFile = WWW_ROOT . "img/{$imagePath}";
        }

        if (!is_file($originalFile)) {
            throw new OriginalFileNotFoundException(__d('image-presenter', 'No se encontró el archivo original.'));
        }
        $settings = Configure::read('ImagePresenter');

        if (empty($settings['variants'][$variantName])) {
            throw new VariantConfigurationNotFoundException(
                __d('image-presenter', 'No se encontró la configuración para esta variante {0}', $variantName)
            );
        }
        $variantSettings = $settings['variants'][$variantName];
        $variantFile = dirname($originalFile) . DS . $variantName . DS . basename($originalFile);
        $_folder = new Folder(dirname($variantFile), true);
        
        if (substr($variantName, 0, 9) === self::OPERATION_THUMBNAIL &&
            !array_key_exists('operation', $variantSettings)
        ) {
            $variantSettings['operation'] = self::OPERATION_THUMBNAIL;
        }
        
        if (isset($variantSettings['operation']) && is_callable($variantSettings['operation'])) {
            $variantSettings['closure'] = $variantSettings['operation'];
            $variantSettings['operation'] = self::OPERATION_CLOSURE;
        }
        
        if (empty($variantSettings['operation'])) {
            throw new MissingConfigurationException(
                __d('image-presenter', 'No se configuró la operación a realizar de forma adecuada')
            );
        }
        
        switch ($variantSettings['operation']) {
            case self::OPERATION_THUMBNAIL:
                $imagine = new Imagine();
                $imagine
                    ->open($originalFile)
                    ->thumbnail(
                        $this->extractSizeOption($variantSettings),
                        $this->extractThumbModeOption($variantSettings),
                        $this->extractFilterOption($variantSettings)
                    )
                    ->save($variantFile);
                break;

            case self::OPERATION_CLOSURE:
                $imagine = new Imagine();
                $image = $imagine->open($originalFile);
                $variantSettings['closure']($image);
                $image->save($variantFile);
                break;

            default:
                throw new NotImplementedOperationException(
                    __d('image-presenter', 'La operación: {0} no ha sido implementada', $variantSettings['operation'])
                );
        }
        
        $this->response->file($variantFile);
        return $this->response;
    }

    /**
     * @param array $settings Variant settings
     * @return Box
     */
    protected function extractSizeOption(array $settings)
    {
        if ((!array_key_exists('size', $settings)) || (!is_array($settings['size']) || empty($settings['size']))) {
            throw new MissingConfigurationException(
                __d('image-presenter', 'No se configuró la opción size de forma adecuada')
            );
        }
        $width = intval($settings['size'][0]);
        $height = !empty($settings['size'][1]) ? intval($settings['size'][1]) : $width;

        return new Box($width, $height);
    }

    /**
     * @param array $settings variant settings
     * @return string one of ImageInterface::THUMBNAIL_OUTBOUND, ImageInterface::THUMBNAIL_INSET
     */
    protected function extractThumbModeOption(array $settings)
    {
        if (!array_key_exists('mode', $settings) ||
            !in_array($settings['mode'], [ImageInterface::THUMBNAIL_OUTBOUND, ImageInterface::THUMBNAIL_INSET])
        ) {
            throw new MissingConfigurationException(
                __d('image-presenter', 'No se configuró la opción mode de forma adecuada')
            );
        }

        return $settings['mode'];
    }

    /**
     * @param array $settings variant settings
     * @return string
     */
    protected function extractFilterOption(array $settings)
    {
        $filter = array_key_exists('filter', $settings) ? $settings['filter'] : ImageInterface::FILTER_UNDEFINED;
        if (!in_array($filter, [
                ImageInterface::FILTER_UNDEFINED,
                ImageInterface::FILTER_BESSEL,
                ImageInterface::FILTER_BLACKMAN,
                ImageInterface::FILTER_BOX,
                ImageInterface::FILTER_CATROM,
                ImageInterface::FILTER_CUBIC,
                ImageInterface::FILTER_GAUSSIAN,
                ImageInterface::FILTER_HAMMING,
                ImageInterface::FILTER_HANNING,
                ImageInterface::FILTER_HERMITE,
                ImageInterface::FILTER_LANCZOS,
                ImageInterface::FILTER_MITCHELL,
                ImageInterface::FILTER_POINT,
                ImageInterface::FILTER_QUADRATIC,
                ImageInterface::FILTER_SINC,
                ImageInterface::FILTER_TRIANGLE,
            ])
        ) {
            throw new MissingConfigurationException(
                __d('image-presenter', 'No se configuró la opción filter de forma adecuada')
            );
        }
        
        return $filter;
    }
}
