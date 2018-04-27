<?php 


class PassportAction extends CommonAction{

	private $create_fields = array('account', 'password', 'nickname');


    private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
        $data['account'] = htmlspecialchars($_POST['account']);

        if (!isMobile($data['account'])) {
            $data = array('status' => self::BAO_INPUT_ERROR ,'msg' =>'只允许手机号注册' );
            $this->stringify($data);
        }
        $data['password'] = htmlspecialchars($_POST['password']); //整合UC的时候需要

        if (empty($data['password']) || strlen($_POST['password']) < 6) {
            session('verify', null);
            $data = array('status' => self::BAO_INPUT_ERROR ,'msg' =>'密码长度必须要在6个字符以上' );
            $this->stringify($data);
        }
        $data['mobile'] = $data['account'];
        $data['reg_ip'] = get_client_ip();
        $data['reg_time'] = NOW_TIME;
        return $data;
    }

    public function register() {
        if ($this->isPost()) {
            if (isMobile(htmlspecialchars($_POST['account']))) {
				/*
                $scode2 = session('scode');
                if (empty($scode2)) {
                    $data = array('status' => self::BAO_SCODE_EMPTY,'msg' =>'请获取短信验证码！' );
                    $this->stringify($data);
                }
                if ($scode != $scode2) {
                    $data = array('status' => self::BAO_SCODE_NOTSAME,'msg' =>'请输入正确的短信验证码！' );
                    $this->stringify($data);
                }
				*/
            }
			
            $data = $this->createCheck();
//            var_dump($data);exit;
            $password2 = $this->_post('password2');
            if ($password2 !== $data['password']) {
                $data = array('status' => self::BAO_REG_PSWD_ERROR,'msg' =>'两次密码不一致' );
                $this->stringify($data);
            }
            //开始其他的判断了
            if (true == D('Passport')->register($data)) {
                //注册成功添加 系统消息
                $datas = array(
                    'huser_id' => getUid(),
                    'title' => '尊敬的用户，恭喜您成功成为你好漂亮的荣誉理发师',
                    'content' => '尊敬的用户，恭喜您成功成为你好漂亮的荣誉理发师',
                    'system' => 1,
                    'status' => 1,
                    'channel' => 1,
                    'orderby' => 100,
                    'is_mail' => 2,
                    'time' => time()
                );
                D('Messages')->add($datas);
                $data = array('status' => self::BAO_REQUEST_SUCCESS,'msg' =>'恭喜您注册成功' );

                $this->stringify($data);
            }
            $data = array('status' => self::BAO_DB_ERROR,'msg' => D('Passport')->getError() );
            $this->stringify($data);
        }
    }

    public function sendsms() {
        if (!$mobile = htmlspecialchars($_POST['mobile'])) {
            $data = array('status' => self::BAO_INPUT_ERROR,'msg' =>'请输入正确的手机号码' );
            $this->stringify($data);
        }
        if (!isMobile($mobile)) {
            $data = array('status' => self::BAO_INPUT_ERROR,'msg' =>'请输入正确的手机号码' );
            $this->stringify($data);
        }
        $findpass = (int)$_POST['findpass'];
        if ($findpass < 0 &&$user = D('Users')->getUserByAccount($mobile)) {
            $data = array('status' => self::BAO_INPUT_ERROR,'msg' =>'该手机号已经被注册' );
            $this->stringify($data);
        }
		//APPCAN无法存储COOKIE，直接传入本地进行比对
        $randstring = session('scode');
        if(empty($randstring)) {
            $randstring = rand_string(6, 1);
            session('scode', $randstring);
        }
        //die(session('scode'));
        D('Sms')->sendSms('sms_code', $mobile, array('code' => $randstring));
		$data = array('status' => self::BAO_REQUEST_SUCCESS,'scode' =>$randstring );
		$this->stringify($data);
    }

    public function third(){
        if (!$type = htmlspecialchars($_POST['type'])) {
            $data = array('status' => self::BAO_INPUT_ERROR);
            $this->stringify($data);
        }
        if (!$openid = htmlspecialchars($_POST['openid'])) {
            $data = array('status' => self::BAO_INPUT_ERROR);
            $this->stringify($data);
        }
        if (!$token = htmlspecialchars($_POST['token'])) {
            $data = array('status' => self::BAO_INPUT_ERROR);
            $this->stringify($data);
        }
        $data = array(
            'type' => $type,
            'open_id' => $openid,
            'token' => $token
        );
        $this->thirdlogin($data);
    }



    private function setuid($uid,$user_token){
        $data = array(
            'uid' => $uid,
            'token' => $user_token
        );
        D('Huser')->save($data);
        $data = array('uid' => $connect['uid']);
        $users = D('Huser')->where($data)->find();
        return $users;
    }

     private function thirdlogin($data) {
        $user_token = md5(uniqid());
        $bind = 0;
        $users = 0;
        if ($this->_CONFIG['connect']['debug']) { //调试状态下 可以直接就登录 不是调试状态就要走绑定用户名的流程
            $data['type'] = 'test'; //DEBUG状态是直接登录
            $connect = D('Connect')->getConnectByOpenid($data['type'], $data['open_id']);
            if (empty($connect)) {
                $connect = $data;
                $connect['connect_id'] = D('Connect')->add($data);
            } else {
                D('Connect')->save(array('connect_id' => $connect['connect_id'], 'token' => $data['token']));
            }
            if (empty($connect['uid'])) {
                $account = $data['type'] . rand(100000, 999999) . '@qq.com';
                $user = array(
                    'account' => $account,
                    'password' => rand(100000, 999999),
                    'nickname' => $data['type'] . $connect['connect_id'],
                    'ext0' => $account,
                    'create_time' => NOW_TIME,
                    'create_ip' => get_client_ip(),
                );
                if (!D('Passport')->register($user))
                    $this->error('创建帐号失败');

                $token = D('Passport')->getToken();
                $connect['uid'] = $token['uid'];
                D('Connect')->save(array('connect_id' => $connect['connect_id'], 'uid' => $connect['uid']));
            }

            setUid($connect['uid']);
            if(IS_WEIXIN) {
                cookie('access', $connect['connect_id']);
                $back_url = cookie('wx_back_url');
                $back_url = $back_url ? $back_url :U('index/index') ;
                header("Location:".$back_url);
            }
            header("Location:" . U('index/index'));
            die;
        } else {
            $connect = D('Connect')->getConnectByOpenid($data['type'], $data['open_id']);
            if (empty($connect)) {
                $connect = $data;
                $connect['connect_id'] = D('Connect')->add($data);
            } else {
                D('Connect')->save(array('connect_id' => $connect['connect_id'], 'token' => $data['token']));
            }
            if (empty($connect['uid'])) {
                if($this->uid){
                    D('Connect')->save(array('connect_id' => $connect['connect_id'], 'uid' => $this->uid));
                    $this->stringify(array('status'=>'200'));
                }else{
                    session('connect', $connect['connect_id']);
                    if($data['type']=='wx') {
                        /*
                        cookie('access', $connect['connect_id']);
                        $back_url = cookie('wx_back_url');
                        $back_url = $back_url ? $back_url :U('index/index') ;
                        header("Location:".$back_url);
                        */
                    }
                    $bind = 1;
                }
            } else {
                $users = $this->setuid($connect['uid'],$user_token);
                if($data['type']=='wx') {
                    /*
                    cookie('access', $connect['connect_id']);
                    $back_url = cookie('wx_back_url');
                    $back_url = $back_url ? $back_url :U('index/index') ;
                    header("Location:".$back_url);
                    */
                }
                
            }
            $this->stringify(array('status'=>'200','bind'=>$bind,'user_token'=>$user_token,'user_info'=>$users));
            die;
        }
    }



	public function logout() {
        D('Passport')->logout();
        $this->stringify(array('status'=>self::BAO_REQUEST_SUCCESS,'msg'=>'退出登录成功！'));
    }

    /**
     *
     */

    public function login() {
//        var_dump(clearUid());exit;
        if(getUid()){
            $data = array('status' => self::BAO_LOGIN_ALREADY ,'msg' =>'您已经登录了,不要重复登录!' );
            $this->stringify($data);
        }
        
        if ($this->isPost()) {
           
            if(!$account = $this->_post('account')){
                $data = array('status' => self::BAO_LOGIN_ACCOUNT_ERROR,'msg' =>'请输入用户名！' );
                $this->stringify($data);
            }
            if(!$password = $this->_post('password')){
                $data = array('status' => self::BAO_LOGIN_PSWD_ERROR,'msg' =>'请输入登录密码！' );
                $this->stringify($data);
            }

            $passport = D('Passport');
            if (true == $passport->login($account, $password)) {
//                $token     = $passport->getToken();
                $user_info = $passport->getUserInfo();
//                $data = array('status' => self::BAO_LOGIN_SUCCESS,'msg' =>'登录成功！','user_token'=>$token,'user_info'=>$user_info);
                $data = array('status' => self::BAO_LOGIN_SUCCESS,'msg' =>'登录成功！','user_info'=>$user_info);
                $this->stringify($data);
            } else {
                $data = array('status' => self::BAO_LOGIN_ERROR ,'msg' => D('Passport')->getError() );
                $this->stringify($data);
            }
        }
    }
