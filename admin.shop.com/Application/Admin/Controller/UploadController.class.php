<?php

namespace Admin\Controller;

/**
 * 描述
 *
 * @author kunx
 */
class UploadController extends \Think\Controller {

    public function index() {
        C('SHOW_PAGE_TRACE',FALSE);
//        $data = array(
//            'status'=>0,
//            'file_path'=>'http://img13.360buyimg.com/n7/g14/M0A/1B/1E/rBEhVVM01w0IAAAAAAFL2bSSMlgAAK81AA0aEwAAUvx453.jpg',
//        );
//        echo json_encode($data);

        /**
         * 具体执行上文件的保存操作
         */
        $config = array(
            'maxSize'  => 3145728,
//            'savePath' => C('UPLOAD_PATH'),
            'rootPath'=>'./',
            'savePath'=>'Uploads/',
            'saveName' => array('uniqid', ''),
            'exts'     => array('jpg', 'gif', 'png', 'jpeg'),
            'autoSub'  => true,
            'subName'  => array('date', 'Ymd'),
        );
        $upload = new \Think\Upload($config);
        $file = $upload->upload();
//        dump($upload->getError());
        echo json_encode($file);
    }

}
