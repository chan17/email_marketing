<?php
namespace Acme\Service\User\Impl;

use Acme\Service\Common\BaseService;
use Acme\Service\User\UserService;
use Acme\Service\User\CurrentUser;
use Acme\Common\ArrayToolkit;
use Acme\Common\SimpleValidator;

class UserServiceImpl extends BaseService implements UserService
{

    public function getUser($id)
    {
        return UserSerialize::unserialize(
            $this->getUserDao()->findUser($id)
        );
    }

    public function getUserByUsername($username)
    {
        return UserSerialize::unserialize(
            $this->getUserDao()->findUserByUsername($username)
        );
    }

    public function getUserByEmail($email)
    {
        return UserSerialize::unserialize(
            $this->getUserDao()->findUserByEmail($email)
        );
    }

    public function getUsersByIds(array $ids, $ordered = false)
    {
        $users = UserSerialize::unserializes(
            $this->getUserDao()->findUsersByIds($ids, $ordered)
        );
        return ArrayToolkit::index('id', $users);
    }

    public function searchUsers(array $conditions, $sort = null, $start = 0, $limit = 30)
    {
        $conditions = $this->prepareUserConditions($conditions);
        $sort = $this->checkSort($sort, array('createdTime', 'loginedTime'));
        return UserSerialize::unserializes(
            $this->getUserDao()->searchUsers($conditions, $sort, $start, $limit)
        );
    }

    public function searchUserCount(array $conditions)
    {
        $conditions = $this->prepareUserConditions($conditions);
        return $this->getUserDao()->searchUserCount($conditions);
    }

    public function getSecureInfo($userId)
    {
        $user = $this->getUser($userId);
        if (empty($user)) {
            throw $this->createServiceException('user not exist.');
        }

        $info = array();
        $info['password'] = true;
        $info['approval'] = $user['approvalStatus'] == 'approved';
        $info['emailConfirm'] = $user['emailConfirmed'] ? true : false;
        $info['secureQuestion'] = !empty($user['secureQuestion1']) && !empty($user['secureQuestion2']) && !empty($user['secureQuestion3']);

        $levels = array('low', 'medium', 'high', 'max');
        $levelNum = 0;
        foreach ($info as $key => $value) {
            if ($value === true) {
                $levelNum ++;
            }
        }
        $info['level'] = $levels[$levelNum-1];

        return $info;
    }

    protected function prepareUserConditions($conditions)
    {
        $conditions = array_filter($conditions, function($value){
            $value = trim($value);
            return !empty($value);
        });

        $prepared = array();

        if (!empty($conditions['keyword']) && !empty($conditions['keywordType']) &&
            in_array(
                $conditions['keywordType'],
                array('username', 'email', 'mobile', 'loginedIp')
            )
        ) {
            if (substr($conditions['keyword'], -1) == '%' or substr($conditions['keyword'], 0, 1) == '%') {
                $prepared[$conditions['keywordType'].':like'] = $conditions['keyword']; 
            } else {
                $prepared[$conditions['keywordType']] = $conditions['keyword'];
            }    
        }

        if (!empty($conditions['role'])) {
            $prepared['roles:like'] = "%{$conditions['role']}%";
        }

        return $prepared;
    }

    public function register($registration)
    {
        $user = array();
        $user['username'] = $registration['username'];
        $user['salt'] = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $user['password'] =  $registration['password'];
        $user['password'] =  $this->getPasswordEncoder()->encodePassword($user['password'], $user['salt']);

        $user['roles'] = array('ROLE_USER');
        $user['createdTime'] = time();

        $user = UserSerialize::unserialize(
            $this->getUserDao()->addUser(UserSerialize::serialize($user))
        );

        return $user;
    }

