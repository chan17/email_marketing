<?php
namespace Acme\Service\Account;

interface AccountService
{
	public function addAccount(array $accountVar);
	
	public function getAllAccount();
	
	public function getOneAccount($id);
	
	public function deleteAccount($id);
	
	public function updateAccount($id,array $newDate);

	public function setLocked($id, array $newLocked);
}

?>