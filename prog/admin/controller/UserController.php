<?php
/**
 * 用户相关
 * @author bxl@gmail.com
 * @date 2017-12-12
 *
 */
class UserController extends Controller
{
    //修改资料
    public function modify()
    {
        $password = trim($this->req->post('password'));
        if (preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]+$/', $password)) {
            throw new Exception("密码必须由字母和数字组成~");
        }
        if (strlen($password) < 6) {
            throw new Exception("密码长度不能少于6位~");
        }
    }
    
    //添加用户
    public function add()
    {
        $this->display('user/add.html', array(
                'title' => '添加用户',
        ));
    }
    
    //用户列表
    public function lists()
    {
        $this->display('user/lists.html', array(
                'title' => '用户列表',
        ));
    }
}
