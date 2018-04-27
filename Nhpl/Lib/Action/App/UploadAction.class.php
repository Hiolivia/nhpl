<?php

class  UploadAction extends  CommonAction{

    //调用云存储
    public function superUpload(){
        import('ORG.Net.Upload');
        $upinfo = M("uploadset")->where("status = 1")->find();
        if($upinfo['type'] != 'Local') {
            $conf = json_decode($upinfo['para'], true);
            $superup = new Upload(array('exts'=>'jpeg,jpg,gif,png'), $upinfo['type'], $conf);
            $upres = $superup->upload();
            echo $upres['Filedata']['url'];
            exit;
        }
    }

    public function upload() {

        $model = $this->_get('model');
        import('ORG.Net.UploadFile');
        $upload = new UploadFile(); //
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $name = date('Y/m/d', NOW_TIME);
        $dir = BASE_PATH . '/attachs/' . $name . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $upload->savePath = $dir; // 设置附件上传目录
        if (isset($this->_CONFIG['attachs'][$model]['thumb'])) {
            $upload->thumb = true;
            if (is_array($this->_CONFIG['attachs'][$model]['thumb'])) {
                $prefix = $w = $h = array();
                foreach ($this->_CONFIG['attachs'][$model]['thumb'] as $k => $v) {
                    $prefix[] = $k . '_';
                    list($w1, $h1) = explode('X', $v);
                    $w[] = $w1;
                    $h[] = $h1;
                }
                $upload->thumbPrefix = join(',', $prefix);
                $upload->thumbMaxWidth = join(',', $w);
                $upload->thumbMaxHeight = join(',', $h);
            } else {
                $upload->thumbPrefix = 'thumb_';
                list($w, $h) = explode('X', $this->_CONFIG['attachs'][$model]['thumb']);
                $upload->thumbMaxWidth = $w;
                $upload->thumbMaxHeight = $h;
            }
        }
        if (!$upload->upload()) {// 上传错误提示错误信息
            $this->error($upload->getErrorMsg());
        } else {// 上传成功 获取上传文件信息
            $info = $upload->getUploadFileInfo();
            if (!empty($this->_CONFIG['attachs']['water'])) {
                import('ORG.Util.Image');
                $Image = new Image();
                $Image->water(BASE_PATH . '/attachs/' . $name . '/thumb_' . $info[0]['savename'], BASE_PATH . '/attachs/' . $this->_CONFIG['attachs']['water']);
            }
            if ($upload->thumb) {
                echo $name . '/thumb_' . $info[0]['savename'];
            } else {
                echo $name . '/' . $info[0]['savename'];
            }
        }
        die;
    }

    public function uploads() {

        $model = $this->_get('model');
        import('ORG.Net.UploadFile');
        $upload = new UploadFile(); //
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $name = date('Y/m/d', NOW_TIME);
        $dir = BASE_PATH . '/attachs/default/' . $name . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $upload->savePath = $dir; // 设置附件上传目录
        if (isset($this->_CONFIG['attachs'][$model]['thumb'])) {
            $upload->thumb = true;
            if (is_array($this->_CONFIG['attachs'][$model]['thumb'])) {
                $prefix = $w = $h = array();
                foreach ($this->_CONFIG['attachs'][$model]['thumb'] as $k => $v) {
                    $prefix[] = $k . '_';
                    list($w1, $h1) = explode('X', $v);
                    $w[] = $w1;
                    $h[] = $h1;
                }
                $upload->thumbPrefix = join(',', $prefix);
                $upload->thumbMaxWidth = join(',', $w);
                $upload->thumbMaxHeight = join(',', $h);
            } else {
                $upload->thumbPrefix = 'thumb_';
                list($w, $h) = explode('X', $this->_CONFIG['attachs'][$model]['thumb']);
                $upload->thumbMaxWidth = $w;
                $upload->thumbMaxHeight = $h;
            }
        }
        if (!$upload->upload()) {// 上传错误提示错误信息
            $this->error($upload->getErrorMsg());
        } else {// 上传成功 获取上传文件信息
            $info = $upload->getUploadFileInfo();
            if (!empty($this->_CONFIG['attachs']['water'])) {
                import('ORG.Util.Image');
                $Image = new Image();
                $Image->water(BASE_PATH . '/attachs/default/' . $name . '/thumb_' . $info[0]['savename'], BASE_PATH . '/attachs/default/' . $this->_CONFIG['attachs']['water']);
            }
            if ($upload->thumb) {
                echo $name . '/thumb_' . $info[0]['savename'];
            } else {
                echo $name . '/' . $info[0]['savename'];
            }
        }
        die;
    }

