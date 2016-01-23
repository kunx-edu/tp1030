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
        C('SHOW_PAGE_TRACE', FALSE);
        $data = array(
            'status' => 1,
            'msg'    => '上传成功',
            'file'   => array(), //保存上传后的文件信息
//            'file_path'=>'http://img13.360buyimg.com/n7/g14/M0A/1B/1E/rBEhVVM01w0IAAAAAAFL2bSSMlgAAK81AA0aEwAAUvx453.jpg',
        );
//        echo json_encode($data);

        /**
         * 具体执行上文件的保存操作
         */
        $config = array(
//            'maxSize'  => 3145728, //3M
            'maxSize'  => 30, //3M
//            'savePath' => C('UPLOAD_PATH'),
            'rootPath' => './',
            'savePath' => 'Uploads/',
            'saveName' => array('uniqid', ''),
            'exts'     => array('jpg', 'gif', 'png', 'jpeg'),
            'autoSub'  => true,
            'subName'  => array('date', 'Ymd'),
            'mimes'    => array('image/jpeg', 'image/png', 'image/gif'), //允许上传的文件MiMe类型
        );
        $upload = new \Think\Upload($config);
        $file   = $upload->upload();
        if ($file) {
            $data['file'] = $file;
        }else{
            $data = array(
                'status'=>0,
                'msg'=>$upload->getError(),
            );
        }
//        dump($upload->getError());
        echo json_encode($data);
    }

}
