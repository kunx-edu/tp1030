<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Model;

/**
 * 描述
 *
 * @author kunx
 */
class MemberModel extends \Think\Model {

    protected $patchValidate = true; //开启批量验证
    protected $_validate     = array(
        array('username', '', '用户名不能重复', self::EXISTS_VALIDATE, 'unique'),
        array('username', 'require', '用户名不能为空'),
        array('password', 'require', '密码不能为空'),
        array('email', '', '邮箱不能重复', self::EXISTS_VALIDATE, 'unique'),
        array('email', 'require', '邮箱不能为空'),
        array('email', 'email', '邮箱不合法'),
        array('repassword', 'password', '两次密码不一致', self::EXISTS_VALIDATE, 'confirm'),
        array('password', '6,16', '密码长度不合法', self::EXISTS_VALIDATE, 'length'),
    );
    protected $_auto = array(
        array('salt', '\Org\Util\String::randString', self::MODEL_INSERT, 'function', array(6)),
    );

    /**
     * 用户添加，并且发送激活邮件
     * @return boolean
     */
    public function createMember() {
        $captcha = new \Think\Verify();
        $code    = I('post.checkcode');
        if ($captcha->check($code)) {
            $this->data['password'] = my_mcrypt($this->data['password'], $this->data['salt']);
            //发送邮件
            $address                = $this->data['email'];
            $subject                = '你还差一步就成功了';
            $username               = $this->data['username'];
            $rand                   = my_mcrypt($username, $this->data['salt']);
            $url                    = U('Member/activation', array('username' => $username, 'rand' => $rand), true, true);
            $content                = <<<mail
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
            if (!$this->add()) {
                $this->error = '用户创建失败';
                return false;
            }
            $data = array('username' => $username, 'rand' => $rand);
            if (!M('MemberActivation')->add($data)) {
                $this->error = '验证串保存失败';
                return false;
            }
            if (!send_mail($address, $subject, $content)) {
                $this->error = '邮件发送失败';
                return false;
            }
            return true;
        } else {
            $this->error = '验证码不正确';
            return false;
        }
    }

    /**
     * 用户激活
     * @param String $username
     * @return type
     */
    public function activationMember($username){
        return $this->where(array('username'=>$username))->setField('status',1);
    }
}
