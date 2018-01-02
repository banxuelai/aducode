<?php
/**
 * 代理相关
 * @author bxl@gmail.com
 * @date 2017-12-27
 *
 */
class AgentController extends AuthController
{

    //添加代理
    public function add()
    {
        $agent_model = new AgentModel();
        if ($this->req->method == 'POST') {
            $name = trim($this->req->post('name'));
            $phone = trim($this->req->post('phone'));
            $uid = $this->getUidbySess();
            
            //check 重复
            $info = $agent_model->getRow(array('name' => $name,'uid' => $uid));
            if ($info) {
                throw new Exception("该代理已存在");
            }
            $data = array(
                'name' => $name,
                'phone' => $phone,
                'uid' => intval($uid),
                'create_time' => time(),
            );
            
            //校验
            $this->check($data);
            $id = $agent_model->insertOne($data);
        }
        
        $this->display('agent/add.html', array(
                'title' => '添加代理',
                'nickname' => $this->getUserName(),
                'menu' => 'agent',
                'sub' => 'add',
                'type' => $this->getTypebyUid(),
        ));
    }
    
    //代理列表
    public function lists()
    {
        $agent_model = new AgentModel();
        $cond = array(
            'status' => 1,
            'uid' => $this->getUidbySess(),
        );
        
        $re = $agent_model->getList($cond, -1);
        $view = array(
                'title' => '代理列表',
                'lists' => $re['rows'],
                'nickname' => $this->getUserName(),
                'menu' => 'agent',
                'sub' => 'lists',
                'type' => $this->getTypebyUid(),
        );
        $this->display('agent/lists.html', $view);
    }
    
    //校验
    private function check($data)
    {
        
        if (!$data['name']) {
            throw new Exception("姓名不能为空~");
        }
        
        if (!$data['phone']) {
            throw new Exception("手机号不能为空~");
        }
        
        if (!preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $data['name'])) {
            throw new Exception("姓名格式不正确~");
        }
        
        if (!preg_match('/0?(13|14|15|17|18|19)[0-9]{9}/', $data['phone'])) {
            throw new Exception("手机号格式不正确~");
        }
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
