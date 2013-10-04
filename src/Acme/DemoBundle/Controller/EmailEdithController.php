<?php
namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class EmailEdithController extends BaseController
{
 	//post文章管理首页
	public function postIndexAction()
	{
		$allPost = $this->getPostService()->getAllPost();
		$newAllPosts= array();
		foreach ($allPost as $key => $onePost) {
			$addTime = date('Y-m-d H:i',$onePost['addTime']);
			$updateTime = date('Y-m-d H:i',$onePost['updateTime']);
			$newAllPosts[] = array('id' => $onePost['id'],'title'=>$onePost['title'],'content'=>$onePost['content'],'addTime'=>$addTime,'updateTime'=>$updateTime);
		}
		return $this->render('AcmeDemoBundle:Email:post-list.html.twig', array(
			'newAllPosts' => $newAllPosts
		));
	}

	public function editAction($id)
	{
		$request = $this->getRequest();
		$post = $this->getPostService()->GetOnePost($id);
		
		if ($request->getMethod() == 'POST') {
			$date = $request->request->all();

			$newDate['title'] = $date['title'];
			$newDate['content'] = $date['content'];
			$newDate['updateTime'] = time();

			$this->getPostService()->UpdatePost($id,$newDate);
			return $this->redirect($this->generateUrl('Post'));
		}

		return $this->render('AcmeDemoBundle:Email:edit.html.twig', array(
			'post' => $post
		));

	}

	public function addPostAction()
	{
		$request = $this->getRequest();
		
		if ($request->getMethod() == 'POST') {
			$date = $request->request->all();

			$newDate['title'] = $date['title'];
			$newDate['content'] = $date['content'];
			$newDate['addTime'] = time();
			$newDate['updateTime'] = time();

			$this->getPostService()->AddPost($newDate);
			return $this->redirect($this->generateUrl('Post'));
		}
		return $this->render('AcmeDemoBundle:Email:edit.html.twig');
	}

	//删除一篇文章
	public function deletePostAction($id)
	{
		$this->getPostService()->DelectPost($id);
		return $this->redirect($this->generateUrl('Post'));
	}

	//预览代码
	public function previewPostAction($id)
	{
		$request = $this->getRequest();
		$postList = $this->getPostService()->GetOnePost($id);
			$newPostList = array();
			$newPostList['id'] = $postList['id'];
			$newPostList['title']=$postList['title'];
			$newPostList['content']=nl2br($postList['content']);
		return $this->createJsonResponse($newPostList);
	}
}
?>