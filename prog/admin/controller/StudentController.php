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
        $school = trim($this->req->get('school'));
        $profess = trim($this->req->get('profess'));
        $arrange = trim($this->req->get('arrange'));
        $fees_status = $this->req->get('fees_status');
        //性别
        $gender = trim($this->req->get('gender'));
        //民族
        $ethnic = trim($this->req->get('ethnic'));
        //户籍点
        $local = $this->req->get('local');
        //确认点
        $confirm_id = intval($this->req->get('confirm_id'));
        //缴费金额
        $all_fees = $this->req->get('all_fees');
        if ($all_fees == '') {
            $all_fees = -1;
        }
        //时间点
        $create_time = $this->req->get('create_time');
        
        $uid = $this->getUidbySess();
        
        list($province,$city,$district) = explode('-', $local);
        
        $search = array();
        $search['name'] = $name;
        $search['uid'] = $agent_uid;
        $search['agent_id'] = $agent_id;
        $search['school'] = $school;
        $search['profess'] = $profess;
        $search['fees_status'] = $fees_status;
        $search['arrange'] = $arrange;
        $search['gender'] = $gender;
        $search['ethnic'] = $ethnic;
        $search['local'] = $local;
        $search['confirm_id'] = $confirm_id;
        $search['all_fees'] = $all_fees;
        $search['create_time'] = $create_time;
        $search['province'] = $province;
        $search['city'] = $city;
        $search['district'] = $district;
        
        
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
        
        if ($fees_status) {
            $cond['b.fees_status'] = $fees_status;
        }
        
        if ($arrange) {
            $cond['b.arrange'] = $arrange;
        }
        
        if ($gender) {
            $cond['a.gender'] = $gender;
        }
        if ($ethnic) {
            $cond['a.ethnic'] = $ethnic;
        }
        
        if ($confirm_id) {
            $cond['b.confirm_id'] = $confirm_id;
        }
          
        
        if ($province) {
            $cond['a.province'] = $province;
        }
        if ($city) {
            $cond['a.city'] = $city;
        }
        if ($district) {
            $cond['a.district'] = $district;
        }
        if ($all_fees >= 0) {
            $cond['b.all_fees'] = $all_fees;
        }
            
        if ($create_time) {
            $time = strtotime($create_time);
            $cond['a.create_time']['>='] = $time;
            $cond['a.create_time']['<'] = $time + 86400;
        }
        $re = $student_model->getList($cond, $offset, $page_size);
        
        foreach ($re['rows'] as $key => $val) {
            $re['rows'][$key] = $this->buildStudentItem($val);
        }
        $pageHtml = $this->createPageHtml($this->buildUrl("student/lists.html", $this->req->get()), $re['count'], $page, $page_size);
        
        //二级代理
        $agent_cond = array(
            'status' => 1,
        );
        if ($this->getTypebyUid() == 0) {
            $agent_cond['uid'] = $uid;
        }
        $agent_info = $agent_model->getList($agent_cond, -1);
        
        $info_uid = $this->getTypebyUid() ? 0 : $this->getUidbySess();
        //学校
        $school_info = $student_model->sdudentSchool($info_uid);
        //专业
        $profess_info = $student_model->sdudentProfess($info_uid);
        //层次
        $arrange_info = $student_model->sdudentArrange($info_uid);
        if ($this->getTypebyUid() == 1) {
            //信息确认点
            $confirm_info = $student_model->studentConfirm();
            //时间
            $time_lists = $student_model->studentTime();
            $tmp = array();
            foreach ($time_lists as $key => $val) {
                $tmp[] = date("Y-m-d", $val['create_time']);
            }
            $time_info = array_unique($tmp);
            //金额
            $fees_info = $student_model->studentFees();
            //户籍地
            $local_info = $student_model->studentLocal();
            //民族
            $ethnic_info = $student_model->studentEthnic();
        }
        $this->display('student/lists.html', array(
                'title' => '我的录入',
                'pages' => $pageHtml,
                'lists' => $re['rows'],
                'userInfo' => isset($uid_list['rows']) ? $uid_list['rows'] : array(),
                'agentInfo' => $agent_info['rows'],
                'schoolInfo' => isset($school_info) ? $school_info : array(),
                'professInfo' => isset($profess_info) ? $profess_info : array(),
                'arrangeInfo' => isset($arrange_info) ? $arrange_info : array(),
                'confirmInfo' => isset($confirm_info) ? $confirm_info : array(),
                'localInfo' => isset($local_info) ? $local_info : array(),
                'timeInfo' => $time_info ? $time_info : array(),
                'ethnicInfo' => isset($ethnic_info) ? $ethnic_info : array(),
                'feesInfo' => isset($fees_info) ? $fees_info : array(),
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
            $arrange_id = intval($this->req->post('arrange'));
            $professType = intval($this->req->post('professType'));
            $school_id = intval($this->req->post('school'));
            $profess_id = intval($this->req->post('profess'));
            $entryFee = intval($this->req->post('entryFee'));
            $fees = intval($this->req->post('fees'));
            $extra = trim($this->req->post('extra'));
            
            $arrange_item = $operation_model->getRow(array('id' => $arrange_id,'status' => 1,'type' => 'arrange'));
            $school_item = $operation_model->getRow(array('id' => $school_id,'status' => 1,'type' => 'school'));
            $profess_item = $operation_model->getRow(array('id' => $profess_id,'status' => 1,'type' => 'profess'));
            
            
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
                    'arrange_id' => $arrange_id,
                    'arrange' => $arrange_item['title'] ? $arrange_item['title'] : '',
                    'professType' => $professType,
                    'school_id' => $school_id,
                    'school' => $school_item['title'] ? $school_item['title'] : '',
                    'profess_id' => $profess_id,
                    'profess' => $profess_item['title'] ? $profess_item['title'] : '',
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
    private function check($data, $extra_data, $type = "add")
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
        
        if ($type == 'add') {
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
            $student_info = $this->buildStudentItem($student_info);
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
    
    //修改学员信息
    public function modify()
    {
        $student_id  = $this->req->gpc('student_id');
        $student_model = new StudentModel();
        $agent_model = new AgentModel();
        $operation_model = new OperationModel();
        $confirm_model = new ConfirmModel();
        $user_model = new UserModel();
        
        $student_info = $student_model->getItem($student_id);
        $item = $this->buildStudentItem($student_info);
                 
        if ($this->req->method == 'POST') {
            //权限判断
            if ($this->getTypebyUid() != 1) {
                throw new Exception("普通用户没有删除权限~");
            }
            
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
                    'update_time' => time(),
            );
            //附加信息
            $extra_data = array(
                    'confirm_id' => $confirm_id,
                    'extra' => $extra,
            );
            //校验
            $this->check($data, $extra_data, 'modify');
            Log::file("pre_info---id({$student_id})--agent_id({$student_info['agent_id']})--name({$student_info['name']})--gender({$student_info['gender']})--phone({$student_info['phone']})--ethnic({$student_info['ethnic']})--ID_num({$student_info['ID_num']}--province({$student_info['province']})--city({$student_info['city']})--district({$student_info['district']})--confirm_id({$student_info['confirm_id']})", 'modifyStudent');
            
            $fees1 = $this->req->post('fees1');
            $fees2 = $this->req->post('fees2');
            $f1 = $f2 = $all_f = 0;
            if ($fees1 && $fees2) {
                $f1 = $student_info['fees'] * $this->feesConfig[1];
                $f2 = $student_info['fees'] * $this->feesConfig[2];
                $all_f = $f1 + $f2;
                $fees_status = 4;
            } elseif ($fees1) {
                $f1 = $student_info['fees'] * $this->feesConfig[1];
                if ($student_info['fees_status'] == 3) {
                    $fees_status = 4;
                    $all_f = $f1 + $student_info['fees2'];
                }
                if ($student_info['fees_status'] == 1) {
                    $fees_status = 2;
                    $all_f = $f1;
                }
            } elseif ($fees2) {
                $f2 = $student_info['fees'] * $this->feesConfig[2];
                if ($student_info['fees_status'] == 2) {
                    $fees_status = 4;
                    $all_f = $f2 + $student_info['fees1'];
                }
                if ($student_info['fees_status'] == 1) {
                    $fees_status = 3;
                    $all_f = $f2;
                }
            }
            if ($f1) {
                $extra_data['fees1'] = $f1;
            }
            if ($f2) {
                $extra_data['fees2'] = $f2;
            }
            if ($all_f) {
                $extra_data['all_fees'] = $all_f;
            }
            if ($fees_status) {
                $extra_data['fees_status'] = $fees_status;
            }
            //更新
            $update_id = $student_model->updateOne($data, array('id' => $student_id), 'student');
            $update_extra_id = $student_model->updateOne($extra_data, array('student_id' => $student_id), 'student_extra');
            if ($update_id&&$update_extra_id) {
                Log::file("student_id({$student_id})--fees({$student_info['fees']})--fees_status({$student_info['fees_status']})--fees1({$student_info['fees1']})--fees2({$student_info['fees2']})--all_fees({$student_info['all_fees']})--new_status({$fees_status})--f1({$f1})--f2({($f2)})--all_f{($all_f)}--editor({$this->getUserName()})", 'editFees');
                 
                Log::file("new_info---id({$student_id})--agent_id({$agent_id})--name({$name})--gender({$gender})--phone({$phone})--ethnic({$ethnic})--ID_num({$ID_num}--province({$province})--city({$city})--district({$district})--confirm_id({$confirm_id})", 'modifyStudent');
            }
            $this->success();
        }
        //二级代理
        $agent_info = $agent_model->getList(array('uid' => $student_info['uid'],'status' => 1), -1);
        //确认点
        $confirm_info = $confirm_model->getList(array('status' => 1), -1);
        foreach ($confirm_info['rows'] as $key => $val) {
            $confirm_info['rows'][$key]['confirm'] = $val['province'].$val['city'].$val['district'];
        }
        
        $this->display('student/modify.html', array(
                'title' => '修改信息',
                'agentInfo' => $agent_info['rows'],
                'confirmInfo' => $confirm_info['rows'],
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
            Log::file("student_id({$student_id})--fees({$student_info['fees']})--fees_status({$student_info['fees_status']})--fees1({$fees1})--fees2({$fees2})--new_status({$fees_status})--editor({$this->getUserName()})", 'editFees');
            
            //更新
            $id = $student_model->updateOne(array('fees_status' => $fees_status,), array('student_id' => $student_id), 'student_extra');
            $this->success();
        }
            
        if ($student_info) {
            $student_info = $this->buildStudentItem($student_info);
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
    
    //删除学员信息
    public function del()
    {
        $student_id = intval($this->req->post('student_id'));
        $uid = $this->getUidbySess();
        if ($this->getTypebyUid() != 1) {
            throw new Exception("普通用户没有删除权限~");
        }
        
        $student_model = new StudentModel();
        
        $update_id = $student_model->updateOne(array('status' => -1,'update_time' => time()), array('id' => $student_id));
        
        if ($update_id) {
            Log::file("del_info---id({$student_id})--editor({$this->getName()})", 'delStudent');
            $this->success();
        }
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
    
    //根据信息获取实际缴费
    private function getFees($student_info, $type = 'all_fees')
    {
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
        if ($type == "fees1") {
            return $fees1;
        } elseif ($type == "fees2") {
            return $fees2;
        } else {
            return $fees1 + $fees2;
        }
    }
    
    //构造学员详细信息
    private function buildStudentItem($student_info)
    {
        $student_model = new StudentModel();
        $agent_model = new AgentModel();
        $operation_model = new OperationModel();
        $confirm_model = new ConfirmModel();
        $user_model = new UserModel();
        
        //性别
        $student_info['gender'] = $this->getGender($student_info['gender']);
        //二级代理
        $agent_info = $agent_model->getRow(array('status' => 1,'id' => $student_info['agent_id']));
        $student_info['agent_name'] = $agent_info['name'];
        //确认点
        $confirm_info = $confirm_model->getRow(array('status' => 1,'id' => $student_info['confirm_id']));
        $student_info['confirm'] = $confirm_info['province'].$confirm_info['city'].$confirm_info['district'];
        //报考层次
/*         $arrange_info = $operation_model->getRow(array('status' => 1,'id' => $student_info['arrange'],'type' => 'arrange'));
        $student_info['arrange'] = $arrange_info['title'];
        //学校
        $school_info = $operation_model->getRow(array('status' => 1,'id' => $student_info['school'],'type' => 'school'));
        $student_info['school_name'] = $school_info['title'];
        //专业
        $profess_info = $operation_model->getRow(array('status' => 1,'id' => $student_info['profess'],'type' => 'profess'));
        $student_info['profess_name'] = $profess_info['title']; */
        //缴费信息
/*         $student_info['fees1'] = $this->getFees($student_info, 'fees1');
        $student_info['fees2'] = $this->getFees($student_info, 'fees2');
        $student_info['all_fees'] = $this->getFees($student_info, 'all_fees'); */
        
        //缴费状态
        $student_info['fees_status'] = $this->feesStatusConfig[$student_info['fees_status']];
        //时间
        $student_info['create_time'] = date("Y-m-d H:i:s", $student_info['create_time']);
         
        return $student_info;
    }
}
