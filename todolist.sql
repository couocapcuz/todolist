/*
 Navicat Premium Data Transfer

 Source Server         : local
 Source Server Type    : MariaDB
 Source Server Version : 100410 (10.4.10-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : todolist

 Target Server Type    : MariaDB
 Target Server Version : 100410 (10.4.10-MariaDB)
 File Encoding         : 65001

 Date: 24/04/2025 13:54:04
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tasks
-- ----------------------------
DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `status` enum('belum','selesai') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'belum',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tasks
-- ----------------------------
INSERT INTO `tasks` VALUES (1, 'tugas 1', 'belum');

SET FOREIGN_KEY_CHECKS = 1;
