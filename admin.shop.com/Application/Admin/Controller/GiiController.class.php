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
            $tpl_file    = MODULE_PATH . 'View/Gii/controller.html';
            $content     = file_get_contents($tpl_file);
            $content     = str_replace(array('%name%', '%title%', '%module%'), array($name, $title, $module), $content);
            $content     = "<?php \r\n". $content;
            $output_path = APP_PATH . $module . '/Controller/';
            $output_file = $output_path . $name . 'Controller.class.php';
            if(!is_dir($output_path)){
                mkdir($output_path, 0777, true);
            }
            file_put_contents($output_file, $content);
            $this->assign(array('name'=>$name,'module'=>$module,'title'=>$title));
        }
        $this->display('build_controller');
    }
    /**
     * 创建统一模型文件
     */
    public function buildModel(){
        
    }

}
