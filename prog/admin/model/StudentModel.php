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
        $sql = " SELECT a.*,b.*  $from_str  ";
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
}
