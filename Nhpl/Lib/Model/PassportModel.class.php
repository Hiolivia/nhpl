<?php



class PassportModel {

    private $CONFIG = array();
    private $charset = 0;
    private $isuc = false;
    private $error = null; //如果存在错误的时候返回一下错误
    private $domain = '@qq.com'; //可以修改
    private $token = array();//手机APP 需要的 access_token
    private $user  = array();
    private $_CONFIG = array();

    public function __construct() {

        $config = D('Setting')->fetchAll();
        $this->_CONFIG = $config;
        if ($config['site']['ucenter']) {
            $this->isuc = true;
        }
        $this->CONFIG = $config['ucenter'];

        $this->charset = $this->CONFIG['charset'];
        if ($this->isuc) {
            $this->ucinit();
        }
    }
    
    public function getToken(){
        return $this->token;
    }

    public function getUserInfo(){
        return $this->user;
    }
    
    public function getError() {
        return $this->error;
    }

    public function ucinit() {
        define('UC_CONNECT', $this->CONFIG['UC_CONNECT']);
        define('UC_DBHOST', $this->CONFIG['UC_DBHOST']);
        define('UC_DBUSER', $this->CONFIG['UC_DBUSER']);
        define('UC_DBPW', $this->CONFIG['UC_DBPW']);
        define('UC_DBNAME', $this->CONFIG['UC_DBNAME']);
        define('UC_DBCHARSET', $this->CONFIG['UC_DBCHARSET']);
        define('UC_DBTABLEPRE', $this->CONFIG['UC_DBTABLEPRE']);
        define('UC_DBCONNECT', $this->CONFIG['UC_DBCONNECT']);
        define('UC_KEY', $this->CONFIG['UC_KEY']);
        define('UC_API', $this->CONFIG['UC_API']);
        define('UC_CHARSET', $this->CONFIG['UC_CHARSET']);
        define('UC_IP', $this->CONFIG['UC_IP']);
        define('UC_APPID', $this->CONFIG['UC_APPID']);
        define('UC_PPP', $this->CONFIG['UC_PPP']);
        require BASE_PATH . '/api/uc_client/client.php';
    }

    public function logout() {
        clearUid();
        if ($this->isuc) {
            uc_user_synlogout();
        }
        return true;
    }

    public function uppwd($account, $oldpwd, $newpwd) {
        if ($this->isuc) {
            if (isMobile($account)) {
                $ucresult = uc_user_edit($account, $oldpwd, $newpwd, '', 1);
            } elseif (isEmail($account)) {
                $local = explode('@', $account);
                $ucresult = uc_user_edit($local[0], $oldpwd, $newpwd, '', 1);
            }
            if ($ucresult == -1) {
                $this->error = '旧密码不正确';
                return false;
            }
        }
        $user = D('Users')->getUserByAccount($account);
        return D('Users')->save(array('user_id' => $user['user_id'], 'password' => md5($newpwd)));
    }


    public function login($account, $password) {
        $this->token = array(
            'token' => md5(uniqid())
        );

        if (isMobile($account)) {
            $user = D('Huser')->getUserByMobile($account);
        } else {
            $user = D('Huser')->getUserByAccount($account);
        }
        if (empty($user)) {
            $this->error = '账号或密码不正确';
            return false;
        }
//        if ($user['closed'] == 1) {
//            $this->error = '用户不存在或被删除！';
//            return false;
//        }
        if ($user['password'] != md5($password)) {
            $this->error = '账号或密码不正确！';
            return false;
        }
//        if (date('Y-m-d', $user['last_time']) < TODAY) {
//            D('Huser')->prestige($user['user_id'], 'login');
//        }
        $data = array(
            'last_time' => NOW_TIME,
            'last_ip' => get_client_ip(),
            'huser_id' => $user['huser_id'],
//            'token' => $this->token['token'],
        );

        D('Huser')->save($data);
        setUid($user['huser_id']);

        $this->user = $user;
//        $this->token['uid'] = $user['huser_id'];
        return true;
    }

