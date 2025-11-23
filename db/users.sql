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

 Date: 23/11/2025 21:14:37
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','user') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Andi', 'andi@andi.com', '123', 'user', '2019-01-28 05:15:29', '2025-10-15 13:06:02');
INSERT INTO `users` VALUES (2, 'Budi', 'budi@budi.com', '123456', 'user', '2019-01-28 05:15:29', '2025-10-15 10:37:15');
INSERT INTO `users` VALUES (3, 'Caca', 'caca@caca.com', '123', 'user', '2019-01-28 05:15:29', '2025-10-15 12:24:29');
INSERT INTO `users` VALUES (4, 'Deni', 'deni@deni.com', 'fghij', 'user', '2019-01-28 05:15:29', '2019-01-28 05:15:29');
INSERT INTO `users` VALUES (5, 'Euis', 'euis@euis.com', 'klmno', 'user', '2019-01-28 05:15:29', '2019-01-28 05:15:29');
INSERT INTO `users` VALUES (6, 'Fafa', 'fafa@fafa.com', '123', 'user', '2019-01-28 05:15:29', '2025-10-15 11:31:08');
INSERT INTO `users` VALUES (7, 'bestta', 'best@gmail.com', '123', 'admin', '2025-10-15 10:04:49', '2025-11-23 21:06:36');
INSERT INTO `users` VALUES (8, 'joko', 'java@java.com', '123', 'user', '2025-10-15 12:29:56', '2025-11-23 21:06:25');
INSERT INTO `users` VALUES (9, 'admin', 'adm@adm.com', '123', 'admin', '2025-11-13 16:32:01', '2025-11-23 21:07:28');
INSERT INTO `users` VALUES (10, 'izey', 'izey@izey.com', '123', 'admin', '2025-11-21 11:33:50', '2025-11-23 21:05:53');
INSERT INTO `users` VALUES (11, 'dind', 'din@gmail.com', '123', 'user', '2025-11-21 11:34:30', '2025-11-23 21:07:05');

SET FOREIGN_KEY_CHECKS = 1;
