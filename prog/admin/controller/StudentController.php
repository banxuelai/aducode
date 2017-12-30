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
