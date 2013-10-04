<?php

namespace Acme\Service\User\Dao;

interface UserDao
{
	public function findUser($id);

	public function findUserByUsername($username);

	public function findUserByEmail($email);

	public function findUsersByIds(array $ids, $ordered);

	public function findUsersByApprovalStatus($status, $orderBy = null, $start = 0, $limit = null);

	public function findUserCountByApprovalStatus($status);

    public function searchUsers($conditions, $order, $start, $limit);

    public function searchUserCount($conditions);

	public function updateUser($id, $fields);

	public function addUser($fields);

}