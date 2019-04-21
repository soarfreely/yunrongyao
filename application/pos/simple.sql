用户管理
	系统用户 admin
	企业用户 企业信息
	企业员工
		企业列表
			企业的员工
DROP TABLE IF EXISTS mini_pos_category;
CREATE TABLE IF NOT EXISTS mini_pos_category(
	`categoryid` int(11) unsigned NOT NULL AUTO_INCREMENT comment '分类id',
	`category` varchar(64) NOT NULL DEFAULT '' comment '分类',
	`parentid` int(11) unsigned NOT NULL DEFAULT 0 comment '父级分类id',
	`sort` int(11) unsigned NOT NULL DEFAULT 0 comment '分类排序',
	`companyid` int(11) unsigned NOT NULL DEFAULT 0 comment '企业id',
    `operateid` int(11) unsigned NOT NULL DEFAULT 0 comment '用户id',
    `create_time` int(11) unsigned NOT NULL DEFAULT 0 comment '添加时间',
    `update_time` int(11) unsigned NOT NULL DEFAULT 0 comment '更新时间',
	primary key(`categoryid`),
    index(sort),
    index(companyid),
    index(operateid),
    index(parentid),
    index(create_time),
    index(update_time)
);

insert into `mini_pos_category` values(1,'药品分类',0,0,1,0,unix_timestamp(NOW()),unix_timestamp(NOW()));
insert into `mini_pos_category` values(2,'处方药',1,0,1,0,unix_timestamp(NOW()),unix_timestamp(NOW()));
insert into `mini_pos_category` values(3,'非处方药',1,0,1,0,unix_timestamp(NOW()),unix_timestamp(NOW()));
insert into `mini_pos_category` values(4,'医疗器械',1,0,1,0,unix_timestamp(NOW()),unix_timestamp(NOW()));
insert into `mini_pos_category` values(5,'保健品',1,0,1,0,unix_timestamp(NOW()),unix_timestamp(NOW()));
-- alter table mini_pos_category add column `operateid` int(11) unsigned NOT NULL DEFAULT 0 comment '用户id';
-- alter table mini_pos_category add column `create_time` int(11) unsigned NOT NULL DEFAULT 0 comment '添加时间';
-- alter table mini_pos_category add column `update_time` int(11) unsigned NOT NULL DEFAULT 0 comment '更新时间';
insert into `mini_pos_category` values(1,'休闲食品',100,0,0,unix_timestamp(NOW()),unix_timestamp(NOW()));
insert into `mini_pos_category` values(2,'休闲食品',100,0,0,unix_timestamp(NOW()),unix_timestamp(NOW()));
insert into `mini_pos_category` values(3,'饮料',100,0,0,unix_timestamp(NOW()),unix_timestamp(NOW()));
insert into `mini_pos_category` values(4,'干货',100,0,0,unix_timestamp(NOW()),unix_timestamp(NOW()));
insert into `mini_pos_category` values(5,'百货',100,0,0,unix_timestamp(NOW()),unix_timestamp(NOW()));

