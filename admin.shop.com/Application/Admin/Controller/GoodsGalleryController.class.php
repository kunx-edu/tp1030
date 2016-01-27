<?php 
namespace Admin\Controller;

/**
 * @author kunx
 */
class GoodsGalleryController extends \Think\Controller {

     public function remove($gid){
        $data = array(
            'status'=>1,
            'msg'=>'删除成功',
        );
        $model = M('GoodsGallery');
        if($model->delete($gid) === false){
            $data = array(
                'status'=>0,
                'msg'=>array_pop($model->getError()),
            );
        }
        echo json_encode($data);
    }

}
