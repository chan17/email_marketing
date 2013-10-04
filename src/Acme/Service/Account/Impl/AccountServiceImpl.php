<?php
namespace Acme\Service\Account\Impl;

use Acme\Service\Common\BaseService;
use Acme\Service\Account\AccountService;

// use Topxia\Common\ArrayToolkit;
// use Topxia\Common\SimpleValidator;

class AccountServiceImpl extends BaseService implements AccountService
{
	// private $AccountDao;
	// public function __construct() {
	// 	$db = new DB();
	// 	$this->getAccountDao()->AccountDao = new AccountDao($db);
	// }

	public function addAccount(array $accountVar) {
		$accountVar['from_address'] = $accountVar['smtp_account'];
	
		return $this->getAccountDao()->addAccount($accountVar);
	}

	public function getAllAccount() {
		return $this->getAccountDao()->getAllAccount();
	}

	public function getOneAccount($id){
		return $this->getAccountDao()->getOneAccount($id);
	}

	public function deleteAccount($id){
		return $this->getAccountDao()->deleteAccount($id);
	}

	public function updateAccount($id,array $newDate){
		$newDate['from_address'] = $newDate['smtp_account'];
		
		return $this->getAccountDao()->updateAccount($id,$newDate);
	}

	private function getAccountDao()
    {
        return $this->createDao('Account.AccountDao');
    }

    public function setLocked($id,array $newLocked)
    {
    	return $this->getAccountDao()->setLocked($id,$newLocked);
    }
}
?>