<?php


class  AppointAction extends CommonAction{
	 private $create_fields = array('shop_id','cate_id','city_id','lng','lat','price', 'title', 'intro', 'unit','gongju', 'photo','thumb', 'user_name','user_mobile', 'biz_time','end_date','contents');
    private $edit_fields = array('shop_id','cate_id','city_id','lng','lat','price', 'title', 'intro', 'unit','gongju', 'photo','thumb', 'user_name','user_mobile', 'biz_time','end_date','contents');
    
    public function index(){
        $Appoint = D('Appoint');
        import('ORG.Util.Page'); 
        $map = array('closed' => 0);
        $keyword = $this->_param('keyword', 'htmlspecialchars');
		
        if ($keyword) {
            $map['title|intro'] = array('LIKE', '%' . $keyword . '%');
            $this->assign('keyword', $keyword);
        }  
		
        if ($cate_id = (int) $this->_param('cate_id')) {
            $map['cate_id'] = array('IN', D('Appointcate')->getChildren($cate_id));
            $this->assign('cate_id', $cate_id);
        }
		
        $count = $Appoint->where($map)->count(); 
        $Page = new Page($count, 10); 
        $show = $Page->show(); 
        $list = $Appoint->where($map)->order(array('appoint_id' => 'asc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();
		
		$shop_ids = array();
        foreach ($list as $key => $val) {
            $shop_ids[$val['shop_id']] = $val['shop_id'];
        }		
		
        $this->assign('list', $list); 
		$this->assign('cates', D('Appointcate')->fetchAll());
        $this->assign('page', $show); 
        $this->assign('shops', D('Shop')->itemsByIds($shop_ids));
        $this->display(); 
    }

	//添加家政
	public function create() {
        if ($this->isPost()) {
            $data = $this->createCheck();
            $obj = D('Appoint');
            if ($obj->add($data)) {
                $this->baoSuccess('添加成功', U('Appoint/index'));
            }
            $this->baoError('操作失败！');
        } else {
            $this->assign('cates', D('Appointcate')->fetchAll());
            $this->display();
        }
    }
	//添加验证
	 private function createCheck() {
        $data = $this->checkFields($this->_post('data', false), $this->create_fields);
		
			$data['shop_id'] = (int) $data['shop_id'];//商家ID
			if (empty($data['shop_id'])) {
            $this->baoError('请您选择商家');
     	    } 
			$shop = D('Shop')->find($data['shop_id']);
			if (empty($shop)) {
				$this->baoError('请选择正确的商家');
			}
			$data['city_id'] = $shop['city_id'];
			$data['area_id'] = $shop['area_id'];
			$data['business_id'] = $shop['business_id'];
			$data['lng'] = $shop['lng'];
			$data['lat'] = $shop['lat'];
        	$data['cate_id'] = (int) $data['cate_id'];//ID
			if (empty($data['cate_id'])) {
				$this->baoError('类型ID不能为空');
			}
			$Appointcate = D('Appointcate')->where(array('cate_id' => $data['cate_id']))->find();
			$parent_id = $Appointcate['parent_id'];
			if ($parent_id == 0) {
			$this->baoError('请选择二级分类');
			}
			
            $data['title'] = htmlspecialchars($data['title']);
			if (empty($data['title'])) {
            $this->baoError('请您填写服务标题');
     	    }
			if ($words = D('Sensitive')->checkWords($data['title'])) {
            $this->baoError('标题内容含有敏感词：' . $words);
			}
			$data['intro'] = htmlspecialchars($data['intro']);//标题名字
			if (empty($data['intro'])) {
            $this->baoError('请您填写服务建议');
     	    }
			if ($words = D('Sensitive')->checkWords($data['intro'])) {
				$this->baoError('服务建议含有敏感词：' . $words);
			}
            $data['price'] = (int)($data['price'] * 100);
			if (empty($data['price'])) {
            $this->baoError('价格不能为空');
            }
            $data['unit']  = htmlspecialchars($data['unit']);
            $data['gongju']  = htmlspecialchars($data['gongju']);
			$data['user_name'] = htmlspecialchars($data['user_name']);
			if (empty($data['user_name'])) {
            $this->baoError('请您填写姓名');
     	    }
			$data['user_mobile'] = htmlspecialchars($data['user_mobile']);
			if (empty($data['user_mobile'])) {
            $this->baoError('请您手机号码');
     	    }
			if (!isPhone($data['user_mobile']) && !isMobile($data['user_mobile'])) {
            $this->baoError('联系电话格式不正确');
            }
			
            $data['photo']  = htmlspecialchars($data['photo']);
			if (empty($data['photo'])) {
            $this->baoError('请您上传图片');
            }
			
			$thumb = $this->_param('thumb', false);
			foreach ($thumb as $k => $val) {
				if (empty($val)) {
					unset($thumb[$k]);
				}
				if (!isImage($val)) {
					unset($thumb[$k]);
				}
			}
			$data['thumb'] = serialize($thumb);
            $data['biz_time']  = htmlspecialchars($data['biz_time']);
			$data['end_date'] = htmlspecialchars($data['end_date']);
			if (empty($data['end_date'])) {
				$this->baoError('结束时间不能为空');
			}
			if (!isDate($data['end_date'])) {
				$this->baoError('结束时间格式不正确');
			}
			$data['contents'] = SecurityEditorHtml($data['contents']);
			if (empty($data['contents'])) {
            $this->baoError('家政内容不能为空');
			}
			if ($words = D('Sensitive')->checkWords($data['contents'])) {
				$this->baoError('家政简介含有敏感词：' . $words);
			}
			$data['audit'] = 1;
        return $data;
    }
	
	 public function edit($appoint_id = 0){
        if ($appoint_id = (int) $appoint_id) {
            $obj = D('Appoint');
            if (!($detail = $obj->find($appoint_id))) {
                $this->baoError('请选择要编辑的活动');
            }
            if ($this->isPost()) {
                $data = $this->editCheck();
                $data['appoint_id'] = $appoint_id;
                if (false !== $obj->save($data)) {
                    $this->baoSuccess('操作成功', U('appoint/index'));
                }
                $this->baoError('操作失败');
            } else {
                $thumb = unserialize($detail['thumb']);
				$this->assign('thumb', $thumb);
				$this->assign('cates', D('Appointcate')->fetchAll());
				$this->assign('shops', D('Shop')->find($detail['shop_id']));
				$this->assign('detail', $detail);
				$this->display();
            }
        } else {
            $this->baoError('请选择要编辑的家政');
        }
    }
    
	
	 private function editCheck(){
			$data = $this->checkFields($this->_post('data', false), $this->edit_fields);
			$data['shop_id'] = (int) $data['shop_id'];//商家ID
			if (empty($data['shop_id'])) {
            $this->baoError('请您选择商家');
     	    } 
			$shop = D('Shop')->find($data['shop_id']);
			if (empty($shop)) {
				$this->baoError('请选择正确的商家');
			}
			$data['city_id'] = $shop['city_id'];
			$data['area_id'] = $shop['area_id'];
			$data['business_id'] = $shop['business_id'];
			$data['lng'] = $shop['lng'];
			$data['lat'] = $shop['lat'];
        	$data['cate_id'] = (int) $data['cate_id'];//ID
			if (empty($data['cate_id'])) {
				$this->baoError('类型ID不能为空');
			}
			$Appointcate = D('Appointcate')->where(array('cate_id' => $data['cate_id']))->find();
			$parent_id = $Appointcate['parent_id'];
			if ($parent_id == 0) {
			$this->baoError('请选择二级分类');
			}
			
            $data['title'] = htmlspecialchars($data['title']);
			if (empty($data['title'])) {
            $this->baoError('请您填写服务标题');
     	    }
			if ($words = D('Sensitive')->checkWords($data['title'])) {
            $this->baoError('标题内容含有敏感词：' . $words);
			}
			$data['intro'] = htmlspecialchars($data['intro']);//标题名字
			if (empty($data['intro'])) {
            $this->baoError('请您填写服务建议');
     	    }
			if ($words = D('Sensitive')->checkWords($data['intro'])) {
				$this->baoError('服务建议含有敏感词：' . $words);
			}
            $data['price'] = (int)($data['price'] * 100);
			if (empty($data['price'])) {
            $this->baoError('价格不能为空');
            }
            $data['unit']  = htmlspecialchars($data['unit']);
            $data['gongju']  = htmlspecialchars($data['gongju']);
			$data['user_name'] = htmlspecialchars($data['user_name']);
			if (empty($data['user_name'])) {
            $this->baoError('请您填写姓名');
     	    }
			$data['user_mobile'] = htmlspecialchars($data['user_mobile']);
			if (empty($data['user_mobile'])) {
            $this->baoError('请您手机号码');
     	    }
			if (!isPhone($data['user_mobile']) && !isMobile($data['user_mobile'])) {
            $this->baoError('联系电话格式不正确');
            }
			
            $data['photo']  = htmlspecialchars($data['photo']);
			if (empty($data['photo'])) {
            $this->baoError('请您上传图片');
            }
			
			$thumb = $this->_param('thumb', false);
			foreach ($thumb as $k => $val) {
				if (empty($val)) {
					unset($thumb[$k]);
				}
				if (!isImage($val)) {
					unset($thumb[$k]);
				}
			}
			$data['thumb'] = serialize($thumb);
            $data['biz_time']  = htmlspecialchars($data['biz_time']);
			$data['end_date'] = htmlspecialchars($data['end_date']);
			if (empty($data['end_date'])) {
				$this->baoError('结束时间不能为空');
			}
			if (!isDate($data['end_date'])) {
				$this->baoError('结束时间格式不正确');
			}
            $data['contents'] = SecurityEditorHtml($data['contents']);
			if (empty($data['contents'])) {
            $this->baoError('家政内容不能为空');
			}
			if ($words = D('Sensitive')->checkWords($data['contents'])) {
				$this->baoError('家政简介含有敏感词：' . $words);
			}
			$data['audit'] = 1;
        	return $data;
    }
	
   
	public function delete($appoint_id = 0) {
        if (is_numeric($appoint_id) && ($appoint_id = (int) $appoint_id)) {
            $obj = D('Appoint');
            $obj->save(array('appoint_id' => $appoint_id, 'closed' => 1));
            $this->baoSuccess('删除成功！', U('appoint/index'));
        } else {
            $appoint_id = $this->_post('appoint_id', false);
            if (is_array($appoint_id)) {
                $obj = D('Appoint');
                foreach ($appoint_id as $appoint_id) {
                    $obj->save(array('appoint_id' => $id, 'closed' => 1));
                }
                $this->baoSuccess('批量删除成功！', U('appoint/index'));
            }
            $this->baoError('请选择要删除的预约项目');
        }
    }
	public function audit($appoint_id = 0) {
        if (is_numeric($appoint_id) && ($appoint_id = (int) $appoint_id)) {
            $obj = D('Appoint');
            $obj->save(array('appoint_id' => $appoint_id, 'audit' => 1));
            $this->baoSuccess('审核成功！', U('appoint/index'));
        } else {
            $appoint_id= $this->_post('appoint_id', false);
            if (is_array($appoint_id)) {
                $obj = D('Appoint');
                foreach ($appoint_id as $id) {
                    $obj->save(array('appoint_id' => $id, 'audit' => 1));
                }
                $this->baoSuccess('审核成功！', U('appoint/index'));
            }
            $this->baoError('请选择要审核的优惠券');
        }
    }
    
}