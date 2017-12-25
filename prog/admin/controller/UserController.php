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
}
