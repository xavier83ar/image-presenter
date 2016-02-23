<?php
namespace ImagePresenter\Test\TestCase\Controller;

use Cake\Core\Plugin;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestCase;
use Cake\TestSuite\StringCompareTrait;
use ImagePresenter\Controller\PresenterController;

/**
 * ImagePresenter\Controller\PresenterController Test Case
 */
class PresenterControllerTest extends IntegrationTestCase
{
    use StringCompareTrait;

    /**
     * 
     */
    public function setUp()
    {
        $this->_compareBasePath =  Plugin::path('ImagePresenter') . 'tests' . DS . 'comparisons' . DS;
        parent::setUp();
    }
    
    /**
     * Test variant method
     *
     * @return void
     */
    public function testVariant()
    {
        $pluginPath = Plugin::path('ImagePresenter');
        $fileName = 'image-1.jpg';
        $variant = 'thumbnail';
        $testFile = $pluginPath . 'webroot' . DS . 'img' . DS . $variant . DS . $fileName;
        // borramos el archivo si existe
        if (is_file($testFile)) {
            unlink($testFile);
        }
        
        $url = Router::url([
            'controller' => 'Presenter',
            'action' => 'variant',
            'plugin' => 'ImagePresenter',
            '?' => [
                'image' => "ImagePresenter.img/{$fileName}",
                'variant' => $variant
            ]
        ]);
        $request = $this->_buildRequest($url, 'GET', []);
        // we mock the Response object due to this https://github.com/cakephp/cakephp/issues/7974#issuecomment-187486141
        $response = $this->getMockBuilder('\\Cake\\Network\\Response')->getMock();
        $response->method('_clearBuffer')->willReturn(false);
        
        $controller = new PresenterController($request, $response);
        $result = $controller->variant();
        $this->_response = $result;

        $this->assertFileEquals(
            $pluginPath . 'tests' . DS . 'comparisons' . DS . 'img' . DS . $fileName,
            $pluginPath . 'webroot'. DS . 'img' . DS . $variant . DS . $fileName
        );
        $this->assertContentType('image/jpeg');
    }
}
