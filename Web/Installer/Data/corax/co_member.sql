/*
Navicat MySQL Data Transfer

Source Server         : mysql
Source Server Version : 50624
Source Host           : localhost:3306
Source Database       : corax

Target Server Type    : MYSQL
Target Server Version : 50624
File Encoding         : 65001

Date: 2015-12-19 16:04:01
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for co_member
-- ----------------------------
DROP TABLE IF EXISTS `co_member`;
CREATE TABLE `co_member` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` text COMMENT '用户名',
  `password` varchar(64) DEFAULT NULL COMMENT '登录密码',
  `truename` varchar(64) DEFAULT NULL COMMENT '真实姓名',
  `mobile` varchar(15) DEFAULT NULL COMMENT '联系电话',
  `email` varchar(32) DEFAULT NULL COMMENT '邮箱地址',
  `sex` tinyint(2) DEFAULT NULL COMMENT '性别',
  `headimgurl` varchar(255) DEFAULT NULL COMMENT '头像地址',
  `city` varchar(255) DEFAULT NULL COMMENT '城市',
  `province` varchar(32) DEFAULT NULL COMMENT '省份',
  `country` varchar(32) DEFAULT NULL COMMENT '国家',
  `language` varchar(32) DEFAULT 'zh-cn' COMMENT '语言',
  `score` int(10) DEFAULT '0' COMMENT '金币值',
  `experience` int(10) DEFAULT '0' COMMENT '经验值',
  `unionid` varchar(50) DEFAULT NULL COMMENT '微信第三方ID',
  `login_count` int(10) DEFAULT '0' COMMENT '登录次数',
  `reg_ip` varchar(30) DEFAULT NULL COMMENT '注册IP',
  `reg_time` int(10) DEFAULT NULL COMMENT '注册时间',
  `last_login_ip` varchar(30) DEFAULT NULL COMMENT '最近登录IP',
  `last_login_time` int(10) DEFAULT NULL COMMENT '最近登录时间',
  `status` tinyint(2) DEFAULT '1' COMMENT '状态',
  `is_init` tinyint(2) DEFAULT '0' COMMENT '初始化状态',
  `is_audit` tinyint(2) DEFAULT '0' COMMENT '审核状态',
  `subscribe_time` int(10) DEFAULT NULL COMMENT '用户关注公众号时间',
  `remark` varchar(100) DEFAULT NULL COMMENT '微信用户备注',
  `groupid` int(10) DEFAULT NULL COMMENT '微信端的分组ID',
  `come_from` tinyint(1) DEFAULT '0' COMMENT '来源',
  `login_name` varchar(100) DEFAULT NULL COMMENT 'login_name',
  `login_password` varchar(255) DEFAULT NULL COMMENT '登录密码',
  `manager_id` int(10) DEFAULT '0' COMMENT '公众号管理员ID',
  `level` tinyint(2) DEFAULT '0' COMMENT '管理等级',
  `membership` char(50) DEFAULT '0' COMMENT '会员等级',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of co_member
-- ----------------------------
INSERT INTO `co_member` VALUES ('1', 'admin', '8341a261434a3f7a62f91032355d9fb4', null, '', '784855684@qq.com', null, null, null, null, null, 'zh-cn', '0', '0', null, '0', '0', '1450504044', '0', '0', '1', '0', '1', null, null, null, '0', 'admin', null, '0', '0', '0');
