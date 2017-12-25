<?php
/**
 * 用户相关
 * @author bxl@gmail.com
 * @date 2017-12-12
 *
 */
class UserController extends Controller
{
    //登录
    public function login()
    {
        $this->display('user/login.html', array(
                'title' => '登陆',
        ));
    }
    
    //退出登录
    public function logout()
    {
        Session::set('aducode', '');
        session_destroy();
        header("Location: /user/login.html");
    }
}