    public function uploadify() {
        //$yun = $this->superUpload();
        $model = $this->_get('model');
        import('ORG.Net.UploadFile');
        $upload = new UploadFile(); //
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $name = date('Y/m/d', NOW_TIME);
        $dir = BASE_PATH . '/attachs/' . $name . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $upload->savePath = $dir; // 设置附件上传目录
        if (isset($this->_CONFIG['attachs'][$model]['thumb'])) {
            $upload->thumb = true;
            if (is_array($this->_CONFIG['attachs'][$model]['thumb'])) {
                $prefix = $w = $h = array();
                foreach($this->_CONFIG['attachs'][$model]['thumb'] as $k=>$v){
                    $prefix[] = $k.'_';
                    list($w1,$h1) = explode('X', $v);
                    $w[]=$w1;
                    $h[]=$h1;
                }
                $upload->thumbPrefix = join(',',$prefix);
                $upload->thumbMaxWidth =join(',',$w);
                $upload->thumbMaxHeight =join(',',$h);
            } else {
                $upload->thumbPrefix = 'thumb_';
                list($w, $h) = explode('X', $this->_CONFIG['attachs'][$model]['thumb']);
                $upload->thumbMaxWidth = $w;
                $upload->thumbMaxHeight = $h;
            }
        }
        if (!$upload->upload()) {// 上传错误提示错误信息
            var_dump($upload->getErrorMsg());
        } else {// 上传成功 获取上传文件信息
            $info = $upload->getUploadFileInfo();
            if(!empty($this->_CONFIG['attachs']['water'])){
                import('ORG.Util.Image');
                $Image = new Image();
                $Image->water(BASE_PATH . '/attachs/'. $name . '/thumb_' . $info[0]['savename'],BASE_PATH . '/attachs/'.$this->_CONFIG['attachs']['water']);
            }
            if($upload->thumb){
                echo $name . '/thumb_' . $info[0]['savename'];
            }else{
                echo $name . '/' . $info[0]['savename'];
            }
        }
    }