/*档案*/
DROP TABLE IF EXISTS mini_pos_goods;
CREATE TABLE `mini_pos_goods`(
	`goodsid` int(11) unsigned NOT NULL AUTO_INCREMENT comment 'id',
	`barcode` varchar(13) NOT NULL DEFAULT '' comment '条码',
	`goods` varchar(128) NOT NULL DEFAULT '' comment '名称',
	`pinyin` varchar(32) NOT NULL DEFAULT '' comment'拼音码',
	`categoryid` int(11) unsigned NOT NULL DEFAULT 0 comment'分类id',
	`specs` varchar(128) NOT NULL DEFAULT '' comment '规格',
	`status` tinyint(2) NOT NULL DEFAULT 0 comment '0-进销,1-只销,2,停销,3-停用',
	`unitid` int(11) unsigned NOT NULL DEFAULT 0 comment '包装单位',
	`prime` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '成本价',
	`last` decimal(14,2) NOT NULL DEFAULT 0.00 comment '最后一次采购价',
	`retail` decimal(14,2) NOT NULL DEFAULT 0.00 comment '零售价',
	`vip` decimal(14,2) NOT NULL DEFAULT 0.00 comment '会员价',
	`whole` decimal(14,2) NOT NULL DEFAULT 0.00 comment '批发价',
	`min` decimal(14,2) NOT NULL DEFAULT 0.00 comment '最低售价',
	`retail_rate` decimal(8,3) NOT NULL DEFAULT 0.00 comment '零售毛利率',
	`whole_rate` decimal(8,3) NOT NULL DEFAULT 0.00 comment '批发毛利率',
	`vip_rate` decimal(8,3) NOT NULL DEFAULT 0.00 comment '会员毛利率',
	`supplierid` int(11) unsigned NOT NULL DEFAULT 0 comment '供货商id',
	`maker` varchar(128) NOT NULL DEFAULT '' comment '生产厂家',
 	`area` varchar(128) NOT NULL DEFAULT '' comment '产地',
 	`date` varchar(16) NOT NULL DEFAULT '' comment '生产日期',
	`expire` varchar(16) NOT NULL DEFAULT '' comment '有效期,转为 Y-m-d格式',
	`rule` varchar(16) NOT NULL DEFAULT '' comment '积分规则',
	`is_stock` tinyint(2) unsigned NOT NULL DEFAULT 1 comment '是否管理库存',
	`is_discount` tinyint(2) unsigned NOT NULL DEFAULT 1 comment '是否允许前台打折',
	`is_change_price` tinyint(2) unsigned NOT NULL DEFAULT 0 comment '是否允许前台改价',
	`is_limits` tinyint(2) unsigned NOT NULL DEFAULT 0 comment '0表示不限量.零售每单限量',
	`is_delete` tinyint(2) unsigned NOT NULL DEFAULT 0 comment '是否删除 1 删除',
	`operateid` int(11) unsigned NOT NULL DEFAULT 0 comment '操作员',
	`companyid` int(11) unsigned NOT NULL DEFAULT 0 comment '企业id',
	`create_time` int(11) unsigned NOT NULL DEFAULT 0 comment '添加时间',
	`update_time` int(11) unsigned NOT NULL DEFAULT 0 comment '修改时间',
	primary key(goodsid),
	index(`categoryid`)
) comment = '商品信息表';
ALTER TABLE mini_pos_goods modify expiration_date varchar(16) NOT NULL DEFAULT '' comment '有效期至'；

INSERT INTO mini_pos_goods(goodsid,goods,pinyin,categoryid,operateid,create_time,update_time) values(NULL,'测试商品3','cssp',1,1,unix_timestamp(NOW()),unix_timestamp(NOW()));
INSERT INTO mini_pos_goods(goodsid,goods,pinyin,categoryid,operateid,create_time,update_time) values(NULL,'测试商品2','cssp',1,1,unix_timestamp(NOW()),unix_timestamp(NOW()));

-- 收款汇总表（临时）
DROP TABLE IF EXISTS mini_pos_tmp_retail_summary;
CREATE TABLE `mini_pos_tmp_retail_summary`(
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT comment 'id',
	`serial` varchar(32) NOT NULL DEFAULT '' comment '流水号',
	`cost` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '成本金额',
	`amount` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '订单金额',
	`pay` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '实付金额',
	`gross` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '毛利金额',
	`discount` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '优惠金额',
	`rate` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '毛利率',
	`number` int(11) unsigned NOT NULL DEFAULT 0 comment '商品总数',
	`storeid` int(11) unsigned NOT NULL DEFAULT 0 comment '门店id',
	`companyid` int(11) unsigned NOT NULL DEFAULT 0 comment '企业id',
	`operateid` int(11) unsigned NOT NULL DEFAULT 0 comment '操作员',
	`posid` int(11) unsigned NOT NULL DEFAULT 0 comment '款台id',
	`create_date` varchar(32) NOT NULL DEFAULT '' comment '添加日期',
	`create_time` int(11) unsigned NOT NULL DEFAULT 0 comment '添加时间',
	`update_time` int(11) unsigned NOT NULL DEFAULT 0 comment '修改时间',
	primary key(id),
	index(`serial`),
	index(`storeid`),
	index(`companyid`),
	index(`posid`),
	index(create_time),
	index(update_time),
	index(create_date)
)engine = memory comment = '收款汇总表';
-- 收款明细表（临时）
DROP TABLE IF EXISTS mini_pos_tmp_retail_detail;
CREATE TABLE `mini_pos_tmp_retail_detail`(
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT comment 'id',
	`serial` varchar(32) NOT NULL DEFAULT '' comment '流水号',
	`goodsid` int(11) unsigned NOT NULL DEFAULT 0 comment '商品id',
	`prime` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '成本价',
	`retail` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '零售价',
	`vip` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '会员价',
	`price` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '优惠价',
	`pay` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '实付金额',
	`number` int(11) unsigned NOT NULL DEFAULT 0 comment '商品数量',
	`gross` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '毛利',
	`rate` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '毛利率',
	`memberid` int(11) unsigned NOT NULL DEFAULT 0 comment '会员id',
	`posid` int(11) unsigned NOT NULL DEFAULT 0 comment '款台id',
	`storeid` int(11) unsigned NOT NULL DEFAULT 0 comment '门店id',
	`companyid` int(11) unsigned NOT NULL DEFAULT 0 comment '企业id',
	`operateid` int(11) unsigned NOT NULL DEFAULT 0 comment '操作员',
	`create_date` varchar(32) NOT NULL DEFAULT '' comment '添加日期',
	`create_time` int(11) unsigned NOT NULL DEFAULT 0 comment '添加时间',
	`update_time` int(11) unsigned NOT NULL DEFAULT 0 comment '修改时间',
	primary key(id),
	index(`serial`),
	index(`memberid`),
	index(`storeid`),
	index(`companyid`),
	index(`operateid`),
	index(`posid`),
	index(create_time),
	index(update_time),
	index(create_date)
)engine = memory comment = '收款明细表';

