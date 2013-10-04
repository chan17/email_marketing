<?php
namespace Acme\Service\Post\Dao;

interface PostDao
{
	public function AddPost(array $postVar);

	public function DelectPost($id);

	public function GetAllPost();

	public function GetOnePost($id);

	public function UpdatePost($id,array $newDate);
}

?>