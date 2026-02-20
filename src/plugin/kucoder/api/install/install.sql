SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 表__prefix__ku_config 结构
DROP TABLE IF EXISTS `__prefix__ku_config`;
CREATE TABLE IF NOT EXISTS `__prefix__ku_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` int(11) DEFAULT NULL COMMENT '配置的分组ID',
  `plugin` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '所属插件',
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '配置名',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '配置标题',
  `tip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '配置标题tip',
  `type` enum('input','input-number','switch','textarea','select','radio','checkbox','rich','upload_img','upload_file') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '输入类型',
  `value` text COLLATE utf8mb4_unicode_ci COMMENT '值',
  `config_data` json DEFAULT NULL COMMENT '选项数据',
  `is_secret` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '敏感字段:0=否,1=是',
  `validate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '验证规则',
  `extend` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '扩展属性',
  `allow_del` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '允许删除:0=否,1=是',
  `hide` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '显示:0=显示,1=隐藏',
  `weigh` int(11) NOT NULL DEFAULT '0' COMMENT '权重',
  `delete_time` datetime DEFAULT NULL COMMENT '软删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='系统参数配置表';

INSERT INTO `__prefix__ku_config` ( `group_id`, `plugin`, `name`, `title`, `tip`, `type`, `value`, `config_data`, `is_secret`, `validate`, `extend`, `allow_del`, `hide`, `weigh`, `delete_time`) VALUES
  ( 1, 'kucoder', 'site_name', '系统名称', '', 'input', 'kucoder系统后台', NULL, 0, 'required', '', 0, 0, 99, NULL),
  ( 1, 'kucoder', 'record_number', '域名备案号', '', 'input', '', NULL, 0, '', '', 0, 0, 0, NULL),
  ( 2, 'kucoder', 'smtp_server', 'smtp服务器', '', 'input', 'smtp.qq.com', NULL, 0, '', '', 0, 0, 9, NULL),
  ( 2, 'kucoder', 'smtp_port', 'smtp端口', '', 'input', '465', NULL, 0, '', '', 0, 0, 8, NULL),
  ( 2, 'kucoder', 'smtp_user', 'smtp用户', '', 'input', NULL, NULL, 0, '', '', 0, 0, 7, NULL),
  ( 2, 'kucoder', 'smtp_pass', 'smtp密码', '', 'input', NULL, NULL, 0, '', '', 0, 0, 6, NULL),
  ( 2, 'kucoder', 'smtp_verification', 'smtp加密方式', '', 'input', 'SSL', '{"SSL": "SSL", "TLS": "TLS"}', 0, '', '', 0, 0, 5, NULL),
  ( 2, 'kucoder', 'smtp_sender_mail', 'smtp发送者邮箱', '', 'input', NULL, NULL, 0, 'email', '', 0, 0, 4, NULL),
  ( 3, 'kucoder', 'upload_mode', '上传模式', '', 'radio', '', '{"hwoss": "华为云OSS", "local": "本地存储", "qnoss": "七牛云OSS", "txoss": "腾讯云OSS", "alioss": "阿里云OSS"}', 0, 'required', '', 0, 0, 99, NULL),
  ( 3, 'kucoder', 'upload_bucket', 'bucket', '请在云厂商对象存储控制台查询', 'input', '', NULL, 0, '', '', 0, 0, 98, NULL),
  ( 3, 'kucoder', 'upload_access_id', 'access_id', '请在云厂商查询', 'input', '', NULL, 0, '', '', 0, 0, 97, NULL),
  ( 3, 'kucoder', 'upload_secret_key', 'secret_key', '请在云厂商查询', 'input', '', NULL, 0, '', '', 0, 0, 96, NULL),
  ( 3, 'kucoder', 'upload_url', 'upload_url', '请选择存储区域', 'input', '', '{"oss-cn-hzjbp": "华东1金融云 oss-cn-hzjbp", "oss-cn-fuzhou": "华东6（福州本地地域） oss-cn-fuzhou", "oss-cn-heyuan": "华南2（河源） oss-cn-heyuan", "oss-eu-west-1": "英国（伦敦） oss-eu-west-1", "oss-me-east-1": "阿联酋（迪拜） oss-me-east-1", "oss-us-east-1": "美国（弗吉尼亚） oss-us-east-1", "oss-us-west-1": "美国（硅谷） oss-us-west-1", "oss-ap-south-1": "印度（孟买） oss-ap-south-1", "oss-cn-beijing": "华北2（北京） oss-cn-beijing", "oss-cn-chengdu": "西南1（成都） oss-cn-chengdu", "oss-cn-nanjing": "华东5（南京本地地域） oss-cn-nanjing", "oss-cn-qingdao": "华北1（青岛） oss-cn-qingdao", "oss-cn-hangzhou": "华东1（杭州） oss-cn-hangzhou", "oss-cn-hongkong": "中国（香港） oss-cn-hongkong", "oss-cn-shanghai": "华东2（上海） oss-cn-shanghai", "oss-cn-shenzhen": "华南1（深圳） oss-cn-shenzhen", "oss-cn-guangzhou": "华南3（广州） oss-cn-guangzhou", "oss-cn-huhehaote": "华北5（呼和浩特） oss-cn-huhehaote", "oss-cn-hzfinance": "杭州金融云公网 oss-cn-hzfinance", "oss-cn-szfinance": "深圳金融云公网 oss-cn-szfinance", "oss-eu-central-1": "德国（法兰克福） oss-eu-central-1", "oss-cn-wulanchabu": "华北6（乌兰察布） oss-cn-wulanchabu", "oss-ap-northeast-1": "日本（东京） oss-ap-northeast-1", "oss-ap-northeast-2": "韩国（首尔） oss-ap-northeast-2", "oss-ap-southeast-1": "新加坡 oss-ap-southeast-1", "oss-ap-southeast-2": "澳大利亚（悉尼） oss-ap-southeast-2", "oss-ap-southeast-3": "马来西亚（吉隆坡） oss-ap-southeast-3", "oss-ap-southeast-5": "印度尼西亚（雅加达） oss-ap-southeast-5", "oss-ap-southeast-6": "菲律宾（马尼拉） oss-ap-southeast-6", "oss-ap-southeast-7": "泰国（曼谷） oss-ap-southeast-7", "oss-cn-zhangjiakou": "华北 3（张家口） oss-cn-zhangjiakou", "oss-cn-beijing-finance-1": "华北2金融云 oss-cn-beijing-finance-1", "oss-cn-shanghai-finance-1": "华东2金融云 oss-cn-shanghai-finance-1", "oss-cn-shenzhen-finance-1": "华南1金融云 oss-cn-shenzhen-finance-1", "oss-cn-beijing-finance-1-pub": "北京金融云公网 oss-cn-beijing-finance-1-pub", "oss-cn-shanghai-finance-1-pub": "上海金融云公网 oss-cn-shanghai-finance-1-pub"}', 0, '', '', 0, 0, 95, NULL),
  ( 3, 'kucoder', 'upload_cdn_url', 'cdn_url', '（可选）请输入对象存储的CDN加速域名，以http(s)://开头', 'input', '', NULL, 0, '', '', 0, 0, 94, NULL),
  ( 1, 'kucoder', 'site_logo', '系统logo', '', 'upload_img', '', NULL, 0, '', '', 0, 1, 0, NULL);


