<?php
namespace Acme\Service\Post\Dao\Impl;

use Acme\Service\Common\BaseDao;
use Acme\Service\Post\Dao\PostDao;

class PostDaoImpl extends BaseDao implements PostDao
{
	protected $table = "post";

	public function AddPost(array $postVar)
	{
		return $this->insert($postVar);
	}

	public function DelectPost($id)
	{
		return $this->delete($id);
	}
	
	public function GetAllPost()
	{
		return $this->fetchAll();
	}
	
	public function GetOnePost($id)
	{
		return $this->fetch($id);
	}

	public function UpdatePost($id,array $newDate)
	{
		return $this->update($id,$newDate);
	}
}
?>