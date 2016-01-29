/*
SQLyog v10.2 
MySQL - 5.5.39-log : Database - tp1030
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`tp1030` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `tp1030`;

/*Table structure for table `admin` */

DROP TABLE IF EXISTS `admin`;

CREATE TABLE `admin` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` char(6) NOT NULL DEFAULT '' COMMENT '盐',
  `email` varchar(30) NOT NULL DEFAULT '' COMMENT '邮箱',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `last_login_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员';

/*Data for the table `admin` */

/*Table structure for table `admin_permission` */

DROP TABLE IF EXISTS `admin_permission`;

CREATE TABLE `admin_permission` (
  `admin_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `permission_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '权限ID',
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='额外权限';

/*Data for the table `admin_permission` */

/*Table structure for table `admin_role` */

DROP TABLE IF EXISTS `admin_role`;

CREATE TABLE `admin_role` (
  `admin_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `role_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '角色ID',
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员角色关系';

/*Data for the table `admin_role` */

/*Table structure for table `article` */

DROP TABLE IF EXISTS `article`;

CREATE TABLE `article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `article_category_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '文章分类',
  `intro` text COMMENT '简介@textarea',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态@radio|1=是&0=否',
  `sort` tinyint(4) NOT NULL DEFAULT '20' COMMENT '排序',
  `inputtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '录入时间',
  PRIMARY KEY (`id`),
  KEY `article_category_id` (`article_category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='文章';

/*Data for the table `article` */

insert  into `article`(`id`,`name`,`article_category_id`,`intro`,`status`,`sort`,`inputtime`) values (1,'运费信息',1,'运费收取标准',1,20,1453792456),(4,'IKBC G104评测',2,'IKBC灯厂键盘评测',1,50,1453794134),(5,'aaa good',2,'aaa is very good',1,40,1453794202),(6,'bbb',2,'bbb is no bad',1,30,1453794236),(7,'abc',2,'abc童装',1,30,1453795774),(8,'abcd',2,'abcd非常棒',1,30,1453795800);

/*Table structure for table `article_category` */

DROP TABLE IF EXISTS `article_category`;

CREATE TABLE `article_category` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `is_help` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否帮助类@radio|1=是&0=否',
  `intro` text COMMENT '简介@textarea',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态@radio|1=是&0=否',
  `sort` tinyint(4) NOT NULL DEFAULT '20' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='文章分类';

/*Data for the table `article_category` */

insert  into `article_category`(`id`,`name`,`is_help`,`intro`,`status`,`sort`) values (1,'帮助信息',1,'给用户提供一些帮助的静态文件',0,20),(2,'资讯类',0,'资讯信息',1,10);

/*Table structure for table `article_content` */

DROP TABLE IF EXISTS `article_content`;

CREATE TABLE `article_content` (
  `article_id` int(10) unsigned NOT NULL,
  `content` text COMMENT '文章内容',
  PRIMARY KEY (`article_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章内容';

/*Data for the table `article_content` */

insert  into `article_content`(`article_id`,`content`) values (1,'<p>&#39;&quot;&quot;&gt;?&gt;&lt;</p>'),(4,'<p>IKBC试水机械键盘，G104用起来超棒的。</p>'),(5,'<p>never get so good goods!</p>'),(6,'<p>never have a goods like this!</p>'),(7,'<p>abc童装童鞋</p>'),(8,'<p>abcd超级棒</p>');

/*Table structure for table `brand` */

DROP TABLE IF EXISTS `brand`;

CREATE TABLE `brand` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `intro` text COMMENT '简介',
  `sort` tinyint(4) NOT NULL DEFAULT '20' COMMENT '排序 数字越小越靠前',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态-1删除   0隐藏   1正常',
  `logo` varchar(200) DEFAULT NULL COMMENT 'logo图片',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='品牌';

/*Data for the table `brand` */

insert  into `brand`(`id`,`name`,`intro`,`sort`,`status`,`logo`) values (1,'欧莱雅','你值得拥有',20,1,NULL),(2,'阿迪王','阿迪王运动鞋，随你征服世界',20,1,NULL),(3,'三鹿奶粉','三鹿牛奶，欢乐开怀',25,1,NULL),(4,'美特斯邦威','不走寻常路',25,1,'http://7xnizi.com1.z0.glb.clouddn.com/Uploads_20160123_56a31a1c4eb4c.jpg'),(5,'特步','非一般的感觉',29,1,NULL),(6,'安踏','安踏，永不止步',27,1,NULL),(7,'安利纽崔莱','有健康，才有将来',18,1,NULL),(8,'IBM','爱爸妈',17,1,NULL),(9,'Baidu','众里寻他千百度',10,1,NULL);

/*Table structure for table `goods` */

DROP TABLE IF EXISTS `goods`;

CREATE TABLE `goods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `sn` varchar(20) NOT NULL DEFAULT '' COMMENT '货号',
  `logo` varchar(200) NOT NULL DEFAULT '' COMMENT '商品LOGO',
  `goods_category_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商品分类',
  `brand_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '品牌',
  `supplier_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '供货商',
  `market_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '市场价格',
  `shop_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '本店价格',
  `stock` int(11) NOT NULL DEFAULT '0' COMMENT '库存',
  `goods_status` int(11) NOT NULL DEFAULT '0' COMMENT '商品状态',
  `is_on_sale` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否上架',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态@radio|1=是&0=否',
  `sort` tinyint(4) NOT NULL DEFAULT '20' COMMENT '排序',
  `inputtime` int(11) NOT NULL DEFAULT '0' COMMENT '录入时间',
  PRIMARY KEY (`id`),
  KEY `goods_category_id` (`goods_category_id`),
  KEY `brand_id` (`brand_id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='商品';

/*Data for the table `goods` */

insert  into `goods`(`id`,`name`,`sn`,`logo`,`goods_category_id`,`brand_id`,`supplier_id`,`market_price`,`shop_price`,`stock`,`goods_status`,`is_on_sale`,`status`,`sort`,`inputtime`) values (1,'小米','1','http://7xnizi.com1.z0.glb.clouddn.com/Uploads_20160127_56a8805f0f2f1.jpg',1,9,1,'0.00','100.00',0,3,1,1,20,1453773189),(2,'_del_del','20160126000000002','',4,4,2,'25.00','58.00',0,5,1,-1,20,1453775393),(3,'小太阳','20160126000000003','',7,7,2,'1000.00','500.00',20,6,1,1,100,1453775557),(4,'容声冰箱','20160126000000004','',3,2,2,'10000.00','8000.00',50,1,1,1,30,1453779756),(5,'长虹电视','20160126000000005','http://7xnizi.com1.z0.glb.clouddn.com/Uploads_20160127_56a8214dc25de.jpg',1,4,2,'8000.00','5000.00',50,6,1,1,100,1453779936),(6,'手机','20160127000000006','http://7xnizi.com1.z0.glb.clouddn.com/Uploads_20160127_56a827be8f2bf.jpg',7,2,6,'5000.00','2000.00',50,3,1,1,100,1453860848);

/*Table structure for table `goods_article` */

DROP TABLE IF EXISTS `goods_article`;

CREATE TABLE `goods_article` (
  `goods_id` int(10) unsigned DEFAULT NULL,
  `article_id` int(10) unsigned DEFAULT NULL,
  KEY `goods_id` (`goods_id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `goods_article` */

insert  into `goods_article`(`goods_id`,`article_id`) values (5,5),(5,7),(5,8),(5,6);

/*Table structure for table `goods_category` */

DROP TABLE IF EXISTS `goods_category`;

CREATE TABLE `goods_category` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `parent_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '父分类',
  `lft` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '左边界',
  `rght` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '右边界',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '级别',
  `intro` text COMMENT '简介@textarea',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态@radio@1=是&0=否',
  `sort` tinyint(4) NOT NULL DEFAULT '20' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `lft` (`lft`,`rght`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='商品分类';

/*Data for the table `goods_category` */

insert  into `goods_category`(`id`,`name`,`parent_id`,`lft`,`rght`,`level`,`intro`,`status`,`sort`) values (1,'平板电视',9,3,4,3,'',1,20),(2,'空调',9,5,6,3,'',1,20),(3,'冰箱',9,7,8,3,'',1,20),(4,'取暖器',8,21,24,3,'取暖器，好暖和',1,20),(5,'净化器',8,25,26,3,'',1,20),(6,'加湿器',8,27,28,3,'',1,20),(7,'小太阳',4,22,23,4,'',1,20),(8,'生活电器',10,20,29,2,'',1,20),(9,'大家电',10,2,19,2,'',1,20),(10,'家用电器',0,1,30,1,'这是家用电器',1,20);

/*Table structure for table `goods_gallery` */

DROP TABLE IF EXISTS `goods_gallery`;

CREATE TABLE `goods_gallery` (
  `gid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` bigint(20) DEFAULT NULL COMMENT '商品ID',
  `path` varchar(200) NOT NULL DEFAULT '' COMMENT '商品图片地址',
  PRIMARY KEY (`gid`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='商品相册';

/*Data for the table `goods_gallery` */

insert  into `goods_gallery`(`gid`,`goods_id`,`path`) values (16,6,''),(17,6,''),(18,6,'');

/*Table structure for table `goods_intro` */

DROP TABLE IF EXISTS `goods_intro`;

CREATE TABLE `goods_intro` (
  `goods_id` bigint(20) NOT NULL COMMENT '商品ID',
  `content` text COMMENT '商品描述',
  PRIMARY KEY (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品描述';

/*Data for the table `goods_intro` */

insert  into `goods_intro`(`goods_id`,`content`) values (4,'<p>可以看电视的冰箱，冰箱中的电视机，欧耶</p>'),(5,'<p>长虹电视，影视长虹</p>'),(6,'<p>手机中的取暖机</p>');

/*Table structure for table `goods_type` */

DROP TABLE IF EXISTS `goods_type`;

CREATE TABLE `goods_type` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `intro` text COMMENT '简介',
  `sort` tinyint(4) NOT NULL DEFAULT '20' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态@radio@-1=删除&0=隐藏&1=正常',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品类型';

/*Data for the table `goods_type` */

/*Table structure for table `menu` */

DROP TABLE IF EXISTS `menu`;

CREATE TABLE `menu` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `path` varchar(50) NOT NULL DEFAULT '' COMMENT 'path:module/controller/action',
  `parent_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '父分类',
  `lft` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '左边界',
  `rght` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '右边界',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '级别',
  `intro` text COMMENT '简介@textarea',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态@radio|1=是&0=否',
  `sort` tinyint(4) NOT NULL DEFAULT '20' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `lft` (`lft`,`rght`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='菜单表';

/*Data for the table `menu` */

insert  into `menu`(`id`,`name`,`path`,`parent_id`,`lft`,`rght`,`level`,`intro`,`status`,`sort`) values (5,'添加商品','Admin/Goods/add',4,2,3,2,'添加商品',1,100),(4,'商品管理','',0,1,4,1,'商品管理权限',1,100);

/*Table structure for table `menu_permission` */

DROP TABLE IF EXISTS `menu_permission`;

CREATE TABLE `menu_permission` (
  `menu_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '菜单',
  `permission_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '权限ID',
  KEY `permission_id` (`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='菜单权限';

/*Data for the table `menu_permission` */

insert  into `menu_permission`(`menu_id`,`permission_id`) values (4,7),(4,6),(4,5),(5,5),(5,6),(5,7);

/*Table structure for table `permission` */

DROP TABLE IF EXISTS `permission`;

CREATE TABLE `permission` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `path` varchar(50) NOT NULL DEFAULT '' COMMENT 'URL',
  `parent_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '父分类',
  `lft` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '左边界',
  `rght` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '右边界',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '级别',
  `intro` text COMMENT '简介@textarea',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态@radio|1=是&0=否',
  `sort` tinyint(4) NOT NULL DEFAULT '20' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `lft` (`lft`,`rght`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='权限';

/*Data for the table `permission` */

insert  into `permission`(`id`,`name`,`path`,`parent_id`,`lft`,`rght`,`level`,`intro`,`status`,`sort`) values (6,'添加商品','Admin/Goods/add',5,2,3,2,'添加商品',0,100),(5,'商品管理','',0,1,6,1,'商品管理权限',0,100),(7,'修改商品','Admin/Goods/edit',5,4,5,2,'修改商品',0,100),(8,'权限管理','',0,7,10,1,'权限管理',1,100),(9,'添加权限','Admin/Permission/add',8,8,9,2,'添加权限',1,100),(10,'会员管理','',0,11,14,1,'会员管理',1,100),(11,'添加会员','Admin/Admin/add',10,12,13,2,'创建会员',1,100);

/*Table structure for table `role` */

DROP TABLE IF EXISTS `role`;

CREATE TABLE `role` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `intro` text COMMENT '简介@textarea',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态@radio|1=是&0=否',
  `sort` tinyint(4) NOT NULL DEFAULT '20' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='角色';

/*Data for the table `role` */

insert  into `role`(`id`,`name`,`intro`,`status`,`sort`) values (1,'运营专员','运营专员角色组',1,100),(2,'超级管理员','超级管理员',1,100);

/*Table structure for table `role_permission` */

DROP TABLE IF EXISTS `role_permission`;

CREATE TABLE `role_permission` (
  `role_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '角色ID',
  `permission_id` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '权限ID',
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色权限关系';

/*Data for the table `role_permission` */

insert  into `role_permission`(`role_id`,`permission_id`) values (2,8),(2,9),(2,10),(2,11),(1,5),(1,6),(1,7);

/*Table structure for table `supplier` */

DROP TABLE IF EXISTS `supplier`;

CREATE TABLE `supplier` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `intro` text COMMENT '简介',
  `sort` tinyint(4) NOT NULL DEFAULT '20' COMMENT '排序 数字越小越靠前',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态-1删除   0隐藏   1正常',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='供货商';

/*Data for the table `supplier` */

insert  into `supplier`(`id`,`name`,`intro`,`sort`,`status`) values (1,'北京供货商','北京供货商供应的烤鸭、稻香村、驴打滚超好吃',25,0),(2,'上海供货商','上海供货商的简介',20,0),(3,'成都供货商_del','成都供货商的简介',20,-1),(4,'武汉供货商_del','武汉供货商的简介',20,-1),(5,'重庆供货商_del','重庆供货商的简介',20,-1),(6,'山东供货商','山东供货商的大葱、煎饼是国内一绝',20,1),(7,'成都供应商','成都供应商提供的夫妻肺片、麻婆豆腐、回锅肉、龙抄手',30,0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
