<?php
namespace Acme\Service\User\Dao\Impl;

use Acme\Service\Common\BaseDao;
use Acme\Service\User\Dao\TokenDao;

class TokenDaoImpl extends BaseDao implements TokenDao
{
    protected $table = 'user_token';

	public function findToken($id)
	{
		return $this->fetch($id);
	}

	public function findTokenByToken($token)
	{
		return $this->fetchRow('token=?', array($token));
	}

	public function addToken(array $token)
	{
		return $this->insert($token);
	}

	public function deleteToken($id)
	{
		return $this->delete($id);
	}

}