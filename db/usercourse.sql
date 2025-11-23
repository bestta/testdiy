/*
 Navicat Premium Data Transfer

 Source Server         : Local SITB
 Source Server Type    : MySQL
 Source Server Version : 100138
 Source Host           : localhost:3306
 Source Schema         : sekolahku

 Target Server Type    : MySQL
 Target Server Version : 100138
 File Encoding         : 65001

 Date: 23/11/2025 21:14:46
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for usercourse
-- ----------------------------
DROP TABLE IF EXISTS `usercourse`;
CREATE TABLE `usercourse`  (
  `id_user` int NULL DEFAULT NULL,
  `id_course` int NULL DEFAULT NULL,
  INDEX `id_course`(`id_course`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of usercourse
-- ----------------------------
INSERT INTO `usercourse` VALUES (2, 4);
INSERT INTO `usercourse` VALUES (2, 5);
INSERT INTO `usercourse` VALUES (2, 6);
INSERT INTO `usercourse` VALUES (3, 7);
INSERT INTO `usercourse` VALUES (3, 8);
INSERT INTO `usercourse` VALUES (3, 9);
INSERT INTO `usercourse` VALUES (4, 1);
INSERT INTO `usercourse` VALUES (4, 3);
INSERT INTO `usercourse` VALUES (4, 5);
INSERT INTO `usercourse` VALUES (5, 2);
INSERT INTO `usercourse` VALUES (5, 4);
INSERT INTO `usercourse` VALUES (5, 6);
INSERT INTO `usercourse` VALUES (6, 7);
INSERT INTO `usercourse` VALUES (6, 8);
INSERT INTO `usercourse` VALUES (6, 9);
INSERT INTO `usercourse` VALUES (6, 2);
INSERT INTO `usercourse` VALUES (8, 11);
INSERT INTO `usercourse` VALUES (8, 10);
INSERT INTO `usercourse` VALUES (8, 5);
INSERT INTO `usercourse` VALUES (6, 6);
INSERT INTO `usercourse` VALUES (8, 13);
INSERT INTO `usercourse` VALUES (9, 13);
INSERT INTO `usercourse` VALUES (1, 1);
INSERT INTO `usercourse` VALUES (1, 5);
INSERT INTO `usercourse` VALUES (1, 6);

SET FOREIGN_KEY_CHECKS = 1;
