<?php
/**
 * 根据错误信息数组，拼接无序列表输出错误。
 * @param array $errors 错误数组。
 * @return string
 */
function get_errors(array $errors) {
    $return = '<ul>';
    foreach ($errors as $error) {
        $return .= '<li>' . $error . '</li>';
    }
    $return .= '</ul>';
    return $return;
}
