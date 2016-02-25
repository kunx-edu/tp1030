<?php
//$file = 'lock.lock';
//$fp = fopen($file,'r+');
//$flag1 = flock($fp,LOCK_EX);
//
//
//$fp2 = fopen($file,'r+');
////$flag2 = flock($fp2,LOCK_EX);
////var_dump($flag1,$flag2);

/**
 * redis锁
 */
$redis = new Redis();
$redis->conncec('127.0.0.1',6379);



$redis = new Redis();
$redis->conncec('127.0.0.1',6379);
for($i=0;$i<3;++$i){
    if($redis->isMember(1)){
        sleep(1);
    }else{
        $redis->sadd('lock_stock',1);
        break;
    }
}
//正常的扣库存流程了

$redis->srem('stock_lock',1);