    /*新增plupload上传插件-cyheng-2017.10.10*/
    public function plupload() {
        if ($this->isPost()) {
            $app=I('post.app/s','');
            if(!in_array($app, array('hotel','user','weixin','editor','default'))){
                $app='default/';
            }else{
                $app= strtolower($app).'/';
            }

            //$model = $this->_get('model');
            $model = 'hotel';
            import('ORG.Net.UploadFile');
            $upload = new UploadFile(); //
            $upload->maxSize = 3145728; // 设置附件上传大小
            $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
            $name = $app . date('Y/m/d', NOW_TIME);
            $dir = BASE_PATH . '/attachs/' . $name . '/';
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $upload->savePath = $dir; // 设置附件上传目录
            if (isset($this->_CONFIG['attachs'][$model]['thumb'])) {
                $upload->thumb = false;
                if (is_array($this->_CONFIG['attachs'][$model]['thumb'])) {
                    $prefix = $w = $h = array();
                    foreach($this->_CONFIG['attachs'][$model]['thumb'] as $k=>$v){
                        $prefix[] = $k.'_';
                        list($w1,$h1) = explode('X', $v);
                        $w[]=$w1;
                        $h[]=$h1;
                    }
                    $upload->thumbPrefix = join(',',$prefix);
                    $upload->thumbMaxWidth =join(',',$w);
                    $upload->thumbMaxHeight =join(',',$h);
                } else {
                    $upload->thumbPrefix = 'thumb_';
                    list($w, $h) = explode('X', $this->_CONFIG['attachs'][$model]['thumb']);
                    $upload->thumbMaxWidth = $w;
                    $upload->thumbMaxHeight = $h;
                }
            }
            if (!$upload->upload()) {// 上传错误提示错误信息
                var_dump($upload->getErrorMsg());
            } else {// 上传成功 获取上传文件信息
                $info = $upload->getUploadFileInfo();
                if(!empty($this->_CONFIG['attachs']['water'])){
                    import('ORG.Util.Image');
                    $Image = new Image();
                    $Image->water(BASE_PATH . '/attachs/'. $name . '/thumb_' . $info[0]['savename'],BASE_PATH . '/attachs/'.$this->_CONFIG['attachs']['water']);
                }
                if($upload->thumb){
                    $thumb_url = '/attachs/'. $name . '/thumb_' . $info[0]['savename'];
                    $data = array("status" => 1,"url" => $thumb_url, "preview_url" => $thumb_url, "filepath" => $thumb_url, "name" => $info[0]['savename']);
                    echo json_encode($data);//返回array
                }else{
                    $url = '/attachs/'. $name . '/' . $info[0]['savename'];
                    $data = array("status" => 1,"url" => $url, "preview_url" => $url, "filepath" => $url, "name" => $info[0]['savename']);
                    echo json_encode($data);//返回array
                }
            }exit;
        }else{
            $filetypes=array(
                'image'=>array('title'=>'Image files','extensions'=>'jpg,jpeg,png,gif'),
                'video'=>array('title'=>'Video files','extensions'=>'mp4,avi'),
                'audio'=>array('title'=>'Audio files','extensions'=>'mp3'),
                'file'=>array('title'=>'Custom files','extensions'=>'txt,doc')
            );

            $filetype = I('get.filetype/s','image');
            $mime_type=array();
            if(array_key_exists($filetype, $filetypes)){
                $mime_type=$filetypes[$filetype];
            }else{
                $this->baoError('上传文件类型配置错误！');
            }

            $multi=I('get.multi',0,'intval');
            $app=I('get.app','default');
            $upload_max_filesize = 10240;

            $this->assign('extensions',$filetypes[$filetype]['extensions']);
            $this->assign('upload_max_filesize',$upload_max_filesize);
            $this->assign('upload_max_filesize_mb',intval($upload_max_filesize/1024));
            $this->assign('mime_type',json_encode($mime_type));
            $this->assign('multi',$multi);
            $this->assign('app',$app);

            $this->display(":plupload");
        }
    }

    public function editor() {
        import('ORG.Net.UploadFile');
        $upload = new UploadFile(); //
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $name = date('Y/m/d', NOW_TIME);
        $dir = BASE_PATH . '/attachs/editor/' . $name . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $upload->savePath = $dir; // 设置附件上传目录

        if (isset($this->_CONFIG['attachs']['editor']['thumb'])) {
            $upload->thumb = true;
            $upload->thumbPrefix = 'thumb_';
            $upload->thumbType = 0; //不自动裁剪
            list($w, $h) = explode('X', $this->_CONFIG['attachs']['editor']['thumb']);
            $upload->thumbMaxWidth = $w;
            $upload->thumbMaxHeight = $h;
        }
        if (!$upload->upload()) {// 上传错误提示错误信息
            var_dump($upload->getErrorMsg());
        } else {// 上传成功 获取上传文件信息
            $info = $upload->getUploadFileInfo();

            if(!empty($this->_CONFIG['attachs']['editor']['water'])){
                import('ORG.Util.Image');
                $Image = new Image();

                $Image->water(BASE_PATH . '/attachs/editor/'. $name . '/thumb_' . $info[0]['savename'],BASE_PATH . '/attachs/'.$this->_CONFIG['attachs']['water']);
            }
            $return = array(
                'url' => $name . '/thumb_' . $info[0]['savename'],
                'originalName' => $name . '/thumb_' . $info[0]['savename'],
                'name' => $name . '/thumb_' . $info[0]['savename'],
                'state' => 'SUCCESS',
                'size' => $info['size'],
                'type' => $info['extension'],
            );
            echo json_encode($return);
        }
    }


