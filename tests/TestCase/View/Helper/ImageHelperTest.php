<?php
namespace ImagePresenter\Test\TestCase\View\Helper;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use ImagePresenter\View\Helper\ImageHelper;

/**
 * ImagePresenter\View\Helper\ImageHelper Test Case
 * 
 * @property ImageHelper $Image
 */
class ImageHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \ImagePresenter\View\Helper\ImageHelper
     */
    public $Image;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $view = new View();
        $this->Image = new ImageHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Image);

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testVariantWithExistingImage()
    {
        $result = $this->Image->variant('ImagePresenter.img/image-2.jpg', 'thumbnail');
        $this->assertEquals('/image_presenter/img/thumbnail/image-2.jpg', $result);
    }

    /**
     * @return void
     */
    public function testVariantWithoutExistingImage()
    {
        $file = Plugin::path('ImagePresenter') . 'webroot' . DS . 'img' . DS . 'thumbnail' . DS . 'image-1.jpg';
        if (is_file($file)) {
            unlink($file);
        }
        
        $result = $this->Image->variant('ImagePresenter.img/image-1.jpg', 'thumbnail');
        $expected = Router::url([
            'controller' => 'Presenter',
            'action' => 'variant',
            'plugin' => 'ImagePresenter',
            '?' => [
                'image' => 'ImagePresenter.img/image-1.jpg',
                'variant' => 'thumbnail'
            ]
        ]);
        
        $this->assertEquals($expected, $result);
    }
}
