<?php

namespace Acme\Service\User\Dao;

interface NotificationDao
{
	public function addNotification($notification);

	public function findNotificationsByUserId($userId, $start, $limit);
}