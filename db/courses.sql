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

 Date: 23/11/2025 21:14:54
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for courses
-- ----------------------------
DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `course` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mentor` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of courses
-- ----------------------------
INSERT INTO `courses` VALUES (1, 'C++', 'Ari', 'Dr.');
INSERT INTO `courses` VALUES (2, 'C#', 'Ari', 'Dr.');
INSERT INTO `courses` VALUES (4, 'CSS', 'Cania', 'S.Kom');
INSERT INTO `courses` VALUES (5, 'HTML', 'Cania', 'S.Kom');
INSERT INTO `courses` VALUES (6, 'Javascript', 'Cania', 'S.Kom');
INSERT INTO `courses` VALUES (7, 'Python', 'Barry', 'S.T.');
INSERT INTO `courses` VALUES (8, 'Micropython', 'Barry', 'S.T');
INSERT INTO `courses` VALUES (9, 'Java', 'Darren', 'M.T.');
INSERT INTO `courses` VALUES (10, 'Ruby', 'Darren', 'M.T');
INSERT INTO `courses` VALUES (11, 'ASP', 'Bestta', 'M.Kom');
INSERT INTO `courses` VALUES (13, 'spring', 'kiko', 'M. Kom');

SET FOREIGN_KEY_CHECKS = 1;
