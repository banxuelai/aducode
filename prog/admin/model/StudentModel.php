<?php
/**
 * 学员信息
 * @author bxl@gmail.com
 * @date 2017-12-30
 *
 */
class StudentModel extends Model
{

    public function __construct()
    {
        parent::__construct();
        $this->db = Db::mysql(Config::mysql('default'));
        $this->table = 'student';
    }
    
    //获取列表
    public function getList($cond, $offset = 0, $limit = 20)
    {
        $offset = intval($offset);
        $limit = intval($limit);
        $where_str = $this->getWhereStr($cond);
        $from_str = "  FROM `student` a LEFT JOIN student_extra  b ON a.id = b.student_id  $where_str ";
        $count_sql = "SELECT count(*) $from_str ";
        $sql = " SELECT a.*,b.*  $from_str  order by a.id desc  ";
        if ($offset >= 0 && $limit > 0) {
            $sql .= " LIMIT $offset, $limit ";
        }
        $re = array(
                'count' => intval($this->queryFirst($count_sql)),
                'rows' => array(),
        );
        if ($re['count']) {
            $re['rows'] = $this->queryRows($sql);
        }
        return $re;
    }
    
    //获取单条记录
    public function getItem($student_id)
    {
        $student_id = intval($student_id);
        $sql = "SELECT a.*,b.*  FROM `student` a LEFT JOIN student_extra  b ON a.id = b.student_id where a.id = '$student_id'";
        return $this->queryRow($sql);
    }
    
    //获取信息确认点（去重）
    public function studentConfirm()
    {
        $sql = "select c.id,c.province,c.city,c.district from (student as a left join student_extra as b on a.id=b.student_id) left join confirm as c on b.confirm_id=c.id where a.status = 1 and c.status = 1 group by b.confirm_id;";
        return $this->queryRows($sql);
    }
    
    //户籍地去重
    public function studentLocal()
    {
        $sql = "select province,city,district from student where status = 1 group by province,city,district;";
        return $this->queryRows($sql);
    }
    
    //时间去重
    public function studentTime()
    {
        $sql = "select id,create_time from student where status = 1 group by create_time";
        return $this->queryRows($sql);
    }
    
    //民族去重
    public function studentEthnic()
    {
        $sql = "select id,ethnic from student where status = 1 group by ethnic";
        return $this->queryRows($sql);
    }
    
    //民族去重
    public function studentFees()
    {
        $sql = "select b.id,a.all_fees from student_extra as a left join student as b on a.student_id = b.id  where b.status = 1 group by a.all_fees";
        return $this->queryRows($sql);
    }
}