    public function updateUserInfo($id, array $info)
    {
        $user = $this->getUser($id);
        if (empty($user)) {
            throw $this->createServiceException('用户不存在，更新用户失败。');
        }
        $infoFields = array(
            'truename', 'idcard', 'gender', 'birthday', 
            'mobile', 'qq', 'school', 'companyName', 'address', 'about'
        );

        $info = ArrayToolkit::parts($info, $infoFields);

        if ($user['approvalStatus'] == 'approved') {
            unset($info['truename']);
            unset($info['idcard']);
        }

        if (!empty($info['truename']) && !SimpleValidator::truename($info['truename'])) {
            throw $this->createServiceException('真实姓名不正确，更新用户失败。');
        }

        if (!empty($info['idcard']) && !SimpleValidator::idcard($info['idcard'])) {
            throw $this->createServiceException('身份证号不正确，更新用户失败。');
        }

        if (!empty($info['gender']) && !in_array($info['gender'], array('male', 'female'))) {
            throw $this->createServiceException('性别不正确，更新用户失败。');
        }

        if (!empty($info['birthday']) && !SimpleValidator::date($info['birthday'])) {
            throw $this->createServiceException('生日不正确，更新用户失败。');
        }

        if (!empty($info['mobile']) && !SimpleValidator::mobile($info['mobile'])) {
            throw $this->createServiceException('手机不正确，更新用户失败。');
        }

        if (!empty($info['qq']) && !SimpleValidator::qq($info['qq'])) {
            throw $this->createServiceException('QQ不正确，更新用户失败。');
        }

        $this->getUserDao()->updateUser($id, UserSerialize::serialize($info));
    }

    public function changeUserRoles($id, array $roles)
    {
        $user = $this->getUser($id);
        if (empty($user)) {
            throw $this->createServiceException('用户不存在，设置用户角色失败。');
        }

        if (empty($roles)) {
            throw $this->createServiceException('用户角色不能为空');
        }

        $allowedRoles = array('ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN');

        $notAllowedRoles = array_diff($roles, $allowedRoles);
        if (!empty($notAllowedRoles)) {
            throw $this->createServiceException('用户角色不正确，设置用户角色失败。');
        }
        $this->getUserDao()->updateUser($id, UserSerialize::serialize(array('roles' => $roles)));
    }

    public function settingUserLevel($userId, $level)
    {
        $user = $this->getUserWithException($userId);
        $this->checkAdminUserWithException();
        return $this->getUserDao()->updateUser($userId, array('level' => $level));
    }

    public function changePassword($id, $password)
    {
        $user = $this->getUser($id);
        if (empty($user) or empty($password)) {
            throw $this->createServiceException('参数不正确，更改密码失败。');
        }

        $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

        $fields = array(
            'salt' => $salt,
            'password' => $this->getPasswordEncoder()->encodePassword($password, $salt),
        );

        $this->getUserDao()->updateUser($id, $fields);

        return true;
    }

    public function changeAvatar($userId, $smallAvatar, $middleAvatar, $largeAvatar)
    {
        $user = $this->getUser($userId);
        if (empty($user)) {
            throw $this->createServiceException('用户不存在，不能修改头像！');
        }
        $avatars = array('smallAvatar' => $smallAvatar, 'middleAvatar' => $middleAvatar, 'largeAvatar' => $largeAvatar);
        return $this->getUserDao()->updateUser($userId, $avatars);
    }

    public function verifyPassword($id, $password)
    {
        $user = $this->getUser($id);

        $encoder = $this->getPasswordEncoder(new CurrentUser());
        $passwordHash = $encoder->encodePassword($password, $user['salt']);

        return $user['password'] == $passwordHash;
    }

    public function confirmEmail($token)
    {
        $token = $this->getToken('email-verify', $token);
        if (empty($token)) {
            return false;
        }

        $user = $this->getUser($token['userId']);
        if (empty($user)) {
            return false;
        }

        $fields['emailConfirmed'] = 1;
        if (!empty($token['data']['email'])) {
            $fields['email'] = $token['data']['email'];
        }

        $this->getUserDao()->updateUser($user['id'], $fields);

        $this->deleteToken('email-verify', $token['token']);

        return $user;
    }

    public function makeConfirmEmailToken($userId, $newEmail = null)
    {
        if ($newEmail) {
            $data['email'] = $newEmail;
        } else {
            $data = null;
        }

        $token = $this->makeToken('email-verify', $userId, strtotime('+1 day'), $data);

        return $token;
    }

    public function resetPassword($token, $password)
    {
        $token = $this->getToken('password-reset', $token);
        if (empty($token)) {
            return false;
        }

        $this->changePassword($token['userId'], $password);

        $this->deleteToken('password-reset', $token['token']);

        return true;
    }