-- 收款金额表（临时）
DROP TABLE IF EXISTS mini_pos_tmp_retail_money;
DROP TABLE IF EXISTS mini_pos_tmp_retail_money;
CREATE TABLE `mini_pos_tmp_retail_money`(
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT comment 'id',
	`serial` varchar(32) NOT NULL DEFAULT '' comment '流水号',
	`methodid` int unsigned NOT NULL DEFAULT '1' comment '支付方式',
	`amount` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '订单金额',
	-- `discount` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '优惠金额',
	`pay` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '实付金额',
	-- `gross` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '毛利金额',
	`storeid` int(11) unsigned NOT NULL DEFAULT 0 comment '门店id',
	`companyid` int(11) unsigned NOT NULL DEFAULT 0 comment '企业id',
	`operateid` int(11) unsigned NOT NULL DEFAULT 0 comment '操作员',
	`posid` int(11) unsigned NOT NULL DEFAULT 0 comment '款台id',
	`create_date` varchar(32) NOT NULL DEFAULT '' comment '添加日期',
	`create_time` int(11) unsigned NOT NULL DEFAULT 0 comment '添加时间',
	`update_time` int(11) unsigned NOT NULL DEFAULT 0 comment '修改时间',
	primary key(id),
	index(`serial`),
	index(`storeid`),
	index(`companyid`),
	index(`posid`),
	index(create_time),
	index(update_time),
	index(create_date)
)engine = memory comment = '收款金额表';


-- 零售汇总表
DROP TABLE IF EXISTS mini_pos_retail_summary;
CREATE TABLE `mini_pos_retail_summary`(
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT comment 'id',
	`serial` varchar(32) NOT NULL DEFAULT '' comment '流水号',
	`cost` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '成本金额',
	`amount` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '订单金额',
	`gross` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '毛利金额',
	`discount` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '优惠金额',
	`rate` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '毛利率',
	`number` int(11) unsigned NOT NULL DEFAULT 0 comment '商品总数',
	`storeid` int(11) unsigned NOT NULL DEFAULT 0 comment '门店id',
	`companyid` int(11) unsigned NOT NULL DEFAULT 0 comment '企业id',
	`operateid` int(11) unsigned NOT NULL DEFAULT 0 comment '操作员',
	`posid` int(11) unsigned NOT NULL DEFAULT 0 comment '款台id',
	`create_date` varchar(32) NOT NULL DEFAULT '' comment '添加日期',
	`create_time` int(11) unsigned NOT NULL DEFAULT 0 comment '添加时间',
	`update_time` int(11) unsigned NOT NULL DEFAULT 0 comment '修改时间',
	primary key(id),
	index(`serial`),
	index(`companyid`),
	index(`storeid`),
	index(`posid`),
	index(create_time),
	index(update_time),
	index(create_date)
) engine = innodb COMMENT = '零售汇总表';
-- 零售明细表
DROP TABLE IF EXISTS mini_pos_retail_detail;
CREATE TABLE `mini_pos_retail_detail`(
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT comment 'id',
	`serial` varchar(32) NOT NULL DEFAULT '' comment '流水号',
	`goodsid` int(11) unsigned NOT NULL DEFAULT 0 comment '商品id',
	`prime` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '成本价',
	`retail` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '零售价',
	`vip` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '会员价',
	`price` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '优惠价',
	`pay` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '实付金额',
	`number` int(11) unsigned NOT NULL DEFAULT 0 comment '商品数量',
	`gross` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '毛利',
	`rate` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '毛利率',
	`memberid` int(11) unsigned NOT NULL DEFAULT 0 comment '会员id',
	`posid` int(11) unsigned NOT NULL DEFAULT 0 comment '款台id',
	`storeid` int(11) unsigned NOT NULL DEFAULT 0 comment '门店id',
	`companyid` int(11) unsigned NOT NULL DEFAULT 0 comment '企业id',
	`operateid` int(11) unsigned NOT NULL DEFAULT 0 comment '操作员',
	`create_date` varchar(32) NOT NULL DEFAULT '' comment '添加日期',
	`create_time` int(11) unsigned NOT NULL DEFAULT 0 comment '添加时间',
	`update_time` int(11) unsigned NOT NULL DEFAULT 0 comment '修改时间',
	primary key(id),
	index(`serial`),
	index(`memberid`),
	index(`companyid`),
	index(`storeid`),
	index(`operateid`),
	index(`posid`),
	index(create_time),
	index(update_time),
	index(create_date)
)engine = innodb COMMENT = '零售明细表';

