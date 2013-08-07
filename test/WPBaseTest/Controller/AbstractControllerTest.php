<?php
namespace WPBaseTest\Controller;

use WPBase\Controller\AbstractController;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

chdir(__DIR__);
class AbstractControllerTest extends AbstractHttpControllerTestCase
{
    protected $object = 'WPBase\\Controller\\AbstractController';

    public function setUp()
    {

        $this->setApplicationConfig( require __DIR__.'/../../TestConfig.php' );
        parent::setUp();
    }

    public function test_verifica_se_classe_existe(){
        $this->assertTrue(class_exists($this->object));
    }

    /**
     * @depends test_verifica_se_classe_existe
     */
    public function test_verifica_se_exite_o_atributo_entity()
    {
        $this->assertClassHasAttribute('entity', $this->object);
    }

    public function test_verifica_se_existe_os_metodos_necessarios()
    {
        $this->assertTrue(method_exists($this->object, 'indexAction'));
        $this->assertTrue(method_exists($this->object, 'addAction'));
        $this->assertTrue(method_exists($this->object, 'editAction'));
        $this->assertTrue(method_exists($this->object, 'removeAction'));
        $this->assertTrue(method_exists($this->object, 'getEntity'));
    }

    public function test_verifica_se_index_esta_acessivel()
    {
        $this->dispatch('/base');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('WPBase');
        $this->assertControllerName('WPBase\Controller\Abstract');
        $this->assertControllerClass('AbstractController');
        $this->assertMatchedRouteName('wp_base');

    }

    public function test_verifica_se_add_esta_acessivel()
    {
        $this->dispatch('/base/add');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('WPBase');
        $this->assertControllerName('WPBase\Controller\Abstract');
        $this->assertControllerClass('AbstractController');
        $this->assertMatchedRouteName('wp_base/crud');

    }

    public function test_verifica_se_edit_esta_acessivel()
    {
        $this->dispatch('/base/edit/1');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('WPBase');
        $this->assertControllerName('WPBase\Controller\Abstract');
        $this->assertControllerClass('AbstractController');
        $this->assertMatchedRouteName('wp_base/crud');

    }

    public function test_verifica_se_remove_esta_acessivel()
    {
        $this->dispatch('/base/remove/1');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('WPBase');
        $this->assertControllerName('WPBase\Controller\Abstract');
        $this->assertControllerClass('AbstractController');
        $this->assertMatchedRouteName('wp_base/crud');

    }

}