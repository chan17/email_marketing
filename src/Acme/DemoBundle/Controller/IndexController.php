<?php
namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class IndexController extends Controller
{
	public function indexAction () {

		return $this->render('AcmeDemoBundle:Email:index.html.twig');
	}
}