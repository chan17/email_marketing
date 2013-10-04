<?php
namespace Acme\Service\Post\Impl;

use Acme\Service\Common\BaseService;
use Acme\Service\Post\PostService;

class PostServiceImpl extends BaseService implements PostService
{
	public function AddPost(array $postVar)
	{
		return $this->GetPostDao()->AddPost($postVar);
	}

	public function DelectPost($id)
	{
		return $this->GetPostDao()->DelectPost($id);
	}

	public function GetAllPost()
	{
		return $this->GetPostDao()->GetAllPost();
	}

	public function GetOnePost($id)
	{
		return $this->GetPostDao()->GetOnePost($id);
	}

	public function UpdatePost($id,array $newDate)
	{
		return $this->GetPostDao()->UpdatePost($id,$newDate);
	}

	private function GetPostDao()
	{
		return $this->createDao('Post.PostDao');
	}
}
?>