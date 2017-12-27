<?php
/**
 * 信息确认点
 * @author bxl@gmail.com
 * @date 2017-12-27
 *
 */
class ConfirmModel extends Model
{

    public function __construct()
    {
        parent::__construct();
        $this->db = Db::mysql(Config::mysql('default'));
        $this->table = 'confirm';
    }
}
