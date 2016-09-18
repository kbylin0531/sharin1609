/*
Navicat MySQL Data Transfer

Source Server         : mysql
Source Server Version : 50624
Source Host           : localhost:3306
Source Database       : corax

Target Server Type    : MYSQL
Target Server Version : 50624
File Encoding         : 65001

Date: 2015-12-19 16:03:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for co_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `co_auth_group`;
CREATE TABLE `co_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(30) DEFAULT NULL COMMENT '分组名称',
  `icon` int(10) unsigned DEFAULT NULL COMMENT '图标',
  `description` text COMMENT '描述信息',
  `status` tinyint(2) DEFAULT '1' COMMENT '状态',
  `type` tinyint(2) DEFAULT '1' COMMENT '类型',
  `rules` text COMMENT '权限',
  `manager_id` int(10) DEFAULT '0' COMMENT '管理员ID',
  `token` varchar(100) DEFAULT NULL COMMENT 'Token',
  `is_default` tinyint(1) DEFAULT '0' COMMENT '是否默认自动加入',
  `qr_code` varchar(255) DEFAULT NULL COMMENT '微信二维码',
  `wechat_group_id` int(10) DEFAULT '-1' COMMENT '微信端的分组ID',
  `wechat_group_name` varchar(100) DEFAULT NULL COMMENT '微信端的分组名',
  `wechat_group_count` int(10) DEFAULT NULL COMMENT '微信端用户数',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否已删除',
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of co_auth_group
-- ----------------------------
INSERT INTO `co_auth_group` VALUES ('1', '默认用户组', null, '通用的用户组', '1', '0', '1,2,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,79,80,81,82,83,84,86,87,88,89,90,91,92,93,94,95,96,97,100,102,103,105,106', '0', '', '0', '', null, '', null, '0');
INSERT INTO `co_auth_group` VALUES ('2', '公众号粉丝组', null, '所有从公众号自动注册的粉丝用户都会自动加入这个用户组', '1', '0', '1,2,5,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,79,80,82,83,84,88,89,90,91,92,93,96,97,100,102,103,195', '0', '', '0', '', null, '', null, '0');
INSERT INTO `co_auth_group` VALUES ('3', '公众号管理组', null, '公众号管理员注册时会自动加入这个用户组', '1', '0', '', '0', '', '0', '', null, '', null, '0');