//
//    public function record(){
//        if ($this->isPost()) {
//            $user_id = htmlspecialchars($this->_post('user_id'));
//            $app_type = htmlspecialchars($this->_post('app_type'));
//            $data['user_id'] = $user_id;
//            $user = D('Users')->where($data)->find();
//            if(!$user){
//                $data = array('status'=> self::BAO_DB_ERROR,'msg'=>D('Users')->getError());
//                $this->stringify($data);
//            }else{
//                $data['user_id'] = $user_id;
//                $data['app_type'] = $app_type;
//                $ret = M('app_user')->add($data);
//                $data = array('status'=>self::BAO_REQUEST_SUCCESS);
//                $this->stringify($data);
//            }
//        }else{
//            $data = array('status'=>self::BAO_DB_ERROR);
//            $this->stringify($data);
//        }
//    }


	public function newpwd() {
		$yzm = $this->_param('yzm');
		$account = $this->_param('account');
        if (empty($account)) {
            session('verify', null);
			$data = array('status' => self::BAO_INPUT_ERROR, 'msg'=>"请输入用户名!");
        }else if(!$user = D('Users')->getUserByAccount($account)){
			 session('verify', null);
			 $data = array('status'=>self::BAO_USER_NOT_EXISTS,'msg'=>'用户不存在!');
		}else{
			$way = $this->_param('way');
			$password = rand_string(8, 1);
			switch ($way) {
				case 1:
					$email = $this->_param('email');
					if (empty($email) || $email != $user['email']) {
						$data = array('status'=>self::BAO_INPUT_ERROR,'msg'=>'邮件不正确!');
					}else{
						D('Passport')->uppwd($user['account'], '', $password);
						D('Email')->sendMail('email_newpwd', $email, '重置密码', array('newpwd' => $password));
						$data = array('status'=>self::BAO_REQUEST_SUCCESS ,'msg'=>'重置密码成功!');
					}
                    break;
				default:
					$mobile = $this->_param('mobile');
					if (empty($mobile) || $mobile != $user['mobile']) {
						$data = array('status'=>self::BAO_INPUT_ERROR,'msg'=>'手机号码不正确!');
					}else{
						D('Passport')->uppwd($user['account'], '', $password);
						D('Sms')->sendSms('sms_newpwd', $mobile, array('newpwd' => $password));
						$data = array('status'=>self::BAO_REQUEST_SUCCESS ,'msg'=>'重置密码成功！');
					}
                    break;
			}
		}
		$this->stringify($data);
    }


    /**
     *发型师端修改密码
     * GET
     */
    public function changepwd(){
        if(getUid()){
            $data = array('status' => self::BAO_LOGIN_ALREADY ,'msg' =>'您已经登录了,不要重复登录!' );
            $this->stringify($data);
        }

        //手机号
        $account = $this->_get('account');
        //短信验证码
        $ycode = $this->_get('ycode');
        //新密码
        $newpassword = $this->_get('newpassword');

        if (empty($account)) {
            $data = array('status' => self::BAO_INPUT_ERROR, 'msg'=>"请输入手机号!");
            $this->stringify($data);
        }

//        if($ycode != session('ycode')){
//            $data = array('status'=>self::BAO_SCODE_ERROR,'msg'=>'校验码不正确');
//            $this->stringify($data);
//        }


        if(!$user = D('Huser')->where('account='.$account)->find()){
            $data = array('status'=>self::BAO_USER_NOT_EXISTS,'msg'=>'用户不存在!');
            $this->stringify($data);
        }

        $password = array(
            'password' => md5($newpassword)
        );
        $result = D('Huser')->where('account='.$account)->save($password);
        if($result){
            $data = array('status' => self::BAO_REQUEST_SUCCESS ,'msg' =>'修改成功' );
            $this->stringify($data);
        }

    }

    /**
     *发型师端 修改头像
     * POST
     */
    public function changeheadpic(){
        if(!getUid()){
            $data = array('status' => self::BAO_REG_NO_FIND,'result' =>'尚未登录');
            $this->stringify($data);
        }
        if($_SERVER['REQUEST_METHOD']=='GET'){
            $data = array('status'=>self::BAO_REQUEST_METHOD_ERROR,'msg'=>'请求方式错误!');
            $this->stringify($data);
        }
        $headpic = $this->_post('headpics');
        if(!$headpic){
            $data = array('status'=>self::BAO_DETAIL_NO_EXSITS,'msg'=>'图片不存在!');
            $this->stringify($data);
        }
        $result = R('App/Upload/uploadImgs',array('headimg'=>$headpic));

        if($result){
            $datas = array(
                'header_pic' => $result
            );
            $res = D('Huser')->where('huser_id='.getUid())->save($datas);
            if($res){
                $data = array('status' => self::BAO_REQUEST_SUCCESS ,'msg' =>'修改成功' );
                $this->stringify($data);
            }else{

                $data = array('status' => self::BAO_EDIT_FALSE ,'msg' =>'修改失败' );
                $this->stringify($data);
            }

        }
    }

    /**
     *发型师端修改姓名
     * GET
     */
    public function changeName(){
        if(!getUid()){
            $data = array('status' => self::BAO_REG_NO_FIND,'result' =>'尚未登录');
            $this->stringify($data);
        }
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $data = array('status'=>self::BAO_REQUEST_METHOD_ERROR,'msg'=>'请求方式错误!');
            $this->stringify($data);
        }
        $name = $this->_get('name');
        if(!$name){
            $data = array('status'=>self::BAO_DETAIL_NO_EXSITS,'msg'=>'请输入姓名!');
            $this->stringify($data);
        }
        $datas = array(
            'name' => $name
        );

        $result = D('Huser')->where('Huser_id='.getUid())->save($datas);
        if($result){
            $data = array('status' => self::BAO_REQUEST_SUCCESS ,'msg' =>'修改成功' );
            $this->stringify($data);
        }else{
            $data = array('status' => self::BAO_EDIT_FALSE ,'msg' =>'修改失败' );
            $this->stringify($data);
        }

    }


    /**
     *发型师端修改性别
     * GET
     */
    public function changeSex(){
        if(!getUid()){
            $data = array('status' => self::BAO_REG_NO_FIND,'result' =>'尚未登录');
            $this->stringify($data);
        }
        $sex = $this->_get('sex');
        $datas = array(
            'sex' => $sex
        );

        $result = D('Huser')->where('Huser_id='.getUid())->save($datas);
        if($result){
            $data = array('status' => self::BAO_REQUEST_SUCCESS ,'msg' =>'修改成功' );
            $this->stringify($data);
        }else{
            $data = array('status' => self::BAO_EDIT_FALSE ,'msg' =>'修改失败' );
            $this->stringify($data);
        }

    }



}
