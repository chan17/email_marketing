<?php
namespace Acme\Service\User\Impl;

use Acme\Service\Common\BaseService;
use Acme\Service\User\NotificationService;

class NotificationServiceImpl extends BaseService implements NotificationService
{
    public function notify($userId, $type, $content)
    {
        $notification = array();
        $notification['userId'] = $userId;
        $notification['type'] = empty($type) ? 'default' : (string) $type;
        $notification['content'] = is_array($content) ? $content : array('message' => $content);
        $notification['createdTime'] = time();

        $this->getNotificationDao()->addNotification(NotificationSerialize::serialize($notification));
        	
        // $this->getUserService()->waveUserCounter($userId, 'unreadNotificationNum', 1);

        return true;
    }

    public function getUserNotifications($userId, $start = 0,  $limit = 30)
    {
        return NotificationSerialize::unserializes($this->getNotificationDao()->findNotificationsByUserId($userId, $start, $limit));
    }

    public function getNotificationDao()
    {
        return $this->createDao('User.NotificationDao');
    }
}


class NotificationSerialize
{
    public static function serialize(array $notification)
    {
        $notification['content'] = json_encode($notification['content']);
        return $notification;
    }

    public static function unserialize(array $notification = null)
    {
        if (empty($notification)) {
            return null;
        }
        $notification['content'] = json_decode($notification['content'], true);
        return $notification;
    }

    public static function unserializes(array $notifications)
    {
    	return array_map(function($notification) {
    		return NotificationSerialize::unserialize($notification);
    	}, $notifications);
    }
}