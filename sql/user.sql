/*
Navicat MySQL Data Transfer

Source Server         : wamp
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : train

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2017-10-12 20:03:55
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',
  `phone_number` varchar(11) NOT NULL DEFAULT '0' COMMENT '手机号码',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `authority` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户权限',
  `remember_token` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否记住密码，0代表不记住',
  `salt` varchar(10) NOT NULL DEFAULT '' COMMENT '盐值',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
