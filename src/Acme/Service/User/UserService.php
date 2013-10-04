<?php

namespace Acme\Service\User;

interface UserService
{
	public function getUser($id);

	public function getUserByUsername($username);

    public function getUserByEmail($email);

	public function getUsersByIds(array $ids, $ordered = false);

    public function searchUsers(array $conditions, $sort = 'latestRegisted', $start = 0, $limit = 30);

    public function searchUserCount(array $conditions);

    public function getSecureInfo($userId);

    public function register($registration);

    public function updateUserInfo($id, array $info);

    public function changeUserRoles($id, array $roles);

    public function changePassword($id, $password);

    public function verifyPassword($id, $password);

    public function confirmEmail($token);

    public function makeConfirmEmailToken($userId, $newEmail = null);

    public function resetPassword($token, $password);

    public function settingUserLevel($userId, $level);

    public function makeResetPasswordToken($userId);

    public function setSecureQuestion($userId, $questions);

    public function verifySecureQuestion($userId, $questions);

    public function lockUser($id);

    public function unlockUser($id);

    public function getApprovingUsers($orderBy = null, $start = 0, $limit = 30);

    public function getApprovingUserCount();

    public function getApprovedUsers($orderBy = null, $start = 0, $limit = 30);

    public function getApprovedUserCount();

    public function getApproveFailUsers($orderBy = null, $start = 0, $limit = 30);

    public function getApproveFailUserCount();
    
    public function applyApprove($userId, array $approveData);

    public function approveAccess($userId, $note = null);

    public function approveFail($userId, $note);

    public function logApproveLog($userId, $operation, $operator, $ip);

    public function makeToken($type, $userId = null, $expiredTime = null, $data = null);

    public function getToken($type, $token);

    public function deleteToken($type, $token);

    public function changeAvatar($userId, $smallAvatar, $middleAvatar, $largeAvatar);

}