DROP TABLE IF EXISTS mini_pos_retail_money;
CREATE TABLE `mini_pos_retail_money`(
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT comment 'id',
	`serial` varchar(32) NOT NULL DEFAULT '' comment '流水号',
	`methodid` int unsigned NOT NULL DEFAULT '1' comment '支付方式',
	`amount` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '订单金额',
	-- `discount` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '优惠金额',
	`pay` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '实付金额',
	-- `gross` decimal(12,3) unsigned NOT NULL DEFAULT 0 comment '毛利金额',
	`storeid` int(11) unsigned NOT NULL DEFAULT 0 comment '门店id',
	`companyid` int(11) unsigned NOT NULL DEFAULT 0 comment '企业id',
	`operateid` int(11) unsigned NOT NULL DEFAULT 0 comment '操作员',
	`posid` int(11) unsigned NOT NULL DEFAULT 0 comment '款台id',
	`create_date` varchar(32) NOT NULL DEFAULT '' comment '添加日期',
	`create_time` int(11) unsigned NOT NULL DEFAULT 0 comment '添加时间',
	`update_time` int(11) unsigned NOT NULL DEFAULT 0 comment '修改时间',
	primary key(id),
	index(`serial`),
	index(`storeid`),
	index(`companyid`),
	index(`posid`),
	index(create_time),
	index(update_time),
	index(create_date)
)engine = memory comment = '收款金额表';

-- 企业信息表
DROP TABLE IF EXISTS mini_pos_company;
CREATE TABLE `mini_pos_company`(
	`companyid` int(11) unsigned NOT NULL AUTO_INCREMENT comment '企业id',
	`company` varchar(128) NOT NULL DEFAULT '' comment '企业名称',
	`goods` varchar(64) NOT NULL DEFAULT '' comment '企业商品描述,用户移动支付展示',
	`license` int(11) unsigned NOT NULL DEFAULT '0' comment '营业执照',
	`corporate` varchar(64) NOT NULL DEFAULT '' comment '企业法人',
	`phone` varchar(16) NOT NULL DEFAULT '' comment '企业联系电话',
	`province` varchar(32) NOT NULL DEFAULT '' comment '省',
	`city` varchar(32) NOT NULL DEFAULT '' comment '市',
	`district` varchar(32) NOT NULL DEFAULT '' comment '区',
	`detail` varchar(128) NOT NULL DEFAULT '' comment '详细地址',
	`status` tinyint(2) unsigned NOT NULL DEFAULT 0 comment '状态 0 默认禁用  1 启用',
	`operateid` int(11) unsigned NOT NULL DEFAULT 0 comment '操作员',
	`create_date` varchar(32) NOT NULL DEFAULT '' comment '添加日期',
	`create_time` int(11) unsigned NOT NULL DEFAULT 0 comment '添加时间',
	`update_time` int(11) unsigned NOT NULL DEFAULT 0 comment '修改时间',
	primary key(companyid),
	index(create_time),
	index(update_time),
	index(create_date)
)engine = innodb COMMENT = '企业信息表';

INSERT INTO mini_pos_company values(1,'国安大药房','国安大药房','1','法人','18888888888','陕西省','安康市','汉滨区',
		'xx路',0,1,now(),unix_timestamp(),unix_timestamp());

