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
        $mail->Body    = '注册成功 <b><a href="#">请激活</a></b>';

        if (!$mail->send()) {
            echo '邮件发送失败.';
            echo '错误信息: ' . $mail->ErrorInfo;
        } else {
            echo '邮件发送成功';
        }
    }

}