    public function shangjia() {
        $shop_id = (int)$this->_get('shop_id');
        $sig  = $this->_get('sig');
        if(empty($shop_id) || empty($sig)) die;
        $sign = md5($shop_id.C('AUTH_KEY'));
        if($sign != $sig) die;
        import('ORG.Net.UploadFile');
        $upload = new UploadFile(); //
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $name = date('Y/m/d', NOW_TIME);
        $dir = BASE_PATH . '/attachs/' . $name . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $upload->savePath = $dir; // 设置附件上传目录

        if (isset($this->_CONFIG['attachs']['shopphoto']['thumb'])) {
            $upload->thumb = true;
            $upload->thumbPrefix = 'thumb_';
            list($w, $h) = explode('X', $this->_CONFIG['attachs']['shopphoto']['thumb']);
            $upload->thumbMaxWidth = $w;
            $upload->thumbMaxHeight = $h;
        }
        if (!$upload->upload()) {// 上传错误提示错误信息
            $this->error($upload->getErrorMsg());
        } else {// 上传成功 获取上传文件信息
            $info = $upload->getUploadFileInfo();
            if(!empty($this->_CONFIG['attachs']['shopphoto']['water'])){
                import('ORG.Util.Image');
                $Image = new Image();
                $Image->water(BASE_PATH . '/attachs/'. $name . '/thumb_' . $info[0]['savename'],BASE_PATH . '/'.$this->_CONFIG['attachs']['water']);
            }
            if($upload->thumb){
                $photo = $name . '/thumb_' . $info[0]['savename'];
            }else{
                $photo =  $name . '/' . $info[0]['savename'];
            }
            $data = array(
                'shop_id' => $shop_id,
                'photo' => $photo,
                'create_time' => NOW_TIME,
                'create_ip' => get_client_ip(),
            );
            D('Shoppic')->add($data);
        }
        echo 1;
    }
    public function shopbanner() {
        $shop_id = (int)$this->_get('shop_id');
        $sig  = $this->_get('sig');
        if(empty($shop_id) || empty($sig)) die;
        $sign = md5($shop_id.C('AUTH_KEY'));
        if($sign != $sig) die;
        import('ORG.Net.UploadFile');
        $upload = new UploadFile(); //
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $name = date('Y/m/d', NOW_TIME);
        $dir = BASE_PATH . '/attachs/' . $name . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $upload->savePath = $dir; // 设置附件上传目录

        if (isset($this->_CONFIG['attachs']['shopbanner']['thumb'])) {
            $upload->thumb = true;
            $upload->thumbPrefix = 'thumb_';
            list($w, $h) = explode('X', $this->_CONFIG['attachs']['shopbanner']['thumb']);
            $upload->thumbMaxWidth = $w;
            $upload->thumbMaxHeight = $h;
        }
        if (!$upload->upload()) {// 上传错误提示错误信息
            $this->error($upload->getErrorMsg());
        } else {// 上传成功 获取上传文件信息
            $info = $upload->getUploadFileInfo();
            if(!empty($this->_CONFIG['attachs']['shopbanner']['water'])){
                import('ORG.Util.Image');
                $Image = new Image();
                $Image->water(BASE_PATH . '/attachs/'. $name . '/thumb_' . $info[0]['savename'],BASE_PATH . '/'.$this->_CONFIG['attachs']['water']);
            }
            if($upload->thumb){
                $photo = $name . '/thumb_' . $info[0]['savename'];
            }else{
                $photo =  $name . '/' . $info[0]['savename'];
            }
            $data = array(
                'shop_id' => $shop_id,
                'photo' => $photo,
                'is_mobile'=>1,
                'create_time' => NOW_TIME,
                'create_ip' => get_client_ip(),
            );
            D('Shopbanner')->add($data);
        }
        echo 1;
    }
    public function shopbanner1() {
        $shop_id = (int)$this->_get('shop_id');
        $sig  = $this->_get('sig');
        if(empty($shop_id) || empty($sig)) die;
        $sign = md5($shop_id.C('AUTH_KEY'));
        if($sign != $sig) die;
        import('ORG.Net.UploadFile');
        $upload = new UploadFile(); //
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $name = date('Y/m/d', NOW_TIME);
        $dir = BASE_PATH . '/attachs/' . $name . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $upload->savePath = $dir; // 设置附件上传目录

        if (isset($this->_CONFIG['attachs']['shopbanner1']['thumb'])) {
            $upload->thumb = true;
            $upload->thumbPrefix = 'thumb_';
            list($w, $h) = explode('X', $this->_CONFIG['attachs']['shopbanner1']['thumb']);
            $upload->thumbMaxWidth = $w;
            $upload->thumbMaxHeight = $h;
        }
        if (!$upload->upload()) {// 上传错误提示错误信息
            $this->error($upload->getErrorMsg());
        } else {// 上传成功 获取上传文件信息
            $info = $upload->getUploadFileInfo();
            if(!empty($this->_CONFIG['attachs']['shopbanner1']['water'])){
                import('ORG.Util.Image');
                $Image = new Image();
                $Image->water(BASE_PATH . '/attachs/'. $name . '/thumb_' . $info[0]['savename'],BASE_PATH . '/'.$this->_CONFIG['attachs']['water']);
            }
            if($upload->thumb){
                $photo = $name . '/thumb_' . $info[0]['savename'];
            }else{
                $photo =  $name . '/' . $info[0]['savename'];
            }
            $data = array(
                'shop_id' => $shop_id,
                'photo' => $photo,
                'is_mobile'=>0,
                'create_time' => NOW_TIME,
                'create_ip' => get_client_ip(),
            );
            D('Shopbanner')->add($data);
        }
        echo 1;
    }