-- 附件
-- DROP TABLE IF EXISTS mini_pos_attachment;
-- CREATE TABLE `mini_pos_attachment`(
-- 	`attachmentid` int(11) unsigned NOT NULL AUTO_INCREMENT comment '附件id',
-- 	`location` varchar(255) NOT NULL DEFAULT '' comment '附件路径',
-- 	`operateid` int(11) unsigned NOT NULL DEFAULT 0 comment '操作员',
-- 	`create_date` varchar(32) NOT NULL DEFAULT '' comment '添加日期',
-- 	`create_time` int(11) unsigned NOT NULL DEFAULT 0 comment '添加时间',
-- 	`update_time` int(11) unsigned NOT NULL DEFAULT 0 comment '修改时间',
-- )engine = MyISAM COMMENT = '附件信息表';

-- 员工,企业账号创建人,自行添加的员工信息
-- DROP TABLE IF EXISTS mini_pos_operateid;
-- CREATE TABLE `mini_pos_operateid`(
-- 	`operateid` int(11) unsigned NOT NULL AUTO_INCREMENT comment '员工id',
-- 	``
-- 	`operateid` int(11) unsigned NOT NULL DEFAULT 0 comment '操作员',
-- )engine = MyISAM COMMENT = '附件信息表';

+-----------------+------------------------+------+-----+---------+----------------+
| Field           | Type                   | Null | Key | Default | Extra          |
+-----------------+------------------------+------+-----+---------+----------------+
| id              | int(11) unsigned       | NO   | PRI | NULL    | auto_increment |
| username        | varchar(32)            | NO   |     |         |                |
| nickname        | varchar(32)            | NO   |     |         |                |
| password        | varchar(96)            | NO   |     |         |                |
| email           | varchar(64)            | NO   |     |         |                |
| email_bind      | tinyint(1) unsigned    | NO   |     | 0       |                |
| mobile          | varchar(11)            | NO   |     |         |                |
| mobile_bind     | tinyint(1) unsigned    | NO   |     | 0       |                |
| avatar          | int(11) unsigned       | NO   |     | 0       |                |
| money           | decimal(11,2) unsigned | NO   |     | 0.00    |                |
| score           | int(11) unsigned       | NO   |     | 0       |                |
| role            | int(11) unsigned       | NO   |     | 0       |                |
| group           | int(11) unsigned       | NO   |     | 0       |                |
| signup_ip       | bigint(20) unsigned    | NO   |     | 0       |                |
| create_time     | int(11) unsigned       | NO   |     | 0       |                |
| update_time     | int(11) unsigned       | NO   |     | 0       |                |
| last_login_time | int(11) unsigned       | NO   |     | 0       |                |
| last_login_ip   | bigint(20) unsigned    | NO   |     | 0       |                |
| sort            | int(11)                | NO   |     | 100     |                |
| status          | tinyint(2)             | NO   |     | 0       |                |
+-----------------+------------------------+------+-----+---------+----------------+

ALTER TABLE mini_admin_user ADD COLUMN companyid int(11) unsigned NOT NULL DEFAULT 0 comment '企业id' AFTER `group`;
ALTER TABLE mini_admin_user ADD COLUMN founder tinyint(1) unsigned NOT NULL DEFAULT 0 comment '是否是,企业创建者 默认 0 不是,1是' AFTER `group`;


INSERT INTO mini_admin_user (id,username,nickname,email,mobile,companyid,create_time) VALUES(
	NULL,'张三','zs','362431947@qq.com','18666666666',1,unix_timestamp()
);

INSERT INTO mini_admin_user (id,username,nickname,email,mobile,companyid,create_time) VALUES(
	NULL,'李四','ls','346777749@qq.com','18999999999',1,unix_timestamp()
);
-- -- 用户与企业管理信息表
-- DROP TABLE IF EXISTS mini_pos_user_company;
-- CREATE TABLE `mini_pos_user_company`(

-- );


-- 员工信息表
DROP TABLE IF EXISTS mini_pos_staff;
CREATE TABLE `mini_pos_staff`(
	id int(11) unsigned not null AUTO_INCREMENT comment '主键',
	uid int(11) unsigned NOT NULL DEFAULT 0 comment '用户id',
	storeid int(11) unsigned not null DEFAULT 0 comment '门店id',
	primary key (id)
);

INSERT INTO mini_pos_staff VALUES(
	NULL,2,1
);
INSERT INTO mini_pos_staff VALUES(
	NULL,3,1
);