    public function makeResetPasswordToken($userId)
    {
        return $this->makeToken('password-reset', $userId, strtotime('+1 day'));
    }

    public function setSecureQuestion($userId, $questions)
    {
        if (empty($questions['secureQuestion1']) or 
            empty($questions['secureQuestion2']) or 
            empty($questions['secureQuestion3']) or
            empty($questions['secureAnswer1']) or
            empty($questions['secureAnswer2']) or
            empty($questions['secureAnswer3']) ) {
            throw $this->createServiceException('secure question error.');
        }

        $fields = array();
        $fields['secureQuestion1'] = $questions['secureQuestion1'];
        $fields['secureQuestion2'] = $questions['secureQuestion2'];
        $fields['secureQuestion3'] = $questions['secureQuestion3'];
        $fields['secureAnswer1'] = $questions['secureAnswer1'];
        $fields['secureAnswer2'] = $questions['secureAnswer2'];
        $fields['secureAnswer3'] = $questions['secureAnswer3'];

        $this->getUserDao()->updateUser($userId, $fields);

        return true;
    }

    public function verifySecureQuestion($userId, $questions)
    {

    }

    public function lockUser($id)
    {
        $user = $this->getUser($id);
        if (empty($user)) {
            throw $this->createServiceException('用户不存在，封禁失败！');
        }
        $this->getUserDao()->updateUser($user['id'], array('locked' => 1));

        return true;
    }

    public function unlockUser($id)
    {
        $user = $this->getUser($id);
        if (empty($user)) {
            throw $this->createServiceException('用户不存在，解禁失败！');
        }
        $this->getUserDao()->updateUser($user['id'], array('locked' => 0));

        return true;
    }

    public function getApprovingUsers($orderBy = null, $start = 0, $limit = 30)
    {
        $users = $this->getUserDao()->findUsersByApprovalStatus('approving', $orderBy, $start, $limit);
        return UserSerialize::unserializes($users);
    }

    public function getApprovingUserCount()
    {
        return $this->getUserDao()->findUserCountByApprovalStatus('approving');
    }

    public function getApprovedUsers($orderBy = null, $start = 0, $limit = 30)
    {
        $users = $this->getUserDao()->findUsersByApprovalStatus('approved', $orderBy, $start, $limit);
        return UserSerialize::unserializes($users);
    }

    public function getApprovedUserCount()
    {
        return $this->getUserDao()->findUserCountByApprovalStatus('approved');
    }

    public function getApproveFailUsers($orderBy = null, $start = 0, $limit = 30)
    {
        $users = $this->getUserDao()->findUsersByApprovalStatus('approve_fail', $orderBy, $start, $limit);
        return UserSerialize::unserializes($users);
    }

    public function getApproveFailUserCount()
    {
        return $this->getUserDao()->findUserCountByApprovalStatus('approve_fail');
    }

    public function applyApprove($userId, array $approveData)
    {
        $user = $this->getUser($userId);
        if (empty($user)) {
            throw $this->createServiceException('用户不存在，申请认证失败!');
        }
        $approveData = $this->filterApproveData($approveData);
        $this->checkApproveData($approveData);
        $approveData['approvalStatus'] = 'approving';
        return $this->getUserDao()->updateUser($userId, $approveData);
    }

    public function approveAccess($userId, $note = null)
    {
        $this->userCanApproveAuth($userId);
        $approveData = array('approvalStatus' => 'approved', 'approvalTime' => time());
        if ($this->getUserDao()->updateUser($userId, $approveData)) {
            $this->sendApproveNotification($userId, 'access', $note);
            return true;
        }
        return false;
    }

    public function approveFail($userId, $note)
    {
        $this->userCanApproveAuth($userId);
        $approveData = array('approvalStatus' => 'approve_fail', 'approvalTime' => time());
        if ($this->getUserDao()->updateUser($userId, $approveData)) {
            $this->sendApproveNotification($userId, 'fail', $note);
            return true;
        }
        return false;
    }

    private function userCanApproveAuth($userId)
    {
        $user = $this->getUser($userId);
        if (empty($user)) {
            throw $this->createServiceException('用户不存在，不能进行实名认证审核');
        }
        if ($user['approvalStatus'] != 'approving') {
            throw $this->createServiceException('用户不处于等待实名认证审核状态，不能进行审核');
        }
    }

