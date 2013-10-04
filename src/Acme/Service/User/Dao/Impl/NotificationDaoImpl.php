<?php

namespace Acme\Service\User\Dao\Impl;

use Acme\Service\Common\BaseDao;
use Acme\Service\User\Dao\NotificationDao;

class NotificationDaoImpl extends BaseDao implements NotificationDao
{
    protected $table = 'notification';

    public function addNotification($notification)
    {
        return $this->insert($notification);
    }

    public function findNotificationsByUserId($userId, $start, $limit)
    {
        return $this->fetchAll('userId=?', array($userId), null, 'createdTime DESC', $start,  $limit);
    }
}