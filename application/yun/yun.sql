DROP TABLE IF EXISTS `yun_pos_category`;
CREATE TABLE `yun_pos_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `category` varchar(64) NOT NULL DEFAULT '' COMMENT '分类',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级分类id',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分类排序',
  `company_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '企业id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `sort` (`sort`),
  KEY `company_id` (`company_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  KEY `create_time` (`create_time`),
  KEY `update_time` (`update_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT = '分类表';

INSERT INTO `yun_pos_category` VALUES (1,'药品分类',0,0,1,0,1535382711,1535382711),(2,'处方药',1,0,1,0,1535382711,1535382711),(3,'非处方药',1,0,1,0,1535382711,1535382711),(4,'医疗器械',1,0,1,0,1535382711,1535382711),(5,'保健品',1,0,1,0,1535382713,1535382713);


DROP TABLE IF EXISTS `yun_pos_company`;
CREATE TABLE `yun_pos_company` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '企业id',
  `company` varchar(128) NOT NULL DEFAULT '' COMMENT '企业名称',
  `goods` varchar(64) NOT NULL DEFAULT '' COMMENT '企业商品描述,用户移动支付展示',
  `license` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '营业执照',
  `corporate` varchar(64) NOT NULL DEFAULT '' COMMENT '企业法人',
  `phone` varchar(16) NOT NULL DEFAULT '' COMMENT '企业联系电话',
  `province` varchar(32) NOT NULL DEFAULT '' COMMENT '省',
  `city` varchar(32) NOT NULL DEFAULT '' COMMENT '市',
  `district` varchar(32) NOT NULL DEFAULT '' COMMENT '区',
  `detail` varchar(128) NOT NULL DEFAULT '' COMMENT '详细地址',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0 默认禁用  1 启用',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作员',
  `create_date` varchar(32) NOT NULL DEFAULT '' COMMENT '添加日期',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `create_time` (`create_time`),
  KEY `update_time` (`update_time`),
  KEY `create_date` (`create_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='企业信息表';


DROP TABLE IF EXISTS `yun_pos_goods`;
CREATE TABLE `yun_pos_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `barcode` varchar(13) NOT NULL DEFAULT '' COMMENT '条码',
  `goods` varchar(128) NOT NULL DEFAULT '' COMMENT '名称',
  `pinyin` varchar(32) NOT NULL DEFAULT '' COMMENT '拼音码',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `specs` varchar(128) NOT NULL DEFAULT '' COMMENT '规格',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0-进销,1-只销,2,停销,3-停用',
  `unit_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '包装单位',
  `prime` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '成本价',
  `last` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT '最后一次采购价',
  `retail` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT '零售价',
  `vip` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT '会员价',
  `whole` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT '批发价',
  `min` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT '最低售价',
  `retail_rate` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '零售毛利率',
  `whole_rate` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '批发毛利率',
  `vip_rate` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '会员毛利率',
  `supplier_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '供货商id',
  `maker` varchar(128) NOT NULL DEFAULT '' COMMENT '生产厂家',
  `area` varchar(128) NOT NULL DEFAULT '' COMMENT '产地',
  `date` varchar(16) NOT NULL DEFAULT '' COMMENT '生产日期',
  `expire` varchar(16) NOT NULL DEFAULT '' COMMENT '有效期,转为 Y-m-d格式',
  `rule` varchar(16) NOT NULL DEFAULT '' COMMENT '积分规则',
  `is_stock` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '是否管理库存',
  `is_discount` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '是否允许前台打折',
  `is_change_price` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否允许前台改价',
  `is_limits` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '0表示不限量.零售每单限量',
  `is_delete` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除 1 删除',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作员',
  `company_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '企业id',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `supplier_id` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品信息表';

INSERT INTO `yun_pos_goods` VALUES (1,'','测试商品2','cssp',0,'',0,0,0.000,12.00,22.00,0.00,0.00,0.00,0.000,0.000,0.000,0,'','','','','',1,1,0,0,0,1,0,1535382862,1535792795);



DROP TABLE IF EXISTS `yun_pos_retail_detail`;
CREATE TABLE `yun_pos_retail_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `serial` varchar(32) NOT NULL DEFAULT '' COMMENT '流水号',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `prime` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '成本价',
  `retail` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '零售价',
  `vip` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '会员价',
  `price` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '优惠价',
  `pay` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '实付金额',
  `number` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `gross` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '毛利',
  `rate` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '毛利率',
  `member_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `pos_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '款台id',
  `store_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '门店id',
  `company_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '企业id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作员',
  `create_date` varchar(32) NOT NULL DEFAULT '' COMMENT '添加日期',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `serial` (`serial`),
  KEY `member_id` (`member_id`),
  KEY `company_id` (`company_id`),
  KEY `store_id` (`store_id`),
  KEY `user_id` (`user_id`),
  KEY `pos_id` (`pos_id`),
  KEY `create_time` (`create_time`),
  KEY `update_time` (`update_time`),
  KEY `create_date` (`create_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='零售明细表';



DROP TABLE IF EXISTS `yun_pos_retail_money`;
CREATE TABLE `yun_pos_retail_money` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `serial` varchar(32) NOT NULL DEFAULT '' COMMENT '流水号',
  `payment_id` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '支付方式',
  `amount` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '订单金额',
  `pay` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '实付金额',
  `store_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '门店id',
  `company_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '企业id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作员',
  `pos_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '款台id',
  `create_date` varchar(32) NOT NULL DEFAULT '' COMMENT '添加日期',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `serial` (`serial`),
  KEY `store_id` (`store_id`),
  KEY `company_id` (`company_id`),
  KEY `pos_id` (`pos_id`),
  KEY `create_time` (`create_time`),
  KEY `update_time` (`update_time`),
  KEY `create_date` (`create_date`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COMMENT='收款金额表';



DROP TABLE IF EXISTS `yun_pos_retail_summary`;
CREATE TABLE `yun_pos_retail_summary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `serial` varchar(32) NOT NULL DEFAULT '' COMMENT '流水号',
  `cost` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '成本金额',
  `amount` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '订单金额',
  `gross` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '毛利金额',
  `discount` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '优惠金额',
  `rate` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '毛利率',
  `number` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品总数',
  `store_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '门店id',
  `company_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '企业id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作员',
  `pos_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '款台id',
  `create_date` varchar(32) NOT NULL DEFAULT '' COMMENT '添加日期',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `serial` (`serial`),
  KEY `company_id` (`company_id`),
  KEY `store_id` (`store_id`),
  KEY `pos_id` (`pos_id`),
  KEY `create_time` (`create_time`),
  KEY `update_time` (`update_time`),
  KEY `create_date` (`create_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='零售汇总表';



DROP TABLE IF EXISTS `yun_pos_staff`;
CREATE TABLE `yun_pos_staff` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `store_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '门店id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `yun_pos_tmp_retail_detail`;
CREATE TABLE `yun_pos_tmp_retail_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  -- `serial` varchar(32) NOT NULL DEFAULT '' COMMENT '流水号',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `prime` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '成本价',
  `retail` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '零售价',
  `vip` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '会员价',
  `price` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '优惠价',
  `pay` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '实付金额',
  `number` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `gross` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '毛利',
  `rate` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '毛利率',
  `member_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `posid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '款台id',
  `store_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '门店id',
  `company_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '企业id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作员',
  `create_date` varchar(32) NOT NULL DEFAULT '' COMMENT '添加日期',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `serial` (`serial`),
  KEY `member_id` (`member_id`),
  KEY `storeid` (`storeid`),
  KEY `company_id` (`company_id`),
  KEY `user_id` (`user_id`),
  KEY `posid` (`posid`),
  KEY `create_time` (`create_time`),
  KEY `update_time` (`update_time`),
  KEY `create_date` (`create_date`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COMMENT='收款明细表';


DROP TABLE IF EXISTS `yun_pos_tmp_retail_summary`;
CREATE TABLE `yun_pos_tmp_retail_summary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `serial` varchar(32) NOT NULL DEFAULT '' COMMENT '流水号',
  `cost` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '成本金额',
  `amount` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '订单金额',
  `pay` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '实付金额',
  `gross` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '毛利金额',
  `discount` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '优惠金额',
  `rate` decimal(12,3) unsigned NOT NULL DEFAULT '0.000' COMMENT '毛利率',
  `number` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品总数',
  `store_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '门店id',
  `company_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '企业id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作员',
  `pos_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '款台id',
  `create_date` varchar(32) NOT NULL DEFAULT '' COMMENT '添加日期',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `serial` (`serial`),
  KEY `store_id` (`store_id`),
  KEY `company_id` (`company_id`),
  KEY `pos_id` (`pos_id`),
  KEY `create_time` (`create_time`),
  KEY `update_time` (`update_time`),
  KEY `create_date` (`create_date`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COMMENT='收款汇总表';