    public function logApproveLog($userId, $operation, $operator, $ip)
    {
        $log = array();
        $log['userId'] = $userId;
        $log['operation'] = $operation;
        $log['operator'] = $operator;
        $log['ip'] = $ip;
        $log['logTime'] = time();
        return $this->getUserApproveLogDao()->addLog($log);
    }

    private function sendApproveNotification($userId, $accessType, $note = null)
    {
        $content = array('accessType' => $accessType, 'note' => $note);
        return $this->getNotificationService()->notify($userId, 'user-appvoe', $content);
    }

    private function filterApproveData($approveData)
    {
        $fields = array('truename', 'idcard', 'idcardPicture1', 'idcardPicture2', 'isCompany', 'companyName', 'companyLicenseNum', 
            'licensePicture', 'companyAddress');
        foreach ($approveData as $key => $value) {
            if (!in_array($key, $fields)) {
                unset($approveData[$key]);
            }
        }
        return $approveData;
    }

    private function checkApproveData($approveData)
    {
        $filePath = $this->getContainer()->getParameter('Acme.privateUpload.path');
        if (!is_file($filePath . '/' . $approveData['idcardPicture1']) || 
            !is_file($filePath . '/' . $approveData['idcardPicture2'])) {
            throw $this->createServiceException('身份证照片不存在，申请认证失败！');
        }
        if (!SimpleValidator::truename($approveData['truename'])) {
            throw $this->createServiceException('真实姓名不正确，申请认证失败！');
        }
        if (!SimpleValidator::idcard($approveData['idcard'])) {
            throw $this->createServiceException('身份证号码不正确，申请认证失败！');
        }

        if ($approveData['isCompany'] && !is_file($filePath . '/' . $approveData['licensePicture'])) {
            throw $this->createServiceException('营业执照照片不存在，申请认证失败！');
        }
    }

    public function makeToken($type, $userId = null, $expiredTime = null, $data = null)
    {
        $token = array();
        $token['type'] = $type;
        $token['userId'] = $userId ? (int)$userId : 0;
        $token['token'] = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $token['data'] = serialize($data);
        $token['expiredTime'] = $expiredTime ? (int) $expiredTime : 0;
        $token['createdTime'] = time();
        $token = $this->getUserTokenDao()->addToken($token);
        return $token['token'];
    }

    public function getToken($type, $token)
    {
        $token = $this->getUserTokenDao()->findTokenByToken($token);
        if (empty($token) || $token['type'] != $type) {
            return null;
        }
        if ($token['expiredTime'] > 0 && $token['expiredTime'] < time()) {
            return null;
        }
        $token['data'] = unserialize($token['data']);
        return $token;
    }

    public function deleteToken($type, $token)
    {
        $token = $this->getUserTokenDao()->findTokenByToken($token);
        if (empty($token) || $token['type'] != $type) {
            return false;
        }
        $this->getUserTokenDao()->deleteToken($token['id']);
        return true;
    }

    private function getUserDao()
    {
        return $this->createDao('User.UserDao');
    }

    private function getUserApproveLogDao()
    {
        return $this->createDao('User.UserApproveLogDao');
    }

    private function getUserTokenDao()
    {
        return $this->createDao('User.TokenDao');
    }

    private function getPasswordEncoder()
    {
        return $this->getContainer()->get('security.encoder_factory')->getEncoder(new CurrentUser());
    }

    private function getNotificationService() {
        return $this->createService('User.NotificationService');
    }
}

class UserSerialize
{
    public static function serialize(array $user)
    {
        $user['roles'] = empty($user['roles']) ? '' :  '|' . implode('|', $user['roles']) . '|';
        return $user;
    }

    public static function unserialize(array $user = null)
    {
        if (empty($user)) {
            return null;
        }
        $user['roles'] = empty($user['roles']) ? array() : explode('|', trim($user['roles'], '|')) ;
        return $user;
    }

    public static function unserializes(array $users)
    {
        return array_map(function($user) {
            return UserSerialize::unserialize($user);
        }, $users);
    }

}