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
        
        $uid = $this->getUidbySess();
        
        
        if ($this->req->method == 'POST') {
            $name = trim($this->req->post('name'));
            $agent_id = intval($this->req->post('agent_id'));
            $gender = trim($this->req->post('gender'));
            $phone = $this->req->post('phone');
            $ethnic = trim($this->req->post('ethnic'));
            $ID_num = $this->req->post('ID_num');
            $province = trim($this->req->post('province'));
            $city = trim($this->req->post('city'));
            $district = trim($this->req->post('district'));
            
            $confirm_id = intval($this->req->post('confirm_id'));
            $arrange = intval($this->req->post('arrange'));
            $professType = intval($this->req->post('professType'));
            $school = intval($this->req->post('school'));
            $profess = intval($this->req->post('profess'));
            $entryFee = intval($this->req->post('entryFee'));
            $fees = intval($this->req->post('fees'));
            $extra = trim($this->req->post('extra'));
            
            //student 基础信息
            $data = array(
                'agent_id' => $agent_id,
                'name' => $name,
                'gender' => $gender,
                'phone' => $phone,
                'ethnic' => $ethnic,
                'ID_num' => $ID_num,
                'province' => $province,
                'city' => $city,
                'district' => $district,
                'create_time' => time(),
            );
            //附加信息
            $extra_data = array(
                    'confirm_id' => $confirm_id,
                    'arrange' => $arrange,
                    'professType' => $professType,
                    'school' => $school,
                    'profess' => $profess,
                    'entryFee' => $entryFee,
                    'fees' => $fees,
                    'extra' => $extra,
            );
            //校验
            $this->check($data, $extra_data);
            $id = $student_model->insertOne($data);
            if ($id) {
                $extra_data['student_id'] = $id;
            }
            $student_model->insertOne($extra_data, 'student_extra');
            $this->success();
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
    private function check($data, $extra_data)
    {
        if (!$data['agent_id']) {
            throw new Exception("请选择二级代理~");
        }
        
        if (!$data['name']) {
            throw new Exception("学员姓名不能为空~");
        }
        
        if (!preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $data['name'])) {
            throw new Exception("姓名格式不正确~");
        }
        
        if (!$data['phone']) {
            throw new Exception("手机号不能为空~");
        }
        
        if (!preg_match('/0?(13|14|15|17|18|19)[0-9]{9}/', $data['phone'])) {
            throw new Exception("手机号格式不正确~");
        }
        
        if (!$data['ethnic']) {
            throw new Exception("民族不能为空~");
        }
        
        if (!preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){1,9}$/', $data['ethnic'])) {
            throw new Exception("民族格式不正确~");
        }
        
        if (!$data['ID_num']) {
            throw new Exception("身份证号不能为空~");
        }
        
        if (!preg_match('/^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/', $data['ID_num'])) {
            throw new Exception("身份证号不正确~");
        }
        
        if ($data['province'] == '省份') {
            throw new Exception("请选择省份~");
        }
         
        if ($data['city'] == '地级市') {
            throw new Exception("请选择地级市~");
        }
        
        if ($data['district'] == '区县') {
            throw new Exception("请选择区县~");
        }
        
        if (!$extra_data['confirm_id']) {
            throw new Exception("请选择信息确认点~");
        }
        
        if (!$extra_data['arrange']) {
            throw new Exception("请选择报考层次~");
        }
        
        if (!$extra_data['school']) {
            throw new Exception("请选择学校~");
        }
        
        if (!$extra_data['profess']) {
            throw new Exception("请选择专业~");
        }
        
        if (!$extra_data['fees']) {
            throw new Exception("学费不能为空~");
        }
    }
}
