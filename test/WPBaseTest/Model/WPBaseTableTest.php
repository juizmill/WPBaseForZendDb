<?php
namespace WPBaseTest\Model;

use PHPUnit_Framework_TestCase;
use WPBase\Model\Mapping;
use WPBase\Model\PositionTable;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class WPBaseTableTest extends PHPUnit_Framework_TestCase
{

    protected $adapter;
    protected $object = 'WPBase\Model\PositionTable';

    public function setUp()
    {

        $adapter = new Adapter(array(
            'driver' => 'Pdo',
            'dsn'    => 'sqlite::memory:'

        ));

        $adapter->driver->getConnection()->execute(
            '
                            CREATE TABLE IF NOT EXISTS position (
                                id INTEGER PRIMARY KEY,
                                title TEXT NOT NULL,
                                description TEXT NOT NULL,
                                place TEXT NOT NULL
                            );
                        '
        );

        $this->adapter = $adapter;
    }


    protected function tearDown()
    {
        $this->adapter->driver->getConnection()->execute('DROP TABLE position');
    }


    public function testClasseExite()
    {
        $this->assertTrue(class_exists($this->object));
    }

    public function testAtributos()
    {

        $this->assertClassHasAttribute('table', $this->object);
    }

    public function testVerificaSeExistesOsMetodosEsperados()
    {

        $this->assertTrue(method_exists($this->object, '__construct'));
        $this->assertTrue(method_exists($this->object, 'fetchAll'));
        $this->assertTrue(method_exists($this->object, 'fetchAllPaginator'));
        $this->assertTrue(method_exists($this->object, 'findBy'));
        $this->assertTrue(method_exists($this->object, 'save'));
        $this->assertTrue(method_exists($this->object, 'remove'));
    }


    public function test_verifica_se_esta_inserindo_no_banco_de_dados()
    {
        $data = array(
            'title'       => 'Title test',
            'description' => 'Description test',
            'place'       => 'Place teste'
        );

        $mapping = new Mapping();
        $mapping->exchangeArray($data);

        $insert = $this->getEntity()->save($mapping);

        $this->assertTrue($insert);

    }

    public function test_verifica_se_esta_alterando_no_banco_de_dados()
    {
        $data = array(
            'title'       => 'Title test',
            'description' => 'Description test',
            'place'       => 'Place teste'
        );

        $data2 = array(
            'id'          => 1,
            'title'       => 'Title test 2',
            'description' => 'Description test 2',
            'place'       => 'Place teste 2'
        );

        $mapping1 = new Mapping();
        $mapping1->exchangeArray($data);

        $mapping2 = new Mapping();
        $mapping2->exchangeArray($data2);

        $this->getEntity()->save($mapping1);
        $update = $this->getEntity()->save($mapping2);

        $this->assertTrue($update);

    }

    public function test_verifica_se_retorna_false_caso_nao_encontre_resitro_para_altera()
    {
        $data = array(
            'id'          => 90,
            'title'       => 'Title test 2',
            'description' => 'Description test 2',
            'place'       => 'Place teste 2'
        );

        $mapping = new Mapping();
        $mapping->exchangeArray($data);

        $update = $this->getEntity()->save($mapping);

        $this->assertFalse($update);

    }

    public function test_verifica_se_deleta_um_registro()
    {

        $data = array(
            'title'       => 'Title test',
            'description' => 'Description test',
            'place'       => 'Place teste'
        );


        $mapping = new Mapping();
        $mapping->exchangeArray($data);

        $this->getEntity()->save($mapping);
        $delete = $this->getEntity()->remove(array('id' => 1));

        $this->assertTrue($delete);

    }

    public function test_verifica_se_retorna_false_caso_nao_poder_deletar()
    {

        $data = array(
            'title'       => 'Title test',
            'description' => 'Description test',
            'place'       => 'Place teste'
        );


        $mapping = new Mapping();
        $mapping->exchangeArray($data);

        $this->getEntity()->save($mapping);
        $delete = $this->getEntity()->remove(array('id' => 90));

        $this->assertFalse($delete);

    }

    public function test_verifica_se_retorna_false_caso_nao_encontre_registro()
    {

        $result = $this->getEntity()->findBy(array('id' => 100));

        $this->assertFalse($result);
    }

    public function test_verifica_se_esta_retornando_registro_especifico()
    {

        $data = array(
            'title'       => 'Title test',
            'description' => 'Description test',
            'place'       => 'Place teste'
        );

        $mapping = new Mapping();
        $mapping->exchangeArray($data);

        $this->getEntity()->save($mapping);

        $result = $this->getEntity()->findBy(array('id' => 1));

        $this->assertInstanceOf('WPBase\Model\Mapping', $result);
    }


    public function test_verifica_se_esta_retornando_todos_os_registros()
    {

        $data = array(
            'title'       => 'Title test',
            'description' => 'Description test',
            'place'       => 'Place teste'
        );

        $mapping = new Mapping();
        $mapping->exchangeArray($data);

        $this->getEntity()->save($mapping);

        $result = $this->getEntity()->fetchAll();

        $this->assertInstanceOf('Zend\Db\ResultSet\ResultSet', $result);
    }

    public function test_verifica_se_esta_retornando_paginacao_de_registros()
    {

        $data = array(
            'title'       => 'Title test',
            'description' => 'Description test',
            'place'       => 'Place teste'
        );

        $mapping = new Mapping();
        $mapping->exchangeArray($data);

        $this->getEntity()->save($mapping);

        $result = $this->getEntity()->fetchAllPaginator();

        $this->assertInstanceOf('Zend\Paginator\Paginator', $result);
    }

    /**
     * @expectedException \Zend\Db\TableGateway\Exception\InvalidArgumentException
     * @expectedExceptionMessage Expected parameter of type Array
     */
    public function test_verifica_se_retorna_um_exception_de_findBy()
    {
        $this->getEntity()->findBy();
    }

    /**
     * @expectedException \Zend\Db\TableGateway\Exception\InvalidArgumentException
     * @expectedExceptionMessage Expected parameter of type Object
     */
    public function test_verifica_se_retorna_um_exception_de_save_caso_nao_passe_por_parametro_um_objeto()
    {
        $this->getEntity()->save('Object?');
    }

    /**
     * @expectedException \Zend\Db\TableGateway\Exception\InvalidArgumentException
     * @expectedExceptionMessage Expected parameter of type Array
     */
    public function test_verifica_se_retorna_um_exception_de_remove()
    {
        $this->getEntity()->remove();
    }


    private function getEntity()
    {
        $resultSet = new ResultSet();
        $mapping = new \WPBase\Model\Mapping();
        return new PositionTable($this->adapter, $resultSet, $mapping);
    }


}