<?php
namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SendEmailController extends BaseController
{
	public function indexAction($id)
	{
		$request = $this->getRequest();
		$post = $this->getPostService()->GetOnePost($id);
	
		return $this->render('AcmeDemoBundle:Email:send-email.html.twig',array(
			'onePost' => $post 
			));
	}

	public function sendEmailAction($id)
	{
		$request = $this->getRequest();
		$post = $this->getPostService()->GetOnePost($id);

		if ($request->query->get('senderid') != null) {
			$senderId=$request->query->get('senderid');
			$addressee = $request->query->get('addressee');
			// var_dump($senderId,$addressee);

			$config = $this->getAccountService()->getOneAccount($senderId);
			// print_r($config);

			//发送邮件的模块
			$transport = \Swift_SmtpTransport::newInstance($config['smtp_service'], $config['port'])
	          ->setUsername($config['smtp_account'])
	          ->setPassword($config['smtp_password']);

	        $mailer = \Swift_Mailer::newInstance($transport);

	        $message = \Swift_Message::newInstance($post['title'])
			  ->setFrom(array ($config['from_address'] => $config['from_name'] ))
			  ->setTo($addressee)
			  ->setBody($post['content'],'text/html')
			  ;
			try {
				$result = $mailer->send($message);
			} catch (\Exception $e) {
				return $this->createJsonResponse(array('status' => 'fail','message' => $e->getMessage()));
			}
			// $result = $mailer->send($message,$failures);
			// var_dump($result);
			// if ($result == 1)
			// {
			// 	return $this->createJsonResponse(array('消息：' => $failures));
			// }else{
			// 	return $this->createJsonResponse(array('消息：' => $message));
			// }
			
			sleep(1);
		}
		return $this->createJsonResponse(array('status' => 'ok','message' => '发送成功'));
	}

}

?> 