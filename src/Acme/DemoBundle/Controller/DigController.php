<?php
namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DigController extends Controller
{
	public function exportAction () {
		// get 
		$request = $this->getRequest();

		if ($request->getMethod() == 'POST') {
			$text = $request->request->get('text');
			if (empty($text)) {
				return new JsonResponse(array('status' => 'error', 'error' => array('message' => '请输入文字')));
			}
			// $text="咱们群里好d像有， 2011-06-29 17:32:26 啜 (爱是山呼海啸般的回应) 我要，我的邮箱 956713070@qq.com 举报 2011-07-11 02:09:35 Hyacinthus (身着素袍的雅辛托斯) 175279616@qq.com 多谢楼主！ 2011-08-04 23:02:26 444 738900383@qq.com 多谢楼主！";

			//判断邮箱的正则表达式。。
			$regexp = '/([a-z0-9_\.\-])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,4})+/i';

			preg_match_all($regexp,$text,$emailArray);

			$newArray = array();
			if (!empty($emailArray[0])) {
			//下面这个foreach是对取出后的邮箱地址处理。
				foreach ($emailArray[0] as $value) {
					$newArray[] = $value;
				}
				return new JsonResponse(array('status' => 'ok', 'emails' => $newArray));
			}else{
				return new JsonResponse(array('status' => 'error', 'error' => array('message' => '在文字中，找不到邮箱哦~')));
				// exit();
			}
		}

		
		return $this->render('AcmeDemoBundle:Email:dig-email.html.twig');
	}
}