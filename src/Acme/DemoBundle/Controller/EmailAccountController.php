<?php
namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

// //下面是用USE的方法去调用我大sevice文件夹里的内容。。注：有空去问哥哥们是怎么在controller下调用service的。。
// use Acme\Service\Account\Impl\AccountServiceImpl;

/*
*http://api.symfony.com/2.0/Symfony/Component/HttpFoundation.html  
*/

class EmailAccountController extends BaseController
{
	public function indexAction()
	{
		// $allAccount = $this->getAccountService()->getAllAccount();
		// $AccountEmail = $this->createJsonResponse($AllAccount);

		return $this->render('AcmeDemoBundle:Email:email-account.html.twig'/*,array(
			'accounts'=>$allAccountm)*/);
	}

	public function GetAccountAction()
	{//输出json数据，到acount
		$allAccount = $this->getAccountService()->getAllAccount();
		return $this->createJsonResponse($allAccount);
	}

	public function DeleteAccountAction()
	{// 删除一条account
		$request = $this->getRequest();
		$accountId = $request->request->get('id');
		// var_dump($accountId);
		$this->getAccountService()->deleteAccount($accountId);
		//调用account页面
		return $this->redirect($this->generateUrl('account'));
	}
	
	public function UpdateAccountAction()
	{// 更新updata的时候,往表单里塞数据用。。
		$allAccount = $this->getAccountService()->getAllAccount();
		return $this->createJsonResponse($allAccount);

	}

	public function SetFormAction()
	{//提交表单里的数据
		$request = $this->getRequest();

		if ($request->request->get('id') != 0) {
			$newDate = $request->request->all();//http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/ParameterBag.html#method_all
			// var_dump($newDate);
			$id = $request->request->get('id');
			var_dump($this->getAccountService()->updateAccount($id,$newDate));
		}
		if ($request->request->get('id') == 0) {
			$newDate = $request->request->all();
			$this->getAccountService()->addAccount($newDate);
		}
	return $this->indexAction();
	}

	//设置locked状态
	public function setLockedAction()
	{
		$request = $this->getRequest();
		$accountId = $request->request->get('id');
		$Locked = $request->request->get('locked');
		$newLocked = array('locked' => $Locked);

		$this->getAccountService()->setLocked($accountId,$newLocked);
		return $this->createJsonResponse(array('status' => 'ok', 'Remind' =>'设置成功' ));
	} 


}
?>