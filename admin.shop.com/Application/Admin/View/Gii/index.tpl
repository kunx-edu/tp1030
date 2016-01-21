<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>ECSHOP 管理中心 - {$meta_title} </title>
        <meta name="robots" content="noindex, nofollow"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="__CSS__/general.css" rel="stylesheet" type="text/css" />
        <link href="__CSS__/main.css" rel="stylesheet" type="text/css" />
        <css href="__CSS__/page.css" />
    </head>
    <body>
        <h1>
            <span class="action-span"><a href="{:U('patchAdd')}">批量添加品牌</a></span>
            <span class="action-span"><a href="{:U('add')}">添加品牌</a></span>
            <span class="action-span1"><a href="{:U('Index/main')}">ECSHOP 管理中心</a></span>
            <span id="search_id" class="action-span1"> - {$meta_title} </span>
        </h1>
        <div style="clear:both"></div>
        <div class="form-div">
            <form action="{:U('',array('p'=>1))}" name="searchForm">
                <img src="__IMG__/icon_search.gif" width="26" height="22" border="0" alt="search" />
                <input type="text" name="keyword" size="15" value='{$keyword}'/>
                <input type="submit" value=" 搜索 " class="button" />
            </form>
        </div>
            <div class="list-div" id="listDiv">
                <input type="button" value=" 删除 " class="button delete_all"/>
                <table cellpadding="3" cellspacing="1">
                    <tr>
                        %thead%
                    </tr>
                    <tbody class="list-data">
                    <foreach name="rows" item="row">
                    %tbody%
                    </foreach>
                    </tbody>
                    <tr>
                        <td align="right" nowrap="true" colspan="6">
                            <div id="turn-page" class="page">
                                {$page_html}
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

        <div id="footer">
            版权所有 &copy; 2005-2012 上海商派网络科技有限公司，并保留所有权利。
        </div>
        <js href='__JS__/jquery.min.js'/>
        <js href='__EXT__/layer/layer.js'/>
        <script type='text/javascript'>
            $(function(){
            });
        </script>
    </body>