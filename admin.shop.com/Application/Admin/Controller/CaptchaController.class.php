<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;

/**
 * 描述
 *
 * @author kunx
 */
class CaptchaController extends \Think\Controller{
    public function captcha(){
        $setting = array(
            'length'=>4,
        );
        $obj = new \Think\Verify($setting);
        $obj->entry();
    }
}
