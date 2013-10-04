<?php

namespace Acme\Service\User\Dao;

interface TokenDao
{

	public function findToken($id);

	public function findTokenByToken($token);

	public function addToken(array $token);

	public function deleteToken($id);

}