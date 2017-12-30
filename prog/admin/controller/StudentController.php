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
        }
        
        //二级代理
        //确认点
        //报考层次
        
        $this->display('student/add.html', array(
                'title' => '录入信息',
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