-- 表__prefix__ku_config_group 结构
DROP TABLE IF EXISTS `__prefix__ku_config_group`;
CREATE TABLE IF NOT EXISTS `__prefix__ku_config_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '分组名称',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '分组name',
  `icon` varchar(30) NOT NULL DEFAULT '' COMMENT '分组图标',
  `plugin` varchar(30) NOT NULL DEFAULT '' COMMENT '分组所属插件',
  `delete_time` datetime DEFAULT NULL COMMENT '软删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='系统参数分组表';

INSERT INTO `__prefix__ku_config_group` (`id`, `title`, `name`, `icon`, `plugin`, `delete_time`) VALUES
  (1, '基础配置', 'base', '', 'kucoder', NULL),
  (2, '邮件配置', 'email', '', 'kucoder', NULL),
  (3, '上传配置', 'upload', '', 'kucoder', NULL);


-- 表__prefix__ku_dept 结构
DROP TABLE IF EXISTS `__prefix__ku_dept`;
CREATE TABLE IF NOT EXISTS `__prefix__ku_dept` (
  `dept_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '部门id',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父部门id',
  `ancestors` varchar(50) NOT NULL DEFAULT '' COMMENT '祖级列表',
  `dept_name` varchar(30) NOT NULL DEFAULT '' COMMENT '部门名称',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `leader` varchar(20) NOT NULL DEFAULT '' COMMENT '负责人',
  `phone` char(11) NOT NULL DEFAULT '' COMMENT '联系电话',
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '邮箱',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '部门状态:0=禁用,1=正常',
  `create_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建者',
  `update_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新者',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`dept_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='后台用户部门表';


