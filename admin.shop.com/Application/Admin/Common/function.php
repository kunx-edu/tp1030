<?php

/**
 * 根据错误信息数组，拼接无序列表输出错误。
 * @param array|string $errors 错误。
 * @return string
 */
function get_errors($errors) {
    if (is_array($errors)) {
        $return = '<ul>';
        foreach ($errors as $error) {
            $return .= '<li>' . $error . '</li>';
        }
        $return .= '</ul>';
        return $return;
    } else {
        return $errors;
    }
}

/**
 * 将数组转换成select下拉列表。
 * @param array $data 关联数据数组
 * @param string $value_field value属性取自哪个字段
 * @param string $name_field option的文案取自哪个字段
 * @param string $name 下拉列表提交时使用的name属性
 * @param string $select_value 默认选中项
 * @return string $html 返回的select html代码
 */
function arr2select(array $data, $value_field, $name_field, $name, $select_value = '') {
    $html = '<select name="' . $name . '">';
    $html .= '<option vaule="-1">请选择...</option>';
    foreach ($data as $value) {
        if ($value[$value_field] == $select_value) {
            $html .= "<option value='{$value[$value_field]}' selected='selected'>{$value[$name_field]}</option>";
        } else {
            $html .= "<option value='{$value[$value_field]}'>{$value[$name_field]}</option>";
        }
    }
    $html .= '</select>';
    return $html;
}

/**
 * 将数组转换为关联数组。
 * array(
 *  0=>array('id'=>3,'name'=>'zhangsan')
 * )
 * 
 * array(
 *  3=>
 *      array('id'=>3,'name'=>'zhangsan')
 * )
 * @param array  $data  多行数据。
 * @param string $field 根据那个字段生成关联数组
 * @return array
 */
function get_data_by_column(array $data, $field) {
    $return = array();
    foreach ($data as $value) {
        $return[$value[$field]] = $value;
    }
    return $return;
}
