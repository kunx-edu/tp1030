<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * 描述
 *
 * @author kunx
 */
class TestController extends \Think\Controller {

    public function send() {
        Vendor('PHPMailer.PHPMailerAutoload');
        $mail = new \PHPMailer;

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host       = 'smtp.126.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                               // Enable SMTP authentication
        $mail->Username   = 'kunx_edu@126.com';                 // SMTP username
        $mail->Password   = 'iam4ge';                           // SMTP password
//        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
//        $mail->Port       = 587;                                    // TCP port to connect to

        $mail->setFrom('kunx_edu@126.com', 'sige');
        $mail->addAddress('kunx-edu@qq.com');     // Add a recipient

        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->CharSet = 'UTF-8';                                  // 邮件内容编码

        $mail->Subject = '你还差一步就成功了';
        $content = <<<mail
<div id="mailContentContainer" class="qmbox qm_con_body_content"><div style="background:#f4f4f4; padding:35px">
<div style="max-width:600px; margin: 20px auto; color:#333333; box-shadow: 0px 0px 12px rgba(0, 0, 0, 0.5); -webkit-box-shadow: 0px 0px 12px rgba(0, 0, 0, 0.5); -moz-box-shadow: 0px 0px 12px rgba(0, 0, 0, 0.5); background-color: #ffffff;border: 1px solid #bbbbbb;">
<div style="border-bottom: 32px solid #348cd6;">
<h1 style="margin: 0; padding: 2px 12px 0px"><a href="http://www.shop.com" target="_blank"><img src="http://t0.qlogo.cn/mbloghead/ee5db3775cb4887dee3e/100" style="height:34px;padding:0;margin:0;border:0;"></a></h1>
</div>

<div style="padding:0px 18px 8px; border-bottom: 2px solid #348cd6;">
<h4 style="font-size: 14px;margin:18px auto;line-height: 2;font-weight: bold;">亲爱的<span style="color: #348cd6;">$username</span> ，您好！感谢 使用仙人跳服务！</h4>

<p style="line-height: 1.7;"><span>请点击下面的激活链接，完成账号的激活操作。</span> <span style="font-weight:bold;"><a href="#">$url</a></span></p>

<p style="font-size: 12px; color: #999; margin: 24px 0px 2px;">（如果您认为这封邮件和您无关，您可以直接忽略它）</p>

<div style="border-top:1px solid #e2e2e2;">
<div style="background-color: #f3f3f2;margin: 14px 0px;text-indent: 2em;">
<p style="padding: 2px 6px;  color: 8c8b8b; font-size: 12px; line-height: 1.66;">仙人跳文化传播有限公司，传播网络正能量，给你人生添色彩！</p>
</div>
</div>
</div>
</div>
</div>
<br><br><div style="width:1px;height:0px;overflow:hidden"><img style="width:0;height:0" src="javascript:;"></div>
<div style="text-align:center"><div style="border-top:1px solid #ddd;width: 600px;display:inline-block;padding:10px"><a style="display:inline-block;background:#ddd;border-radius:4px;padding: 3px 15px;color:#a6a6a6;text-decoration:none;font-size:12px" href="http://scu.sohu.com/track/unsubscribe.do?p=eyJ1c2VyX2lkIjogMjg0OTgsICJ0YXNrX2lkIjogIiIsICJlbWFpbF9pZCI6ICIxNDQ5ODk2MDc0NDUwXzI4NDk4XzE3NDgxXzYyNzcuc2MtMTBfMTBfMjRfMTQyLWluYm91bmQwJGt1bngtZWR1QHFxLmNvbSIsICJzaWduIjogImFmNTI3ZWE5ZTZhNWI0MmFhNjk5YTE2ZmUwZmI0Y2E2IiwgInVzZXJfaGVhZGVycyI6IHt9LCAibGFiZWwiOiAwLCAicmVjZWl2ZXIiOiAia3VueC1lZHVAcXEuY29tIiwgImNhdGVnb3J5X2lkIjogOTExMzd9" target="_blank">点击这里取消订阅</a></div></div>
<style type="text/css">.qmbox style, .qmbox script, .qmbox head, .qmbox link, .qmbox meta {display: none !important;}</style></div>
mail;
        $mail->Body    = '注册成功 <b><a href="#">请激活</a></b>';

        if (!$mail->send()) {
            echo '邮件发送失败.';
            echo '错误信息: ' . $mail->ErrorInfo;
        } else {
            echo '邮件发送成功';
        }
    }

}
