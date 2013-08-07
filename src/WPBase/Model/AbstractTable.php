<?php

namespace WPBase\Model;

use Zend\Db\TableGateway\Exception\InvalidArgumentException;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

abstract class AbstractTable extends AbstractTableGateway
{

    protected $table;

    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter, ResultSet $resultSet, Mapping $mapping)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = $resultSet;
        $this->resultSetPrototype->setArrayObjectPrototype($mapping);
        $this->initialize();
    }

    /**
     * @return ResultSet
     */
    public function fetchAll()
    {
        return $this->select();
    }

    /**
     * @param int    $pageNumber
     * @param int    $countPerPage
     * @param string $order
     *
     * @return Paginator
     */
    public function fetchAllPaginator($pageNumber = 1, $countPerPage = 2, $order = 'id ASC')
    {
        //Definindo novo select
        $select = new Select();
        $select->from($this->table)
            ->order($order);

        //Configurando o novo select
        $statement = $this->adapter->createStatement();
        $select->prepareStatement($this->adapter, $statement);
        $resultSet = new ResultSet;
        $result = $resultSet->initialize($statement->execute());

        //Configurando a paginação
        $adapter = new DbSelect($select, $this->adapter, $result);
        $paginator = new Paginator($adapter);
        $paginator->setCurrentPageNumber($pageNumber);
        $paginator->setItemCountPerPage($countPerPage);

        return $paginator;
    }

    /**
     * @param array $param
     *
     * @return array|\ArrayObject|bool|null
     * @throws \Zend\Db\TableGateway\Exception\InvalidArgumentException
     */
    public function findBy(Array $param = array())
    {

        if (empty($param))
            throw new InvalidArgumentException('Expected parameter of type Array');

        $row = $this->select($param)->current();

        if ($row)
            return $row;
        else
            return false;
    }

    /**
     * @param $class
     *
     * @return bool
     * @throws \Zend\Db\TableGateway\Exception\InvalidArgumentException
     */
    public function save($class)
    {
        if (! is_object($class))
            throw new InvalidArgumentException('Expected parameter of type Object');

        $id = (int) $class->getId();

        if ($id == 0 && $this->insert($class->toArray()))
           return true;

        $row = $this->select(array('id' => $id))->current();

        if ($row && $this->update($class->toArray(), array('id' => $row->getId())))
            return true;

        return false;
    }

    /**
     * @param array $param
     *
     * @return bool
     * @throws \Zend\Db\TableGateway\Exception\InvalidArgumentException
     */
    public function remove(Array $param = array())
    {
        if (! empty($param)) {
            if($this->delete($param))
                return true;
            else
                return false;
        } else {
            throw new InvalidArgumentException('Expected parameter of type Array');
        }

    }


}
