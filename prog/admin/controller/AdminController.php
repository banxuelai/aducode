<?php
/**
 * 登录
 * @author bxl@gmail.com
 * @date 2017-12-12
 *
 */
class AdminController extends Controller
{
    //登录
    public function login()
    {
        $back_url = $this->req->get('back_url');
        
        if ($this->req->method == 'POST') {
            $nickname = trim($this->req->post('nickname'));
            $password = trim($this->req->post('password'));
            $back_url = $back_url ? $back_url : Config::site('base_url');
            
            $name = $this->checkLogin($nickname, $password);
            session_regenerate_id(true);
            Session::set('aducode', array(
            'login_time' => time(),
            'active_time' => time(),
            'nickname' => $nickname,
            'name' => $name));
            //$this->redirect("/?back_url=$back_url");
            $this->success($back_url);
        }
        
        $this->display('admin/login.html', array(
                'back_url' => $back_url,
                'title' => '登陆',
        ));
    }
    
    //退出登录
    public function logout()
    {
        Session::set('aducode', '');
        session_destroy();
        header("Location: /admin/login.html");
    }
    
    //校验用户名和密码
    private function checkLogin($nickname, $password)
    {
        if (!$nickname || !$password) {
            throw new Exception("请输入用户名和密码～");
        }
        $user_model = new UserModel();
        $user_info = $user_model->getRow(array('nickname' => $nickname));
        if (!$user_info || !password_verify($password, $user_info['password'])) {
            throw new Exception("用户名或密码错误～");
        }
        return $user_info['name'];
    }
    
    
    public function test()
    {
        $nickname = "chenxiaolong";
        $user_model = new UserModel();
        $user_info = $user_model->getRow(array('nickname' => $nickname));
        print_r($user_info);
    }
}