    public function register($data = array()) {
//        var_dump(getUid());exit;
        $this->token = array(
            'token' => md5(uniqid())
        );
        $data['reg_time'] = NOW_TIME;
        $data['reg_ip'] = get_client_ip();
        $obj = D('Huser');

        if (empty($data))
            return false;
        $data['password'] = md5($data['password']);
        $user = $obj->getUserByAccount($data['account']);
        if ($user) {
            $this->error = '该账户已经存在';
            return false;
        }

        if (isMobile($data['account'])) {
            $data['mobile'] = $data['account'];
        }
//        var_dump($data);exit;
        $data['huser_id'] = $obj->add($data);
//        }
//        echo $obj->getLastSql();exit;
        $this->token['uid'] = $data['huser_id'];
//        $connect = session('connect');
//        if (!empty($connect)) {
//            D('Connect')->save(array('connect_id' => $connect, 'uid' => $data['user_id']));
//        }
//		$integral_register = (int)$this->_CONFIG['integral']['register'];
//		if(!empty($integral_register)){
//			D('Users')->addIntegral($data['user_id'],$integral_register,'用户首次注册赠送积分');
//		}
        setUid($data['huser_id']);
        return true;
    }
	//增加微信应用注册
	
	public function register2($data = array()) {
        $this->token = array(
            'token' => md5(uniqid())
        );
        $data['reg_time'] = NOW_TIME;
        $data['reg_ip'] = get_client_ip();
        $invite_id = (int)cookie('invite_id');
        if(!empty($invite_id)){
            $userinvite = D('Users')->find($invite_id);
            if(!empty($userinvite)){ //讲新的 推广员身份给创建账号的
                $data['invite6'] = $invite_id;
                $data['invite5'] = $userinvite['invite6'];
                $data['invite4'] = $userinvite['invite5'];
                $data['invite3'] = $userinvite['invite4'];
                $data['invite2'] = $userinvite['invite3'];
                $data['invite1'] = $userinvite['invite2'];
            }
        }
        if (empty($data))
            return false;
        if ($this->isuc) { //开启了UC
            if (isMobile($data['account'])) {
                $uid = uc_user_register($data['ext0'], $data['password'], $data['account'] . $this->domain); //这个@QQ.COM 可以自己更换
            } else {
                $uid = uc_user_register($data['ext0'], $data['password'], $data['account']);
            }

            if ($uid <= 0) {
                switch ($uid) {
                    case -1:
                        $this->error = '用户名不合法';
                        break;
                    case -2:
                        $this->error = '用户名包含不允许注册的词语';
                        break;
                    case -3:
                        $this->error = '用户名已经存在';
                        break;
                    case -4:
                        $this->error = 'Email 格式有误';
                        break;
                    case -5:
                        $this->error = 'Email 不允许注册';
                        break;
                    case -6:
                        $this->error = '该 Email 已经被注册';
                        break;
                }
                return false;
            }
            $data['uc_id'] = $uid;
            $data['password'] = md5($data['password']);
            $obj = D('Users');
            $user = $obj->getUserByAccount($data['account']);
            $data['token'] = $this->token['token'];
            if ($user) {
                $data['user_id'] = $user['user_id'];
                $obj->save($data);
            } else {
                $data['user_id'] = $obj->add($data);
            }
        } else {
            $obj = D('Users');
            $data['password'] = md5($data['password']);
            $user = $obj->getUserByAccount($data['account']);
            if ($user) {
                $this->error = '该账户已经存在';
                return false;
            }
            $data['user_id'] = $obj->add($data);
        }
        $this->token['uid'] = $data['user_id'];
        $connect = session('connect');
        if (!empty($connect)) {
            D('Connect')->save(array('connect_id' => $connect, 'uid' => $data['user_id']));
        }
        setUid($data['user_id']);
        return true;
    }

}