-- 表__prefix__ku_log 结构
DROP TABLE IF EXISTS `__prefix__ku_log`;
CREATE TABLE IF NOT EXISTS `__prefix__ku_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '操作日志主键',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '操作日志title',
  `plugin` varchar(20) NOT NULL DEFAULT '' COMMENT '请求插件',
  `app` varchar(10) NOT NULL DEFAULT '' COMMENT '请求应用名',
  `app_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '应用类型:0=后台admin应用,1=客户端api应用,2=客户端小程序app应用',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '请求路径',
  `controller` varchar(100) NOT NULL DEFAULT '' COMMENT '请求的控制器',
  `action` varchar(20) NOT NULL DEFAULT '' COMMENT '请求的控制器方法',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id(后台管理员id或前台会员id)',
  `ip` varchar(128) NOT NULL DEFAULT '' COMMENT '操作IP',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '操作状态:0=操作未完成,1=操作完成',
  `msg` varchar(255) NOT NULL DEFAULT '' COMMENT '操作结果消息',
  `create_time` datetime DEFAULT NULL COMMENT '操作时间',
  `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_sys_oper_log_bt` (`action`) USING BTREE,
  KEY `idx_sys_oper_log_s` (`status`) USING BTREE,
  KEY `idx_sys_oper_log_ot` (`create_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='操作日志表';


-- 表__prefix__ku_log_login 结构
DROP TABLE IF EXISTS `__prefix__ku_log_login`;
CREATE TABLE IF NOT EXISTS `__prefix__ku_log_login` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '登录日志主键',
  `plugin` varchar(20) NOT NULL DEFAULT '' COMMENT '请求插件',
  `app` varchar(10) NOT NULL DEFAULT '' COMMENT '请求应用名',
  `app_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '应用类型:0=后台admin应用,1=客户端api应用,2=客户端小程序app应用',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id(后台管理员id或前台会员id)',
  `ip` varchar(128) NOT NULL DEFAULT '' COMMENT '登录IP',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '登录状态:0=异常,1=正常',
  `msg` varchar(255) NOT NULL DEFAULT '' COMMENT '登录结果消息',
  `create_time` datetime DEFAULT NULL COMMENT '登录时间',
  `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_sys_oper_log_s` (`status`) USING BTREE,
  KEY `idx_sys_oper_log_ot` (`create_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='登录日志表';


-- 表__prefix__ku_member 结构
DROP TABLE IF EXISTS `__prefix__ku_member`;
CREATE TABLE IF NOT EXISTS `__prefix__ku_member` (
  `m_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '会员ID',
  `role_ids` varchar(10000) NOT NULL DEFAULT '' COMMENT '会员角色',
  `username` varchar(50) NOT NULL COMMENT '会员账号',
  `password` varchar(100) NOT NULL DEFAULT '' COMMENT '会员密码',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '会员昵称',
  `email` varchar(50) DEFAULT NULL COMMENT '会员邮箱',
  `mobile` char(11) DEFAULT NULL COMMENT '手机号码',
  `sex` tinyint(1) unsigned zerofill NOT NULL DEFAULT '2' COMMENT '会员性别:0=男,1=女,2=未知',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像路径',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '会员账号状态:0=禁用,1=正常',
  `remark` varchar(1000) NOT NULL COMMENT '备注',
  `login_ip` varchar(128) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_time` datetime DEFAULT NULL COMMENT '最后登录时间',
  `password_update_time` datetime DEFAULT NULL COMMENT '密码更新时间',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  `delete_time` datetime DEFAULT NULL COMMENT '软删除时间',
  PRIMARY KEY (`m_id`) USING BTREE,
  UNIQUE KEY `username` (`username`) USING BTREE,
  KEY `mobile` (`mobile`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='系统会员表';


-- 表__prefix__ku_menu 结构
DROP TABLE IF EXISTS `__prefix__ku_menu`;
CREATE TABLE IF NOT EXISTS `__prefix__ku_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '菜单ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `pid` int(10) unsigned DEFAULT NULL COMMENT '父菜单ID',
  `icon` varchar(100) NOT NULL DEFAULT '#' COMMENT '菜单图标',
  `plugin` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单所属插件名',
  `type` enum('dir','menu','button','link') DEFAULT NULL COMMENT '菜单类型:dir=目录,menu=菜单,button=按钮,link=链接',
  `path` varchar(100) NOT NULL DEFAULT '' COMMENT '路由path',
  `component` varchar(100) NOT NULL DEFAULT '' COMMENT '路由组件view',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '路由name',
  `query` varchar(255) NOT NULL DEFAULT '' COMMENT '路由query',
  `link_url` varchar(100) NOT NULL DEFAULT '#' COMMENT '外链地址',
  `keepalive` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否刷新:0=不缓存,1=缓存',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `show` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '菜单状态:0=隐藏,1=显示',
  `create_uid` int(10) unsigned DEFAULT NULL COMMENT '创建者',
  `update_uid` int(10) unsigned DEFAULT NULL COMMENT '更新者',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='后台菜单权限表';


-- 表__prefix__ku_plugin_local 结构
DROP TABLE IF EXISTS `__prefix__ku_plugin_local`;
CREATE TABLE IF NOT EXISTS `__prefix__ku_plugin_local` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '插件ID',
  `market_id` int(10) unsigned DEFAULT NULL COMMENT '插件市场id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '插件标识 唯一',
  `title` varchar(30) NOT NULL COMMENT '插件名称',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '插件简介',
  `version` varchar(20) NOT NULL DEFAULT '' COMMENT '插件版本号',
  `kucoder_version` varchar(20) NOT NULL DEFAULT '' COMMENT '插件版本号',
  `img` varchar(255) NOT NULL DEFAULT '' COMMENT '插件图片',
  `plugin_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '插件类型:0=辅助开发插件,1=完整独立系统,2=完整sass系统,3=物联网应用,4=AI应用',
  `fee_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '付费类型:0=免费,1=收费',
  `common_price` decimal(10,2) DEFAULT NULL COMMENT '普通授权价格',
  `advance_price` decimal(10,2) DEFAULT NULL COMMENT '高级授权价格',
  `author` varchar(50) NOT NULL DEFAULT '' COMMENT '插件作者',
  `author_id` int(11) NOT NULL COMMENT '插件作者id',
  `homepage` varchar(255) NOT NULL DEFAULT '' COMMENT '插件主页',
  `doc_url` varchar(255) NOT NULL DEFAULT '' COMMENT '插件文档url',
  `source` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '插件来源:1=插件市场,2=本地导入',
  `install` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '插件已安装:0=未安装,1=已安装',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '启用:0=禁用,1=启用',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  `delete_time` datetime DEFAULT NULL COMMENT '软删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='本地插件表';


-- 表__prefix__ku_role 结构
DROP TABLE IF EXISTS `__prefix__ku_role`;
CREATE TABLE IF NOT EXISTS `__prefix__ku_role` (
  `role_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '角色ID',
  `role_name` varchar(30) NOT NULL DEFAULT '' COMMENT '角色名称',
  `rules` mediumtext COMMENT '角色菜单',
  `depts` mediumtext COMMENT '角色部门',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `data_scope` tinyint(3) unsigned DEFAULT '1' COMMENT '数据范围:1=全部数据权限,2=自定数据权限,3=本部门数据权限,4=本部门及以下数据权限',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '角色状态:0=禁用,1=正常',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注',
  `create_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建者',
  `update_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新者',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`role_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='后台用户角色表';


-- 表__prefix__ku_user 结构
DROP TABLE IF EXISTS `__prefix__ku_user`;
CREATE TABLE IF NOT EXISTS `__prefix__ku_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `dept_id` int(10) unsigned DEFAULT NULL COMMENT '部门ID',
  `role_ids` varchar(15000) NOT NULL DEFAULT '' COMMENT '用户角色',
  `username` varchar(30) NOT NULL COMMENT '登录账号',
  `nickname` varchar(30) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `user_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '用户类型:0=系统用户,1=注册用户',
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '用户邮箱',
  `mobile` char(11) DEFAULT '' COMMENT '手机号码',
  `sex` tinyint(1) unsigned zerofill DEFAULT '2' COMMENT '用户性别:0=男,1=女,2=未知',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像路径',
  `password` varchar(100) NOT NULL DEFAULT '' COMMENT '密码',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '账号状态:1=正常,0=停用',
  `del_flag` enum('0','1') NOT NULL DEFAULT '0' COMMENT '删除标志:0=存在,1=删除',
  `login_ip` varchar(128) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_time` datetime DEFAULT NULL COMMENT '最后登录时间',
  `pwd_update_date` datetime DEFAULT NULL COMMENT '密码最后更新时间',
  `is_developer` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '开发者:0=否,1=是',
  `developer_status` tinyint(3) unsigned DEFAULT NULL COMMENT '开发者状态:0=禁用,1=正常',
  `create_uid` int(11) NOT NULL DEFAULT '0' COMMENT '创建者',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_uid` int(11) NOT NULL DEFAULT '0' COMMENT '更新者',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注',
  `delete_time` datetime DEFAULT NULL COMMENT '软删除时间',
  PRIMARY KEY (`user_id`) USING BTREE,
  UNIQUE KEY `username` (`username`) USING BTREE,
  UNIQUE KEY `email` (`email`) USING BTREE,
  UNIQUE KEY `mobile` (`mobile`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='后台用户表';
