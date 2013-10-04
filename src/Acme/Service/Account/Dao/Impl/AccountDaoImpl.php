<?php
namespace Acme\Service\Account\Dao\Impl;

use Acme\Service\Common\BaseDao;
use Acme\Service\Account\Dao\AccountDao;

class AccountDaoImpl extends BaseDao implements AccountDao
{
	// private $db;
	protected $table = "account_management";

	// public function __construct($db) {
	// 	$this->db = $db;
	// }

	public function addAccount(array $accountVar) {
		// $sql = "INSERT into {$this->table}(`smtp_account`,`smtp_password`,`smtp_service`,`port`,`from_address`,`from_name`) values(?,?,?,?,?,?)";
		return $this->insert($accountVar);
	}

	public function getAllAccount(){
		// return $this->fetchAll("SELECT * FROM {$this->table}");
		return $this->fetchAll();
	}

	public function getOneAccount($id)
	{
		// return $this->fetch("SELECT * FROM {$this->table} WHERE id={$id}");
		return $this->fetch($id);
	}

	public function deleteAccount($id)
	{
		return $this->delete($id);
	}

	public function updateAccount($id,array $newDate){
		// $sql = "UPDATE {$this->table} set smtp_account = ?, smtp_password = ? ,smtp_service = ?, port = ?, from_address = ?, from_name = ? WHERE id={$id}";
		return $this->update($id,$newDate);
	}

	public function setLocked($id, array $newLocked)
	{
		return $this->update($id,$newLocked);
	}

}
?>