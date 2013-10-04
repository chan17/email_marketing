<?php
namespace Acme\DemoBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Acme\DemoBundle\Form\RegisterType;


class RegisterController extends BaseController
{
    public function indexAction(Request $request)
    {
    	$form = $this->createForm(new RegisterType());
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $registration = $form->getData();
                $user = $this->getUserService()->register($registration);
                $this->authenticateUser($user);
                return $this->redirect($this->generateUrl('login'));
            }
        }

        return $this->render("AcmeDemoBundle:Register:index.html.twig", array(
            'form' => $form->createView()
        ));
	}

    public function checkUsernameAction(Request $request)
    {
        return $this->createJsonResponse(true);
    }
}