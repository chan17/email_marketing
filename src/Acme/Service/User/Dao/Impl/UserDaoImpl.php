<?php

namespace Acme\Service\User\Dao\Impl;

use Acme\Service\Common\BaseDao;
use Acme\Service\User\Dao\UserDao;

class UserDaoImpl extends BaseDao implements UserDao
{
    protected $table = 'user';

    public function findUser($id)
    {
        return $this->fetch($id);
    }

    public function findUserByUsername($username)
    {
        return $this->fetchRow('username=?', array($username));
    }

    public function findUserByEmail($email)
    {
        return $this->fetchRow('email=?', array($email));
    }

    public function findUsersByIds(array $ids, $ordered)
    {
        return $this->fetchIn('id', $ids, $ordered);
    }

    public function findUsersByApprovalStatus($status, $orderBy = null, $start = 0, $limit = null)
    {
        return $this->fetchAll('approvalStatus=?', array($status), array(), $start, $limit);
    }

    public function findUserCountByApprovalStatus($status)
    {
        return $this->count('approvalStatus=?', array($status));
    }

    public function searchUsers($conditions, $orderBy, $start, $limit)
    {
        $compiled = $this->getConditionCompiler()->compile($conditions);
        $orderBy = empty($orderBy) ? null : join(' ', $orderBy);
        return $this->fetchAll($compiled['where'], $compiled['params'], array(), $orderBy, $start, $limit);
    }

    public function searchUserCount($conditions)
    {
        $compiled = $this->getConditionCompiler()->compile($conditions);
        return $this->count($compiled['where'], $compiled['params']);
    }

    public function updateUser($id, $fields)
    {
        return $this->update($id, $fields);
    }

    public function addUser($fields)
    {
        return $this->insert($fields);
    }

}