<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Acme\Service\Common\ServiceKernel;
use Acme\Service\User\CurrentUser;
use Imagine\Imagick\Imagine;
use Imagine\Imagick\Image;

class BaseController extends Controller
{    
    protected function getAccountService()
    {
        return $this->createService('Account.AccountService');
    }
    
    protected function getPostService()
    {
        return $this->createService('Post.PostService');
    }

    protected function getUserService()
    {
        return $this->createService('User.UserService');
    }

    protected function authenticateUser ($user)
    {
        $currentUser = new CurrentUser();
        $currentUser->fromArray($user);

        $token = new UsernamePasswordToken($currentUser, null, 'main', $currentUser->getRoles());
        $this->container->get('security.context')->setToken($token);

        $loginEvent = new InteractiveLoginEvent($this->getRequest(), $token);
        $this->get('event_dispatcher')->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $loginEvent);
    }

    protected function getCurrentUser()
    {
         $container = $this->getServiceKernel()->getContainer();
        if (!$container->has('security.context')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $container->get('security.context')->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user->toArray();
    }

    protected function setFlashMessage ($message, $level = 'message') {
        $this->get('session')->setFlash($level, $message);
    }

    protected function getSort($conditions,$orderBy,$order='desc'){
        if(empty($conditions['orderBy'])){
            return array($orderBy, $order);
        }else{
            return array($conditions['orderBy'], $conditions['order']);
        }
    }

    protected function getQuery ($key, $default = null) {
        return $this->get('request')->query->get($key, $default);
    }

    protected function sendEmail($to, $title, $body, $format = 'text')
    {
        $format == 'html' ? 'text/html' : 'text/plain';

        $config = $this->getSettingService()->get('mailer') ? : array();

        if (empty($config['enabled'])) {
            return false;
        }

        $transport = \Swift_SmtpTransport::newInstance($config['host'], $config['port'])
          ->setUsername($config['username'])
          ->setPassword($config['password']);

        $mailer = \Swift_Mailer::newInstance($transport);

        $email = \Swift_Message::newInstance();
        $email->setSubject($title);
        $email->setFrom(array ($config['from'] => $config['name'] ));
        $email->setTo($to);
        $email->setBody($body, $format);
        $mailer->send($email);

        return true;
    }

    protected function createJsonResponse($data)
    {
        return new JsonResponse($data);
    }

    protected function createService($name)
    {
        return $this->getServiceKernel()->createService($name);
    }

    protected function getServiceKernel()
    {
        return ServiceKernel::instance();
    }

    public function createPrivateFileDownloadResponse($fileUri)
    {

        $filename = realpath($this->container->getParameter('Acme.privateUpload.path')) . '/' .  $fileUri;

        $response = new Response();

        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($filename));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filename) . '"');
        $response->headers->set('Content-length', filesize($filename));

        $response->sendHeaders();

        $response->setContent(readfile($filename));

        return $response;
    }

    public function showPrivatePicture($fileUri)
    {
        $filename = realpath($this->container->getParameter('Acme.privateUpload.path')) . '/' .  $fileUri;
        $imagine = new Imagine();
        $image = $imagine->open($filename);
        $image->show('jpg');
        unset($image, $imagine);
        exit();
    }
}