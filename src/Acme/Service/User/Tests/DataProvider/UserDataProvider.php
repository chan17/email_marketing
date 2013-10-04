<?php

namespace Topxia\Service\User\Tests\DataProvider;

use Topxia\Service\Common\TestDataProvider;

class UserDataProvider extends TestDataProvider {

	protected $rows = array(
		array(
			'id' => 1,
			'username' => 'username_aaa',
			'password' => 'password_aaa'
		),
		array(
			'id' => 2,
			'username' => 'username_bbb',
			'password' => 'password_aaa'
		),
		array(
			'id' => 3,
			'username' => 'admin',
			'password' => '123456'
		),
		array(
			'id' => 4,
			'username' => 'username_ddd',
			'password' => 'password_aaa'
		),
		array(
			'id' => 5,
			'username' => 'username_eee',
			'password' => 'password_aaa'
		),
		array(
			'id' => 6,
			'username' => 'username_fff',
			'password' => 'password_aaa'
		),
	);

	protected $dropSql = 'DROP TABLE IF EXISTS user';

	protected $emptySql = 'TRUNCATE TABLE user';

	protected $createSql = "
		CREATE TABLE IF NOT EXISTS `user` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `username` varchar(128) NOT NULL,
		  `password` varchar(64) NOT NULL,
		  `salt` varchar(32) NOT NULL,
		  `avatar` varchar(255) NOT NULL,
		  `largeAvatar` varchar(255) NOT NULL,
		  `roles` varchar(255) NOT NULL,
		  `loginTime` int(11) NOT NULL DEFAULT '0',
		  `loginIp` varchar(64) NOT NULL,
		  `createdIp` varchar(64) NOT NULL,
		  `createdTime` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	";
}