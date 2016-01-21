<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
header('Content-Type: text/html;charset=utf-8');
$reg    = '/(.+)@(text|radio|textarea)@?(.*)/';
$items  = array(
    '状态@radio@1=启用&0=隐藏',
    '名字',
    '类别@text',
);
$fields = array();
$html   = '';
foreach ($items as $key => $item) {
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
    $html .= tag($field['type'], $field['title'], 'test' . $key, $field['param']);
}

echo $html;

function tag($type, $title, $field, $params = array()) {
    echo $type . '<br />';
    $html = '';
    switch ($type) {
        case 'text':
            $html .= <<<HTML
                <tr>
                        <td class="label">$title</td>
                        <td>
                            <input type="text" name="$field" maxlength="60" value="{\$row.$field}" />
                        </td>
                    </tr>
HTML;
            break;
        case 'radio':
            $html .= "<tr>
                        <td class='label'>$title</td>
                        <td>";
            foreach ($params as $key => $value) {
                $html.="<input type='radio' name='$field' value='$key'/>$value ";
            }
            $html .= '</td>
                    </tr>';
            break;
        case 'textarea':
            $html .= "<textarea name='$field'>{\$row.$field}</textarea>";
            break;
    }
    return $html;
}

//  如果有分隔符，就获取各个部分，中间部分表示使用什么标签
//  最后部分是附加信息，用在状态可选值中