<?php

namespace app\common\model;

use ba\Random;
use think\Model;
use think\facade\Config;

/**
 * 会员公共模型
 * @property int    $id              会员ID
 * @property string $password        密码密文
 * @property string $salt            密码盐
 * @property int    $login_failure   登录失败次数
 * @property string $last_login_time 上次登录时间
 * @property string $last_login_ip   上次登录IP
 */
class User extends Model
{
    protected $autoWriteTimestamp = true;

    public function getAvatarAttr($value)
    {
        return full_url(htmlspecialchars_decode($value), true, Config::get('buildadmin.default_avatar'));
    }

    public function resetPassword($uid, $newPassword)
    {
        $salt   = Random::build('alnum', 16);
        $passwd = encrypt_password($newPassword, $salt);
        return $this->where(['id' => $uid])->update(['password' => $passwd, 'salt' => $salt]);
    }

    public function getMoneyAttr($value)
    {
        return bcdiv($value, 100, 2);
    }

    /**
     * 用户的余额是不可以直接进行修改的，请通过 UserMoneyLog 模型插入记录来实现自动修改余额
     * 此处定义上 money 的修改器仅为防止直接对余额的修改造成数据错乱
     */
    public function setMoneyAttr($value)
    {
        return bcmul($value, 100, 2);
    }
}