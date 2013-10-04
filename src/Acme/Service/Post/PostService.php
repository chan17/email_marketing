<?php
namespace Acme\Service\Post;

interface PostService
{
	public function AddPost(array $postVar);

	public function DelectPost($id);

	public function GetAllPost();

	public function GetOnePost($id);

	public function UpdatePost($id,array $newDate);
}
?>