    /**
     * $headimg  base64编码图片上传(多图之间用 . 隔开)
     */
    public function uploadImg($headimg){
        $path = '';
        $name = date('Y/m/d', NOW_TIME);
        $dir = BASE_PATH . '/attachs/' . $name . '/';

        if (!is_dir($dir)) {
            @mkdir(iconv("UTF-8", "GBK", $dir), 0777, true);
        }

        if(strpos($headimg,'.')){
            $headimg = explode('.',$headimg);
        }

        if(is_array($headimg)){
            //多图上传
            foreach($headimg as $value){

                preg_match('/^(data:\s*image\/(\w+);base64,)/', $value, $result);
                $value = str_replace($result[0], "", $value);

                $value = base64_decode($value);

                $filename = md5(rand(1, 99999999) . time()) . ".jpg";//要生成的图片名字
                $file = fopen($dir . $filename, "w");//打开文件准备写入
                fwrite($file, $value);//写入
                fclose($file);//关闭

                $path .= '/attachs/' . $name . '/'.$filename . ',';
            }

            $path = substr($path, 0, -1);
        }else {
            //单图上传
            preg_match('/^(data:\s*image\/(\w+);base64,)/', $headimg, $result);
            $headimg = str_replace($result[0], "", $headimg);

            $tmp = base64_decode($headimg);
            $filename = md5(rand(1, 99999999) . time()) . ".jpg";//要生成的图片名字
            $file = fopen($dir . $filename, "w");//打开文件准备写入
            fwrite($file, $tmp);//写入
            fclose($file);//关闭

            $path = '/attachs/' . $name . '/'.$filename;
        }

        return $path;
    }


}