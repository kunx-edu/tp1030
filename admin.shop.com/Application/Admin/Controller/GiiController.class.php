<?php

namespace Admin\Controller;

/**
 * 脚手架工具，生成通用的控制器、模型、视图
 *
 * @author kunx
 */
class GiiController extends \Think\Controller {

    /**
     * 生成控制器
     */
    public function buildController() {
        //1.如果用户提交了表单
        if (IS_POST) {
            //1.1引入控制器模板，替换特定字符串
            $name        = I('post.name');
            $module      = I('post.module');
            $title       = I('post.title');
            $tpl_file    = MODULE_PATH . 'View/Gii/controller.tpl';
            $content     = file_get_contents($tpl_file);
            $content     = str_replace(array('%name%', '%title%', '%module%'), array($name, $title, $module), $content);
            $content     = "<?php \r\n" . $content;
            $output_path = APP_PATH . $module . '/Controller/';
            $output_file = $output_path . $name . 'Controller.class.php';
            if (!is_dir($output_path)) {
                mkdir($output_path, 0777, true);
            }
            file_put_contents($output_file, $content);
            $this->assign(array('name' => $name, 'module' => $module, 'title' => $title));
        }
        $this->display('build_controller');
    }

    /**
     * 创建统一模型文件
     */
    public function buildModel() {
        //1.如果用户提交了表单
        if (IS_POST) {
            $model      = M('goods_type');
            $table_info = $model->query('SHOW FULL COLUMNS FROM goods_type');
            $rules = array();
            foreach ($table_info as $info) {
                $comment = $info['comment'];
                //1.根据类型设置规则
                if ($info['key'] == 'PRI') { //主键不加规则
                    continue;
                }
                if ($info['key'] == 'UNI') { //唯一键添加不能重复的规则
                    $rules[] = "array('{$info['field']}','','{$comment}不能重复',self::EXISTS_VALIDATE,'unique')";
                }
                if ($info['null'] == 'NO') { //如果字段有not null要求，就添加必填的规则
                    $rules[] = "array('{$info['field']}','require','{$comment}不能为空')";
                }
                if (strpos($info['type'], 'enum') !== false) {
                    $tmp = explode("','", $info['type']);
                    $values = range(1, count($tmp));
                    $values = var_export($values,true);
                    $rules[] = "array('{$info['field']}',$values,'{$comment}不合法',self::MUST_VALIDATE,'in')";
                }
                if (strpos($info['type'], 'set') !== false) {
                    $tmp = explode("','", $info['type']);
                    $tmp_arr = array();
                    for($i=0,$len=count($tmp);$i<$len;++$i){
                        $tmp_arr[] = pow(2, $i);
                    }
                    $values = var_export($tmp_arr,true);
                    $rules[] = "array('{$info['field']}',$values,'{$comment}不合法',self::MUST_VALIDATE,'in')";
                }
            }
            $rules = implode(",\r\n\t", $rules);
//            $rules = "\t" . $rules;
//            echo $rule;
//            exit;
//            dump($rules);
//            exit;
            //1.1引入控制器模板，替换特定字符串
            $name        = I('post.name');
            $module      = I('post.module');
            $title       = I('post.title');
            $tpl_file    = MODULE_PATH . 'View/Gii/model.tpl';
            $content     = file_get_contents($tpl_file);
            $content     = str_replace(array('%name%', '%title%', '%module%','%rules%'), array($name, $title, $module,$rules), $content);
            $content     = "<?php \r\n" . $content;
            $output_path = APP_PATH . $module . '/Model/';
            $output_file = $output_path . $name . 'Model.class.php';
            if (!is_dir($output_path)) {
                mkdir($output_path, 0777, true);
            }
            file_put_contents($output_file, $content);
            $this->assign(array('name' => $name, 'module' => $module, 'title' => $title));
        }
        $this->display('build_model');
    }

}
