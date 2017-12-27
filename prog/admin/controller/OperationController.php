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
