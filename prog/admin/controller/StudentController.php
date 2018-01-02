<?php
/**
 * 学员信息
 * @author bxl@gmail.com
 * @date 2017-12-30
 *
 */
class StudentController extends AuthController
{
    //缴费比例配置
    private $feesConfig = array(
            '1' => 0.4,
            '2' => 0.6,
    );
    
    //缴费状态
    private $feesStatusConfig = array(
            '1' => '未缴',
            '2' => '缴费1',
            '3' => '缴费2',
            '4' => '已缴',
    );
    
    //列表
    public function lists()
    {
        $student_model = new StudentModel();
        $agent_model = new AgentModel();
        $operation_model = new OperationModel();
        $confirm_model = new ConfirmModel();
        $user_model = new UserModel();
        
        $name = trim($this->req->get('name'));
        $agent_uid = intval($this->req->get('uid'));
        $agent_id = intval($this->req->get('agent_id'));
        $school = intval($this->req->get('school'));
        $profess = intval($this->req->get('profess'));
        $fees_status = $this->req->get('fees_status');
        $uid = $this->getUidbySess();
        
        $search = array();
        $search['name'] = $name;
        $search['uid'] = $agent_uid;
        $search['agent_id'] = $agent_id;
        $search['school'] = $school;
        $search['profess'] = $profess;
        $search['fees_status'] = $fees_status;
        
        $page = intval($this->req->get('page'));
        $page_size = max(intval($this->req->get('page_size')), 20);
        empty($page) && $page = 1;
        $offset = ($page - 1) * $page_size;
      
        $cond = array(
            'a.status' => 1,
        );
        
        if ($name) {
            $cond['a.name'] = array('like' => "%$name%");
        }
        //超级管理员特殊处理
        if ($this->getTypebyUid() == 1) {
            if ($agent_uid) {
                $cond['a.uid'] = $agent_uid;
            }
            $uid_list = $user_model->getList(array('status' => 1), -1);
        } else {
            $cond['a.uid'] = $uid;
        }
        
        if ($agent_id) {
            $cond['a.agent_id'] = $agent_id;
        }

        if ($school) {
            $cond['b.school'] = $school;
        }
        
        if ($profess) {
            $cond['b.profess'] = $profess;
        }
        
        if ($fees_status >= 0) {
            $cond['b.fees_status'] = $fees_status;
        }
       
        $re = $student_model->getList($cond, $offset, $page_size);
        
        foreach ($re['rows'] as $key => $val) {
            //二级代理
            $agent_info = $agent_model->getRow(array('status' => 1,'id' => $val['agent_id']));
            $re['rows'][$key]['agent_name'] = $agent_info['name'];
            //学校
            $school_info = $operation_model->getRow(array('status' => 1,'id' => $val['school'],'type' => 'school'));
            $re['rows'][$key]['school_name'] = $school_info['title'];
            //专业
            $profess_info = $operation_model->getRow(array('status' => 1,'id' => $val['profess'],'type' => 'profess'));
            $re['rows'][$key]['profess_name'] = $profess_info['title'];
            //缴费状态
            $re['rows'][$key]['fees_status'] = $this->feesStatusConfig[$val['fees_status']];
        }
        $pageHtml = $this->createPageHtml($this->buildUrl("student/lists.html", $this->req->get()), $re['count'], $page, $page_size);
        
        //二级代理
        $agent_info = $agent_model->getList(array('uid' => $uid,'status' => 1), -1);
        //学校
        $school_info = $operation_model->getList(array('status' => 1,'type' => 'school'), -1);
        //专业
        $profess_info = $operation_model->getList(array('status' => 1,'type' => 'profess'), -1);
        $this->display('student/lists.html', array(
                'title' => '我的录入',
                'pages' => $pageHtml,
                'lists' => $re['rows'],
                'userInfo' => isset($uid_list['rows']) ? $uid_list['rows'] : array(),
                'agentInfo' => $agent_info['rows'],
                'schoolInfo' => $school_info['rows'],
                'professInfo' => $profess_info['rows'],
                'nickname' => $this->getUserName(),
                'search' => $search,
                'type' => $this->getTypebyUid(),
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
                'uid' => $uid,
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
                'type' => $this->getTypebyUid(),
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
    
    //学员信息详情
    public function detail()
    {
        $student_id  = $this->req->get('student_id');
        $student_model = new StudentModel();
        $agent_model = new AgentModel();
        $operation_model = new OperationModel();
        $confirm_model = new ConfirmModel();
        $user_model = new UserModel();
        
        $student_info = $student_model->getItem($student_id);
        
        if ($student_info) {
            //性别
            $student_info['gender'] = $this->getGender($student_info['gender']);
            //二级代理
            $agent_info = $agent_model->getRow(array('status' => 1,'id' => $student_info['agent_id']));
            $student_info['agent_name'] = $agent_info['name'];
            //确认点
            $confirm_info = $confirm_model->getRow(array('status' => 1,'id' => $student_info['confirm_id']));
            $student_info['confirm'] = $confirm_info['province'].$confirm_info['city'].$confirm_info['district'];
            //报考层次
            $arrange_info = $operation_model->getRow(array('status' => 1,'id' => $student_info['arrange'],'type' => 'arrange'));
            $student_info['arrange'] = $arrange_info['title'];
            //学校
            $school_info = $operation_model->getRow(array('status' => 1,'id' => $student_info['school'],'type' => 'school'));
            $student_info['school_name'] = $school_info['title'];
            //专业
            $profess_info = $operation_model->getRow(array('status' => 1,'id' => $student_info['profess'],'type' => 'profess'));
            $student_info['profess_name'] = $profess_info['title'];
           //缴费信息
            $fees1 = $fees2 = 0;
            if ($student_info['fees_status'] == 2) {
                $fees1 = $student_info['fees'] * $this->feesConfig[1];
            }
            
            if ($student_info['fees_status'] == 3) {
                $fees2 = $student_info['fees'] * $this->feesConfig[2];
            }
            
            if ($student_info['fees_status'] == 4) {
                $fees1 = $student_info['fees'] * $this->feesConfig[1];
                $fees2 = $student_info['fees'] * $this->feesConfig[2];
            }
            $student_info['fees1'] = $fees1;
            $student_info['fees2'] = $fees2;
            $student_info['all_fees'] = $fees1 + $fees2;
        }
        
        $this->display('student/detail.html', array(
                'title' => '详情信息',
                'nickname' => $this->getUserName(),
                'menu' => 'student',
                'sub' => 'lists',
                'type' => $this->getTypebyUid(),
                'studentInfo' => $student_info,
        ));
    }
    
    //修改缴费信息
    public function edit()
    {
        $student_id  = $this->req->gpc('student_id');
        $student_model = new StudentModel();
        $agent_model = new AgentModel();
        $operation_model = new OperationModel();
        $confirm_model = new ConfirmModel();
        $user_model = new UserModel();
        
        $student_info = $student_model->getItem($student_id);
        
        if ($this->req->method == 'POST') {
            $fees1 = $this->req->post('fees1');
            $fees2 = $this->req->post('fees2');
            if ($fees1 && $fees2) {
                $fees_status = 4;
            } elseif ($fees1) {
                if ($student_info['fees_status'] == 3) {
                    $fees_status = 4;
                }
                if ($student_info['fees_status'] == 1) {
                    $fees_status = 2;
                }
            } elseif ($fees2) {
                if ($student_info['fees_status'] == 2) {
                    $fees_status = 4;
                }
                if ($student_info['fees_status'] == 1) {
                    $fees_status = 3;
                }
            }
            //更新
            $id = $student_model->updateOne(array('fees_status' => $fees_status,), array('student_id' => $student_id), 'student_extra');
            $this->success();
        }
            
        if ($student_info) {
            //性别
            $student_info['gender'] = $this->getGender($student_info['gender']);
            //二级代理
            $agent_info = $agent_model->getRow(array('status' => 1,'id' => $student_info['agent_id']));
            $student_info['agent_name'] = $agent_info['name'];
            //确认点
            $confirm_info = $confirm_model->getRow(array('status' => 1,'id' => $student_info['confirm_id']));
            $student_info['confirm'] = $confirm_info['province'].$confirm_info['city'].$confirm_info['district'];
            //报考层次
            $arrange_info = $operation_model->getRow(array('status' => 1,'id' => $student_info['arrange'],'type' => 'arrange'));
            $student_info['arrange'] = $arrange_info['title'];
            //学校
            $school_info = $operation_model->getRow(array('status' => 1,'id' => $student_info['school'],'type' => 'school'));
            $student_info['school_name'] = $school_info['title'];
            //专业
            $profess_info = $operation_model->getRow(array('status' => 1,'id' => $student_info['profess'],'type' => 'profess'));
            $student_info['profess_name'] = $profess_info['title'];
            //缴费信息
            $fees1 = $fees2 = 0;
            if ($student_info['fees_status'] == 2) {
                $fees1 = $student_info['fees'] * $this->feesConfig[1];
            }
            
            if ($student_info['fees_status'] == 3) {
                $fees2 = $student_info['fees'] * $this->feesConfig[2];
            }
            
            if ($student_info['fees_status'] == 4) {
                $fees1 = $student_info['fees'] * $this->feesConfig[1];
                $fees2 = $student_info['fees'] * $this->feesConfig[2];
            }
            $student_info['fees1'] = $fees1;
            $student_info['fees2'] = $fees2;
            $student_info['all_fees'] = $fees1 + $fees2;
        }
    
        $this->display('student/edit.html', array(
                'title' => '缴费信息',
                'nickname' => $this->getUserName(),
                'menu' => 'student',
                'sub' => 'lists',
                'type' => $this->getTypebyUid(),
                'studentInfo' => $student_info,
        ));
    }
    
    //获取性别
    private function getGender($xingbie)
    {
        $gender = '你猜';
        switch ($xingbie) {
            case 'm':
                $gender = '男';
                break;
            case 'f':
                $gender = '女';
                break;
            default:
                $gender = '你猜';
        }
        return $gender;
    }
}
