<?php

namespace Admin\Controller;

/**
 * 描述
 *
 * @author kunx
 */
class UploadController extends \Think\Controller {

    /**
     * 处理上传的数据，使用json字符串的形式返回
     * 其中status为1，表示上传成功，0，表示上传失败
     * msg：提示信息，如果失败就是失败的原因
     * file：上传成功后的所有的文件信息
     */
    public function index() {
//        dump($_FILES);
        C('SHOW_PAGE_TRACE', FALSE);
        $data = array(
            'status' => 1,
            'msg'    => '上传成功',
            'file'   => array(), //保存上传后的文件信息
//            'file_path'=>'http://img13.360buyimg.com/n7/g14/M0A/1B/1E/rBEhVVM01w0IAAAAAAFL2bSSMlgAAK81AA0aEwAAUvx453.jpg',
        );
//        echo json_encode($data);

        $bucket_array = array(
            'brand'=>'kunx_brand',
            'goods'=>'kunx_goods',
            'default'=>'test1011',
            
        );
        $bucket = isset($bucket_array[I('post.category')])?$bucket_array[I('post.category')]:$bucket_array['default'];
        /**
         * 具体执行上文件的保存操作
         */
        $config = array(
            'maxSize'  => 3145728, //3M
//            'maxSize'      => 30, //3M
//            'savePath' => C('UPLOAD_PATH'),
            'rootPath' => './',
            'savePath'     => 'Uploads/',
            'saveName' => array('uniqid', ''),
            'exts'         => array('jpg', 'gif', 'png', 'jpeg'),
            'autoSub'      => true,
            'subName'      => array('date', 'Ymd'),
            'mimes'        => array('image/jpeg', 'image/png', 'image/gif'), //允许上传的文件MiMe类型
            //又拍云配置
//            'driver'       => 'Upyun',
//            'driverConfig' => array(
//                'host'     => 'v0.api.upyun.com', //又拍云服务器
//                'username' => 'kunx', //又拍云操作员用户名
//                'password' => 'sigexukun', //又拍云操作员密码
//                'bucket'   => 'test1033', //空间名称
//                'timeout'  => 90, //超时时间
//            ),
            //七牛配置
            'driver'       => 'Qiniu',
            'driverConfig' => array(
                'secrectKey' => 'KBYoPnqTbgX4a65rXNI9f-6_kCKwwnHMSnLOGLNk', //七牛服务器
                'accessKey' => 'qJHe4wo24q6X6AWSXsv-syl8PkhHjo6i5WXc-to5', //七牛用户
                'domain'    => '7xnizi.com1.z0.glb.clouddn.com', //七牛密码
//                'bucket'    => 'test1011', //空间名称
                'bucket'    => $bucket, //空间名称
                'timeout'   => 300, //超时时间
            ),
        );
        $upload = new \Think\Upload($config);
        $file   = $upload->uploadOne($_FILES['logo']);
        if ($file) {
            $data['file'] = json_encode($file);
        } else {
            $data = array(
                'status' => 0,
                'msg'    => $upload->getError(),
            );
        }
//        dump($upload->getError());itsource   itsource
        echo json_encode($data);
    }

}
