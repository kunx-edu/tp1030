<!-- $Id: brand_info.htm 14216 2008-03-10 02:27:21Z testyang $ -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ECSHOP 管理中心 - {$meta_title} </title>
<meta name="robots" content="noindex, nofollow"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="__CSS__/general.css" rel="stylesheet" type="text/css" />
<link href="__CSS__/main.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>
    <span class="action-span"><a href="{:U('index')}">品牌管理</a></span>
    <span class="action-span1"><a href="{:U('Index/main')}">ECSHOP 管理中心</a></span>
    <span id="search_id" class="action-span1"> - {$meta_title} </span>
</h1>
    <div style="clear:both"></div>
<div class="main-div">
    <form method="post" action="{:U()}"enctype="multipart/form-data" >
        <table cellspacing="1" cellpadding="3" width="100%">
            %table%
            <tr>
                <td colspan="2" align="center"><br />
                    <input type="hidden" name="id" value="{$row.id}" />
                    <input type="submit" class="button" value=" 确定 " />
                    <input type="reset" class="button" value=" 重置 " />
                </td>
            </tr>
        </table>
    </form>
</div>

<div id="footer">
共执行 1 个查询，用时 0.018952 秒，Gzip 已禁用，内存占用 2.197 MB<br />
版权所有 &copy; 2005-2012 上海商派网络科技有限公司，并保留所有权利。
</div>
    <js href="__JS__/jquery.min.js" />
    <script type="text/javascript">
        $(function(){
            $('.status').val([{$row.status|default=1}]);//选中指定的选项
        });
    </script>
</body>
</html>