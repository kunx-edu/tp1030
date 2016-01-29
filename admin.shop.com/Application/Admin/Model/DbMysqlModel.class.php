<?php

namespace Admin\Model;

class DbMysqlModel implements DbMysqlInt {

    public function connect() {
        echo __METHOD__ . '<br />';
    }

    public function disconnect() {
        echo __METHOD__ . '<br />';
    }

    public function free($result) {
        echo __METHOD__ . '<br />';
    }

    public function getAll($sql, array $args = array()) {
        echo __METHOD__ . '<br />';
    }

    public function getAssoc($sql, array $args = array()) {
        echo __METHOD__ . '<br />';
    }

    public function getCol($sql, array $args = array()) {
        echo __METHOD__ . '<br />';
    }

    /**
     * 获取一行数据的第一列。
     * @param string $sql sql结构
     * @param array $args 参数，还有更多
     * @return mixed
     */
    public function getOne($sql, array $args = array()) {
        $sql  = $this->buildSql(func_get_args());
        $rows = M()->query($sql);
        return array_shift(array_shift($rows));
    }

    /**
     * 返回一行数据，关联数组
     * @param string $sql
     * @param string $args
     * 有更多参数。
     */
    public function getRow($sql, array $args = array()) {
        $sql  = $this->buildSql(func_get_args());
        $rows = M()->query($sql);
        return array_shift($rows);
    }

    /**
     * 执行插入操作。
     * @param string $sql
     * @param array $args
     * 还有更多的参数。
     * @return int|false 成功返回新增记录的id，失败返回false
     */
    public function insert($sql, array $args = array()) {
        $params     = func_get_args();
        $sql        = $params[0];//得到sql语句
        $table_name = $params[1];//得到数据表名
        $fields = $params[2];//得到要修改数据的键值对
        $sql = str_replace('?T', $table_name, $sql);
        $tmp_sql = array();
        foreach ($fields as $key =>$value){
            if($key=='id'){
                $tmp_sql[] = $key . '=null';
            }else{
                $tmp_sql[] = $key . '="' . $value . '"';
            }
            
        }
        $tmp = implode(',', $tmp_sql);
        $sql = str_replace('?%', $tmp, $sql);
        M()->execute($sql);
        return M()->getLastInsID();
    }

    public function query($sql, array $args = array()) {
        $sql = $this->buildSql(func_get_args());
        return M()->execute($sql);
    }

    public function update($sql, array $args = array()) {
        echo __METHOD__ . '<br />';
    }

    /**
     * 拼凑sql语句，从而得到一个标准SQL
     * @param array $params
     * @return string
     */
    private function buildSql($params) {
        $sql     = array_shift($params);
        $sqls    = preg_split('/\?[FTN]/', $sql); //使用占位拆分成数组，得到sql的区段
        $tmp_sql = '';
        foreach ($sqls as $key => $value) {
            $tmp_sql .= $value . $params[$key];
        }
        return $tmp_sql;
    }

//put your code here
}
