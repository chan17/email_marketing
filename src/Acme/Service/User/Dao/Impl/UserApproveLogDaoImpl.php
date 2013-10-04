<?php
namespace Acme\Service\User\Dao\Impl;

use Acme\Service\Common\BaseDao;
use Acme\Service\User\Dao\UserApproveLogDao;

class UserApproveLogDaoImpl extends BaseDao implements UserApproveLogDao
{

	protected $table = 'userApproveLog';

	public function addLog(array $log)
	{
		return $this->insert($log);
	}

}