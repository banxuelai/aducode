<?php
/**
 * 配置相关
 * @author bxl@gmail.com
 * @date 2017-12-27
 *
 */
class OperationController extends AuthController
{
    //信息确认点
    public function confirm()
    {
        $confirm_model = new ConfirmModel();
        if ($this->req->method == 'POST') {
            $province = trim($this->req->post('province'));
            $city = trim($this->req->post('city'));
            $district = trim($this->req->post('district'));
            
            //校验
            if ($province == '省份') {
                throw new Exception("请选择省份~");
            }
           
            if ($city == '地级市') {
                throw new Exception("请选择地级市~");
            }
            
            if ($district == '区县') {
                throw new Exception("请选择区县~");
            }
            
            $data = array(
                'province' => $province,
                'city' => $city,
                'district' => $district,
                'admin_name' => $this->getUserName(),
                'create_time' => time(),
            );
            
            $id = $confirm_model->insertOne($data);
        }
        $re = $confirm_model->getList(array('status' => 1), -1);
        $this->display('operation/confirm.html', array(
                'title' => '信息确认点',
                'nickname' => $this->getUserName(),
                'lists' => $re['rows'],
                'menu' => 'operation',
                'sub' => 'confirm',
        ));
    }
    
    //报考层次
    public function arrange()
    {
        $operation_model = new OperationModel();
        
        if ($this->req->method == 'POST') {
            $title = trim($this->req->post('title'));
            
            //校验
            if (!$title) {
                throw new Exception("名称不能为空~");
            }
            
            if (!preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $title)) {
                throw new Exception("请输入三字汉语~");
            }
            
            $data = array(
                'title' => $title,
                'type' => 'arrange',
                'create_time' => time(),
                'admin_name' => $this->getUserName(),
            );
            
            $id = $operation_model->insertOne($data);
            $this->success();
        }
        
        //获取列表
        $re = $operation_model->getList(array('status' => 1,'type' => 'arrange'), -1);
        $this->display('operation/arrange.html', array(
                'title' => '报考层次',
                'nickname' => $this->getUserName(),
                'lists' => $re['rows'],
                'menu' => 'operation',
                'sub' => 'arrange',
        ));
    }
    
    //专业类别
    public function professType()
    {
        $operation_model = new OperationModel();
        
        if ($this->req->method == 'POST') {
            $title = trim($this->req->post('title'));
            $fees = $this->req->post('fees');
            //校验
            if (!$title) {
                throw new Exception("名称不能为空~");
            }
        
            if (!preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $title)) {
                throw new Exception("请输入三字汉语~");
            }
            
            if (!$fees) {
                throw new Exception("金额不能为空~");
            }
            
            if (!preg_match('/^[1-9]\d*/', $fees)) {
                throw new Exception("金额必须为数字~");
            }
            
            $data = array(
                    'title' => $title,
                    'fees' => $fees,
                    'type' => 'professType',
                    'create_time' => time(),
                    'admin_name' => $this->getUserName(),
            );
        
            $id = $operation_model->insertOne($data);
            $this->success();
        }
        
        //获取列表
        $re = $operation_model->getList(array('status' => 1,'type' => 'professType'), -1);
        $this->display('operation/professType.html', array(
                'title' => '专业类别',
                'nickname' => $this->getUserName(),
                'lists' => $re['rows'],
                'menu' => 'operation',
                'sub' => 'professType',
        ));
    }
    
    //学校配置
    public function school()
    {
        $this->display('operation/school.html', array(
                'title' => '学校配置',
                'nickname' => $this->getUserName(),
                'menu' => 'operation',
                'sub' => 'school',
        ));
    }
    
    //添加
    public function addschool()
    {
        $operation_model = new OperationModel();
        
        if ($this->req->method == 'POST') {
            $type = intval($this->req->post('type'));
            $title = trim($this->req->post('title'));
            
            if (!$type) {
                throw new Exception("请选择报考层次~");
            }
            
            if (!$title) {
                throw new Exception("学校名称不能为空~");
            }
            
            if (!preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){4,15}$/', $title)) {
                throw new Exception("学校名称格式不正确~");
            }
            
            $data = array(
                'title' => $title,
                'parent_id' => $type,
                'type' => 'school',
                'create_time' => time(),
                'admin_name' => $this->getUserName(),
            );
            
            $id = $operation_model->insertOne($data);
            $this->success();
        }
        $re = $operation_model->getList(array('status' => 1,'type' => 'arrange'), -1);
        $this->display('operation/addschool.html', array(
                'title' => '学校配置',
                'nickname' => $this->getUserName(),
                'lists' => $re['rows'],
                'menu' => 'operation',
                'sub' => 'school',
        ));
    }
    
    //专业配置
    public function profess()
    {
        $this->display('operation/profess.html', array(
                'title' => '专业配置',
                'nickname' => $this->getUserName(),
                'menu' => 'operation',
                'sub' => 'profess',
        ));
    }
    
    //添加
    public function addprofess()
    {
        $this->display('operation/addprofess.html', array(
                'title' => '专业配置',
                'nickname' => $this->getUserName(),
                'menu' => 'operation',
                'sub' => 'profess',
        ));
    }
    
    //删除
    public function del()
    {
        
        $id = intval($this->req->gpc('id'));
        $agent_model = new AgentModel();
        
        //权限验证
        $uid = $this->getUidbySess();
        $admin_agent = $agent_model->getRow(array('id' => $id));
        
        if (!isset($admin_agent) || $admin_agent['uid'] != $uid) {
            throw new Exception("没有删除权限~");
        }
        
        $agent_model->updateOne(array('status' => -1), array('id' => $id));
        
        $this->success();
    }
}
