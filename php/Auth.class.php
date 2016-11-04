<?php
/**
 * 用户及授权
 * @author Flc <2016-10-19 18:38:43>
 * @example      
 */
namespace Common\Service;

use Think\Crypt;

class Auth
{
    /**
     * 获取用户信息
     * @return [type] [description]
     */
    public function info($userid = '', $is_refress = false)
    {
        if (empty($userid))
            $userid = self::check();

        return self::user($userid, $is_refress);
    }

    /**
     * 用户登录
     * @param  integer $userid 用户ID
     * @return boolean         
     */
    public function login($userid)
    {
        $user = self::user($userid);

        if (! $user)
            return false;

        // 生成session
        $sessions = [
            'userid'    => $user['userid'],
            'timestamp' => time(),
        ];
        $sessions['token'] = self::generateToken($sessions);

        session(self::getSessionKey(), $sessions);

        return true;
    }

    /**
     * 判断当前登录是否合法(如果合法，则返回userid)
     * @return boolden
     */
    public static function check()
    {
        $sessions = session(self::getSessionKey());

        if (! array_key_exists('userid', $sessions) ||
            ! array_key_exists('timestamp', $sessions) ||
            ! array_key_exists('token', $sessions)
        ) {
            self::logout();
            return false;
        }

        // 校验合法性
        $nToken = self::generateToken([
            'userid'    => $sessions['userid'],
            'timestamp' => $sessions['timestamp'],
        ]);
        if ($nToken != $sessions['token']) {
            self::logout();
            return false;
        }

        // 数据合法性校验
        if (! self::user($sessions['userid'])) {
            self::logout();
            return false;
        }

        return $sessions['userid'];
    }

    /**
     * 登出
     * @return [type] [description]
     */
    public static function logout()
    {
        session(self::getSessionKey(), null);

        return true;
    }

    /**
     * 获取用户信息
     * @param  integer $userid 用户ID
     * @return array|boolean         
     */
    protected static function user($userid, $is_refress = false)
    {
        static $result = [];

        if (! $is_refress && array_key_exists($userid, $result))
            return $result[$userid];

        return $result[$userid] = M('user')->where(['userid' => $userid])->find();
    }

    /**
     * 创建Token
     * @return [type] [description]
     */
    protected static function generateToken($params = [])
    {
        ksort($params);

        $tmps = array();

        foreach ($params as $k => $v) {
            $tmps[] = $k . $v;
        }

        $string = self::getAuthKey() . implode('', $tmps) . self::getAuthKey();

        return strtoupper(md5($string));
    }

    /**
     * 获取session的KEY
     * @return string 
     */
    protected static function getSessionKey()
    {
        return 'login_' . md5('user');;
    }

    /**
     * 获取加密密钥
     * @return string 
     */
    protected static function getAuthKey()
    {
        return C('AUTH_USER_KEY');
    }
}