<?php

namespace Admin\Controller;

/**
 * 脚手架工具，生成通用的控制器、模型、视图
 *
 * @author kunx
 */
class GiiController extends \Think\Controller {

    public function index(){
        if(IS_POST){
            $this->buildController();
            $this->buildModel();
            $this->buildView();
        }else{
            
            $this->display('build_model');
        }
    }
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
//        $this->display('build_controller');
    }

    /**
     * 创建统一模型文件
     */
    public function buildModel() {
        //1.如果用户提交了表单
//        if (IS_POST) {
            //1.1引入控制器模板，替换特定字符串
            $name       = I('post.name');
            $module     = I('post.module');
            $title      = I('post.title');
            $model      = M();
            $table_info = $model->query('SHOW FULL COLUMNS FROM ' . parse_name($name, 0));
            $rules      = array();
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
                    $tmp     = explode("','", $info['type']);
                    $values  = range(1, count($tmp));
                    $values  = var_export($values, true);
                    $rules[] = "array('{$info['field']}',$values,'{$comment}不合法',self::MUST_VALIDATE,'in')";
                }
                if (strpos($info['type'], 'set') !== false) {
                    $tmp     = explode("','", $info['type']);
                    $tmp_arr = array();
                    for ($i = 0, $len = count($tmp); $i < $len; ++$i) {
                        $tmp_arr[] = pow(2, $i);
                    }
                    $values  = var_export($tmp_arr, true);
                    $rules[] = "array('{$info['field']}',$values,'{$comment}不合法',self::MUST_VALIDATE,'in')";
                }
            }
            $rules = implode(",\r\n\t", $rules);

            $tpl_file    = MODULE_PATH . 'View/Gii/model.tpl';
            $content     = file_get_contents($tpl_file);
            $content     = str_replace(array('%name%', '%title%', '%module%', '%rules%'), array($name, $title, $module, $rules), $content);
            $content     = "<?php \r\n" . $content;
            $output_path = APP_PATH . $module . '/Model/';
            $output_file = $output_path . $name . 'Model.class.php';
            if (!is_dir($output_path)) {
                mkdir($output_path, 0777, true);
            }
            file_put_contents($output_file, $content);
            $this->assign(array('name' => $name, 'module' => $module, 'title' => $title));
//        }
//        $this->display('build_model');
    }

    public function buildView() {
//        if (IS_POST) {
            //1.1引入控制器模板，替换特定字符串
            $name       = I('post.name');
            $module     = I('post.module');
            $title     = I('post.title');
            $model      = M();
            $table_info = $model->query('SHOW FULL COLUMNS FROM ' . parse_name($name, 0));
            $thead      = '';
            $tbody      = '';
            $reg    = '/(.+)@(text|radio|textarea)@?(.*)/';
            
            //列表页面的，每个单元格模板
            $index_tr_str = "\t<td align='center'>%title%</td>\r\n";
            foreach ($table_info as $info) {
                if (!$info['comment']) {
                    continue;
                }
                
                //拼凑列表页所需要的tbody
                $tbody_index .= str_replace('%title%',"{\$row.{$info['field']}}", $index_tr_str);
                
                //表格主体部分
                $item  = $info['comment'];
                $field = array(
                    'title' => '',
                    'type'  => 'text',
                    'param' => array(),
                );
//正则匹配，每行记录，检查是否有@，如果没有找到，说明只有[字段描述]
                if (strpos($item, '@') === false) {
                    $field['title'] = $item;
                    $field['type']  = 'text';
                } else {
                    preg_match_all($reg, $item, $tmp); //tmp就保存了每行的所有的匹配结果
                    $field['title'] = $tmp[1][0];
                    $field['type']  = $tmp[2][0];
                    if (!$tmp[3][0]) {
                        
                    } else {
                        parse_str($tmp[3][0], $field['param']);
                    }
                }
                //表头部分
                $thead .= '<th>' . $field['title'] . '</th>' ."\r\n";
                
                $tbody .= $this->tag($field['type'], $field['title'], $info['field'], $field['param']);
            }
            
            $tpl_file    = MODULE_PATH . 'View/Gii/edit.tpl';
            $content     = file_get_contents($tpl_file);
            $content     = str_replace(array('%table%'), array($tbody), $content);
            $output_path = APP_PATH . $module . '/View/'.$name . '/';
            $output_file = $output_path . 'edit.html';
//            echo $output_file;
            if (!is_dir($output_path)) {
                mkdir($output_path, 0777, true);
            }
            file_put_contents($output_file, $content);
            
            
            
            //生成首页
            $tpl_file    = MODULE_PATH . 'View/Gii/index.tpl';
            $content     = file_get_contents($tpl_file);
            $tbody_index = "\t<tr>" . $tbody_index . "</tr>\r\n";
            $content     = str_replace(array('%title%','%thead%','%tbody%'), array($title,$thead,$tbody_index), $content);
            $output_path = APP_PATH . $module . '/View/'.$name . '/';
            $output_file = $output_path . 'index.html';
            if (!is_dir($output_path)) {
                mkdir($output_path, 0777, true);
            }
            file_put_contents($output_file, $content);
//        } else {
//            $this->display('build_view');
//        }
    }

    private function tag($type, $title, $field, $params = array()) {
        $html = '';
        switch ($type) {
            case 'text':
                $html .= <<<HTML
                <tr>
                        \t<td class="label">$title</td>
                        \t<td>
                            \t<input type="text" name="$field" maxlength="60" value="{\$row.$field}" />
                        \t</td>
                    </tr>
HTML;
                break;
            case 'radio':
                $html .= "<tr>
                        \t<td class='label'>$title</td> \r\n
                        <td>";
                foreach ($params as $key => $value) {
                    $html.="<input type='radio' name='$field' value='$key'/>$value ";
                }
                $html .= "</td> \r\n
                    </tr> \r\n";
                break;
            case 'textarea':
                $html .= "\t<textarea name='$field'>{\$row.$field}</textarea> \r\n";
                break;
        }
        return $html;
    }

}
