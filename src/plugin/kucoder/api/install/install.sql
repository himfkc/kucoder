/*
插件数据表sql文件
*/

SET NAMES utf8mb4;
SET
FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for __prefix__member
-- ----------------------------
DROP TABLE IF EXISTS `__prefix__member`;
CREATE TABLE `__prefix__member`
(
    `id`               int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '会员ID',
    `role_ids`         int UNSIGNED NULL DEFAULT NULL COMMENT '会员角色',
    `username`         varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci   NOT NULL COMMENT '会员账号',
    `nickname`         varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci   NOT NULL DEFAULT '' COMMENT '会员昵称',
    `email`            varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '会员邮箱',
    `mobile`           char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '手机号码',
    `sex`              tinyint(1) UNSIGNED ZEROFILL NOT NULL DEFAULT 2 COMMENT '会员性别:0=男,1=女,2=未知',
    `avatar`           varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  NOT NULL DEFAULT '' COMMENT '头像路径',
    `password`         varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  NOT NULL DEFAULT '' COMMENT '会员密码',
    `status`           tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '会员账号状态:1=正常,0=停用',
    `balance`          decimal(10, 2)                                                 NOT NULL DEFAULT 0.00 COMMENT '会员余额',
    `login_ip`         varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  NOT NULL DEFAULT '' COMMENT '最后登录IP',
    `last_login_time`  datetime NULL DEFAULT NULL COMMENT '最后登录时间',
    `pwd_update_date`  datetime NULL DEFAULT NULL COMMENT '密码最后更新时间',
    `is_developer`     tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '开发者:0=否,1=是',
    `developer_status` tinyint UNSIGNED NULL DEFAULT NULL COMMENT '开发者状态:0=禁用,1=正常',
    `remark`           varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '备注',
    `create_time`      datetime                                                       NOT NULL COMMENT '创建时间',
    `update_time`      datetime                                                       NOT NULL COMMENT '更新时间',
    `delete_time`      datetime NULL DEFAULT NULL COMMENT '软删除时间',
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `username`(`username` ASC) USING BTREE,
    INDEX              `mobile`(`mobile` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for __prefix__member_role
-- ----------------------------
DROP TABLE IF EXISTS `__prefix__member_role`;
CREATE TABLE `__prefix__member_role`
(
    `role_id`     int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '角色ID',
    `role_name`   varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci  NOT NULL COMMENT '角色名称',
    `rules`       text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色菜单',
    `level`       tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '角色级别:1=普通,2=标准,3=高级',
    `depts`       text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '角色部门',
    `sort`        int UNSIGNED NOT NULL DEFAULT 0 COMMENT '显示顺序',
    `data_scope`  tinyint UNSIGNED NULL DEFAULT 1 COMMENT '数据范围:1=全部数据权限,2=自定数据权限,3=本部门数据权限,4=本部门及以下数据权限',
    `status`      tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '角色状态:0=禁用,1=正常',
    `remark`      varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
    `create_uid`  int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建者',
    `update_uid`  int UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新者',
    `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
    `update_time` datetime NULL DEFAULT NULL COMMENT '更新时间',
    `delete_time` datetime NULL DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`role_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 115 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色信息表' ROW_FORMAT = Dynamic;

SET
FOREIGN_KEY_CHECKS = 1;