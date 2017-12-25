<?php
/**
 * 用户相关
 * @author bxl@gmail.com
 * @date 2017-12-25
 *
 */
class UserModel extends Model
{

    public function __construct()
    {
        parent::__construct();
        $this->db = Db::mysql(Config::mysql('default'));
        $this->table = 'user';
    }
}
