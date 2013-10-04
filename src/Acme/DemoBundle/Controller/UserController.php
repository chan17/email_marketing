<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;

class UserController extends BaseController
{

	public function indexAction()
	{
		$request = $this->getRequest();
		$session = $request->getSession();
		return $this->render('AcmeDemoBundle:User:index.html.twig');
	}

	public function infoAction()
	{
		$request = $this->getRequest();
		$session = $request->getSession();
		return $this->render('AcmeDemoBundle:User:index.html.twig');
	}

	public function safeAction()
	{
		$request = $this->getRequest();
		$session = $request->getSession();
		return $this->render('AcmeDemoBundle:User:authenticate.html.twig');
	}
	
	public function authenticateAction()
	{
		$request = $this->getRequest();
		$session = $request->getSession();
		return $this->render('AcmeDemoBundle:User:authenticate.html.twig');
	}

	public function showIdcardAction($userId, $type)
	{
		$visitor = $this->getUser();
		if (empty($visitor)) {
            throw $this->createAccessDeniedException();
        }

		$user = $this->getUserService()->getUser($userId);
        if ($visitor['id'] != $user['id'] && !$this->get('security.context')->isGranted('ROLE_ADMIN_SERVICE')) {
            throw $this->createAccessDeniedException();
        }
        
        $type = $type == 'font' ? 'idcardPicture1' : 'idcardPicture2';
        return $this->showPrivatePicture($user[$type]);
	}

	public function showCompanyLicenseAction($userId)
	{
		$visitor = $this->getUser();
		if (empty($visitor)) {
            throw $this->createAccessDeniedException();
        }

		$user = $this->getUserService()->getUser($userId);
        if ($visitor['id'] != $user['id'] && !$this->get('security.context')->isGranted('ROLE_ADMIN_SERVICE')) {
            throw $this->createAccessDeniedException();
        }
        return $this->showPrivatePicture($user['licensePicture']);
	}

	

}