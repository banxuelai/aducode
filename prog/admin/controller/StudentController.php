<?php
/**
 * 学员信息
 * @author bxl@gmail.com
 * @date 2017-12-30
 *
 */
class StudentController extends AuthController
{
    //列表
    public function lists()
    {
        $this->display('student/lists.html', array(
                'title' => '我的录入',
                'nickname' => $this->getUserName(),
                'menu' => 'student',
                'sub' => 'lists',
        ));
    }
    
    //添加
    public function add()
    {
        $student_model = new StudentModel();
        $agent_model = new AgentModel();
        $operation_model = new OperationModel();
        $confirm_model = new ConfirmModel();
        
        if ($this->req->method == 'POST') {
            $uid = $this->getUidbySess();
            $name = trim($this->req->post('name'));
            $agent_id = intval($this->req->post('agent_id'));
            $gender = trim($this->req->post('gender'));
        }
        
        //二级代理
        $agent_info = $agent_model->getList(array('uid' => $uid,'status' => 1), -1);
        //确认点
        $confirm_info = $confirm_model->getList(array('status' => 1), -1);
        foreach ($confirm_info['rows'] as $key => $val) {
            $confirm_info['rows'][$key]['confirm'] = $val['province'].$val['city'].$val['district'];
        }
        //报考层次
        $arrange_info = $operation_model->getList(array('status' => 1,'type' => 'arrange'), -1);
        $this->display('student/add.html', array(
                'title' => '录入信息',
                'agentInfo' => $agent_info['rows'],
                'confirmInfo' => $confirm_info['rows'],
                'arrangeInfo' => $arrange_info['rows'],
                'nickname' => $this->getUserName(),
                'menu' => 'student',
                'sub' => 'add',
        ));
    }
    
    //校验信息
    private function check($data)
    {
    }
}
