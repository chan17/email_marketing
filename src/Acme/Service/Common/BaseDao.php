<?php
namespace Acme\Service\Common;

// use Topxia\Common\DaoException;

abstract class BaseDao {

    protected $connection;

    protected $primaryKey = 'id';

    public function setConnection($connection) {
        $this->connection = $connection;
    }

    public function getConnection () {
        return $this->connection;
    }

    public function getTable() {
        return $this->table;
    }

    protected function fetch($id) {
        return $this->fetchRow("{$this->primaryKey} = ?", array($id));
    }

    protected function insert ($data) {
        $affected = $this->getConnection()->insert($this->table, $data);
        if ($affected <= 0) {
            throw $this->createDaoException('insert error.');
        }
        $id = $this->getConnection()->lastInsertId();
        return $this->fetch($id);
    }

    protected function update ($id, array $data) {
        if (!empty($data)) {
            $affected = $this->getConnection()->update($this->table, $data, array(
                'id' => $id
            ));
        }
        return $this->fetch($id);
    }

    protected function wave ($id, $fields) {
        $sql = "UPDATE {$this->table} SET ";
        $fieldStmts = array();
        foreach (array_keys($fields) as $field) {
            $fieldStmts[] = "{$field} = {$field} + ? ";
        }
        $sql .= join(',', $fieldStmts);
        $sql .= "WHERE id = ?";

        $params = array_merge(array_values($fields), array($id));
        return $this->getConnection()->executeUpdate($sql, $params);
    }

    protected function delete ($id) {
        return $this->getConnection()->delete($this->table, array(
            'id' => $id
        ));
    }

    protected function deleteAll ($where = null, array $params = array()) {
        $sql = 'DELETE FROM ' . $this->table;
        $sql .= !empty($where) ? ' WHERE ' . $where : '';
        return $this->getConnection()->executeUpdate($sql, $params);
    }

    //取一条数据
    protected function fetchRow ($where = null, array $params = array(), $fields = null, $orderBy = null, $start = 0, $limit = null) {
        $sql = $this->buildFetchSql($this->table, $fields, $where, $orderBy, $start, $limit);
        $result = $this->getConnection()->fetchAssoc($sql, $params);
        return empty($result) ? null : $result;
    }

    protected function fetchAll ($where = null, array $params = array(), $fields = null, $orderBy = null, $start = 0, $limit = null) {
        $sql = $this->buildFetchSql($this->table, $fields, $where, $orderBy, $start, $limit);
        $result = $this->getConnection()->fetchAll($sql, $params);
        return empty($result) ? array() : $result;
    }

    //取一列数据
    protected function fetchColumn ($where = null, array $params = array(), $fields = null, $orderBy = null, $start = 0, $limit = null) {
        $sql = $this->buildFetchSql($this->table, $fields, $where, $orderBy, $start, $limit);
        return $this->getConnection()->fetchColumn($sql, $params);
    }

    protected function fetchIn($field, array $values, $ordered = false)
    {
        if (empty ($values)) {
            return array();
        }

        $values = array_values($values);
        $marks = str_repeat('?,', count($values) - 1) . '?';

        if ($ordered) {
            return $this->fetchAll("{$field} IN ({$marks})", array_merge($values, $values), null, "FIELD ({$field}, {$marks})");
        }

        return $this->fetchAll("{$field} IN ({$marks})", $values);
    }

    protected function count ($where = null, array $params = array(), $countField = null) {
        $sql = 'SELECT ';
        $sql .= empty($countField) ? 'COUNT(*) ' : "COUNT({$countField}) ";
        $sql .= "FROM {$this->table} ";
        $sql .= empty($where) ? '' : " WHERE {$where} ";
        return (int) $this->getConnection()->fetchColumn($sql, $params);
    }

    protected function buildFetchSql ($table, $fields, $where, $orderBy = null, $start = 0, $limit = null) {
        if (!empty($limit)) {
            $start = (int) $start;
            if ($start < 0) {
                $start = 0;
            }
            $limit = (int) $limit;
            if ($limit < 1) {
                $limit = 1;
            }
        }
        $sql = 'SELECT ';
        $sql .= empty($fields) ? '*' : '`' . join('`,`', $fields) . '`';
        $sql .= " FROM {$table} ";
        $sql .= empty($where) ? '' : " WHERE {$where} ";
        $sql .= empty($orderBy) ? '' : " ORDER BY {$orderBy}";
        $sql .= empty($limit) ? '' : " LIMIT {$start}, {$limit}";
        return $sql;
    }

    protected function createDaoException($message = null, $code = 0) {
        return new DaoException($message, $code);
    }

    protected function getConditionCompiler()
    {
        return  new SqlConditionCompiler();
    }

}