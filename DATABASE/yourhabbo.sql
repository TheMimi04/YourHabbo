/*
 Navicat Premium Data Transfer

 Source Server         : Debian
 Source Server Type    : MySQL
 Source Server Version : 100137
 Source Host           : 192.168.0.24:3306
 Source Schema         : yh

 Target Server Type    : MySQL
 Target Server Version : 100137
 File Encoding         : 65001

 Date: 09/01/2019 22:17:10
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for sting_access
-- ----------------------------
DROP TABLE IF EXISTS `sting_access`;
CREATE TABLE `sting_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mysql_hôte` varchar(255) DEFAULT NULL,
  `mysql_user` varchar(255) DEFAULT NULL,
  `mysql_pass` varchar(255) DEFAULT NULL,
  `ftp_hôte` varchar(255) DEFAULT NULL,
  `ftp_user` varchar(255) DEFAULT NULL,
  `ftp_pass` varchar(255) DEFAULT NULL,
  `session_ip` varchar(255) DEFAULT NULL,
  `session_user` varchar(255) DEFAULT NULL,
  `session_pass` varchar(255) DEFAULT NULL,
  `session_expiration` datetime DEFAULT NULL,
  `clef_user` int(11) DEFAULT NULL,
  `id_service` int(11) DEFAULT NULL,
  `session_état` enum('En attente','Active','Expiré','Résilié','Suspendu') DEFAULT NULL,
  `swf_variables` text,
  `swf_text` text,
  `swf_override_variables` text,
  `swf_furnidata` text,
  `swf_figuredata` text,
  `swf_productdata` text,
  `swf_game` text,
  `swf_habbo_swf` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_adm_accounts
-- ----------------------------
DROP TABLE IF EXISTS `sting_adm_accounts`;
CREATE TABLE `sting_adm_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_badges
-- ----------------------------
DROP TABLE IF EXISTS `sting_badges`;
CREATE TABLE `sting_badges` (
  `id` int(11) NOT NULL,
  `badge_id` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `public` enum('0','1') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_bans
-- ----------------------------
DROP TABLE IF EXISTS `sting_bans`;
CREATE TABLE `sting_bans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) DEFAULT NULL,
  `type` enum('Shoutbox','IP','Site&IP') DEFAULT NULL,
  `raison` text,
  `ip` varchar(255) DEFAULT NULL,
  `date_expiration` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for sting_bots
-- ----------------------------
DROP TABLE IF EXISTS `sting_bots`;
CREATE TABLE `sting_bots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phrase` text,
  `type` enum('Bonjour','Heure','Recrutement') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_codes_kdo
-- ----------------------------
DROP TABLE IF EXISTS `sting_codes_kdo`;
CREATE TABLE `sting_codes_kdo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(8) DEFAULT NULL,
  `gold` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of sting_codes_kdo
-- ----------------------------
BEGIN;
INSERT INTO `sting_codes_kdo` VALUES (1, 'VJTFU59B', 37);
COMMIT;

-- ----------------------------
-- Table structure for sting_confirm_email
-- ----------------------------
DROP TABLE IF EXISTS `sting_confirm_email`;
CREATE TABLE `sting_confirm_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `clef` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `expiration` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_domains
-- ----------------------------
DROP TABLE IF EXISTS `sting_domains`;
CREATE TABLE `sting_domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `extension` enum('.fr','.eu','.com','.net','.org','.xyz','.tk','.ml','.ga','.cf') NOT NULL,
  `état` enum('En attente','En cours de propagation','Actif','Expiré','Suspendu') NOT NULL,
  `date_order` datetime NOT NULL,
  `date_expiration` datetime NOT NULL,
  `dns_1` text NOT NULL,
  `dns_2` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_extensions
-- ----------------------------
DROP TABLE IF EXISTS `sting_extensions`;
CREATE TABLE `sting_extensions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `extension` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_forum
-- ----------------------------
DROP TABLE IF EXISTS `sting_forum`;
CREATE TABLE `sting_forum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` int(11) DEFAULT NULL,
  `sujet` varchar(50) DEFAULT NULL,
  `contenu` text,
  `catégorie` enum('Discussion','Tutoriel','Aide','Annonce','Important') DEFAULT NULL,
  `état` enum('Ouvert','Épinglé','Fermé') DEFAULT NULL,
  `last_comment` datetime DEFAULT NULL,
  `date_open` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_forum_comments
-- ----------------------------
DROP TABLE IF EXISTS `sting_forum_comments`;
CREATE TABLE `sting_forum_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) DEFAULT NULL,
  `clef_user` int(11) DEFAULT NULL,
  `comment` text,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_forum_likes
-- ----------------------------
DROP TABLE IF EXISTS `sting_forum_likes`;
CREATE TABLE `sting_forum_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) DEFAULT NULL,
  `clef_user` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_friends_requests
-- ----------------------------
DROP TABLE IF EXISTS `sting_friends_requests`;
CREATE TABLE `sting_friends_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `demandeur_clef` int(11) DEFAULT NULL,
  `receveur_clef` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_logs
-- ----------------------------
DROP TABLE IF EXISTS `sting_logs`;
CREATE TABLE `sting_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` int(11) DEFAULT NULL,
  `action` text,
  `ip` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_logs_admin
-- ----------------------------
DROP TABLE IF EXISTS `sting_logs_admin`;
CREATE TABLE `sting_logs_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_logs_payments
-- ----------------------------
DROP TABLE IF EXISTS `sting_logs_payments`;
CREATE TABLE `sting_logs_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `rate` varchar(255) DEFAULT NULL,
  `gold` int(11) NOT NULL,
  `payout` varchar(255) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `navigator` text,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_lotos
-- ----------------------------
DROP TABLE IF EXISTS `sting_lotos`;
CREATE TABLE `sting_lotos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` int(11) DEFAULT NULL,
  `lot` text,
  `date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_notice
-- ----------------------------
DROP TABLE IF EXISTS `sting_notice`;
CREATE TABLE `sting_notice` (
  `content` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_notifications
-- ----------------------------
DROP TABLE IF EXISTS `sting_notifications`;
CREATE TABLE `sting_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` int(11) DEFAULT NULL,
  `avatar` text,
  `content` text,
  `open` enum('0','1') DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_options
-- ----------------------------
DROP TABLE IF EXISTS `sting_options`;
CREATE TABLE `sting_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_orders
-- ----------------------------
DROP TABLE IF EXISTS `sting_orders`;
CREATE TABLE `sting_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `extension` enum('.fr','.eu','.com','.net','.org','.tk','.ml','.xyz','.ga','.cf','.yourhabbo.fr') NOT NULL,
  `état` enum('En attente','Livraison en cours') NOT NULL,
  `plan` varchar(255) NOT NULL,
  `options` enum('Aucune','SWF','Session','SWF + Session') NOT NULL,
  `type` enum('Normal','Premium') DEFAULT NULL,
  `date_order` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_orders_options
-- ----------------------------
DROP TABLE IF EXISTS `sting_orders_options`;
CREATE TABLE `sting_orders_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_service` int(11) DEFAULT NULL,
  `retro` varchar(255) DEFAULT NULL,
  `options` enum('SWF','Session') DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_plans
-- ----------------------------
DROP TABLE IF EXISTS `sting_plans`;
CREATE TABLE `sting_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `cms` varchar(255) NOT NULL,
  `émulateur` varchar(255) NOT NULL,
  `access` varchar(255) NOT NULL,
  `demo` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_profile_comments
-- ----------------------------
DROP TABLE IF EXISTS `sting_profile_comments`;
CREATE TABLE `sting_profile_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `statut_id` int(11) DEFAULT NULL,
  `profile_clef` int(11) DEFAULT NULL,
  `user_clef` int(11) DEFAULT NULL,
  `content` text,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_profile_likes
-- ----------------------------
DROP TABLE IF EXISTS `sting_profile_likes`;
CREATE TABLE `sting_profile_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `statut_id` int(11) DEFAULT NULL,
  `profile_clef` int(11) DEFAULT NULL,
  `user_clef` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_profile_messages
-- ----------------------------
DROP TABLE IF EXISTS `sting_profile_messages`;
CREATE TABLE `sting_profile_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` int(11) DEFAULT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `message` text,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_profile_reports
-- ----------------------------
DROP TABLE IF EXISTS `sting_profile_reports`;
CREATE TABLE `sting_profile_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `statut_id` int(11) DEFAULT NULL,
  `profile_clef` int(11) DEFAULT NULL,
  `user_clef` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_pubs
-- ----------------------------
DROP TABLE IF EXISTS `sting_pubs`;
CREATE TABLE `sting_pubs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `bouton_name` varchar(255) DEFAULT NULL,
  `bouton_lien` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_repairs
-- ----------------------------
DROP TABLE IF EXISTS `sting_repairs`;
CREATE TABLE `sting_repairs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_service` int(11) DEFAULT NULL,
  `type` enum('Redémarrage','CMS','BDD') DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_services
-- ----------------------------
DROP TABLE IF EXISTS `sting_services`;
CREATE TABLE `sting_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `extension` enum('.fr','.eu','.com','.net','.org','.tk','.ml','.xyz','.ga','.cf','.yourhabbo.fr') NOT NULL,
  `plan` varchar(255) NOT NULL,
  `état` enum('Actif','Expiré','Résilié','Suspendu') NOT NULL,
  `date_order` datetime DEFAULT NULL,
  `date_validation` datetime DEFAULT NULL,
  `date_expiration` datetime DEFAULT NULL,
  `options` enum('Aucune','SWF','Session','SWF + Session') DEFAULT NULL,
  `id_service` int(11) DEFAULT NULL,
  `clef_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_settings
-- ----------------------------
DROP TABLE IF EXISTS `sting_settings`;
CREATE TABLE `sting_settings` (
  `name` varchar(30) NOT NULL,
  `description` text,
  `maintenance` enum('0','1') DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `register` enum('1','0') DEFAULT NULL,
  `recrutement` enum('0','1') DEFAULT NULL,
  `shoutbox` enum('1','2','3') DEFAULT NULL,
  `dedipass_public_key` varchar(255) DEFAULT NULL,
  `dedipass_private_key` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of sting_settings
-- ----------------------------
BEGIN;
INSERT INTO `sting_settings` VALUES ('YourHabbo', 'Description', '0', 'http://localhost', '1', '0', '1', NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for sting_shoutbox
-- ----------------------------
DROP TABLE IF EXISTS `sting_shoutbox`;
CREATE TABLE `sting_shoutbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` varchar(255) DEFAULT NULL,
  `message` text,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_subscriptions
-- ----------------------------
DROP TABLE IF EXISTS `sting_subscriptions`;
CREATE TABLE `sting_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` int(11) DEFAULT NULL,
  `expiration` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_support
-- ----------------------------
DROP TABLE IF EXISTS `sting_support`;
CREATE TABLE `sting_support` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` int(11) DEFAULT NULL,
  `sujet` varchar(45) DEFAULT NULL,
  `département` enum('Technique','Commercial') DEFAULT NULL,
  `contenu` text,
  `état` enum('Ouvert','En attente','Répondu','Fermé') DEFAULT NULL,
  `date_open` datetime DEFAULT NULL,
  `last_response` datetime DEFAULT NULL,
  `last_clef` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_support_responses
-- ----------------------------
DROP TABLE IF EXISTS `sting_support_responses`;
CREATE TABLE `sting_support_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) DEFAULT NULL,
  `response` text,
  `clef_user` int(11) DEFAULT NULL,
  `date_response` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_updates
-- ----------------------------
DROP TABLE IF EXISTS `sting_updates`;
CREATE TABLE `sting_updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` int(11) DEFAULT NULL,
  `type_de_tâche` enum('Maintenance','Incident','Amélioration') DEFAULT NULL,
  `catégorie` enum('Site','Web','MySQL','VPS','Serveur dédié','Plesk') DEFAULT NULL,
  `système_exploitation` enum('Plesk','Linux','Windows','Proxmox') DEFAULT NULL,
  `sévérité` enum('Très basse','Basse','Moyenne','Haute','Très haute','Critique') DEFAULT NULL,
  `résumé` varchar(100) DEFAULT NULL,
  `état` enum('En cours','Terminé') DEFAULT NULL,
  `progression` int(11) DEFAULT NULL,
  `contenu` text,
  `date` datetime DEFAULT NULL,
  `date_close` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_users
-- ----------------------------
DROP TABLE IF EXISTS `sting_users`;
CREATE TABLE `sting_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `clef` int(11) NOT NULL,
  `rank` enum('1','2','3','4','5','6','7','8','9','10') NOT NULL,
  `gold` int(11) NOT NULL,
  `avatar` text,
  `registration_ip` varchar(255) DEFAULT NULL,
  `last_ip` varchar(255) DEFAULT NULL,
  `last_connection` datetime DEFAULT NULL,
  `registration` datetime DEFAULT NULL,
  `account_confirmed` enum('0','1','2') DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_users_badges
-- ----------------------------
DROP TABLE IF EXISTS `sting_users_badges`;
CREATE TABLE `sting_users_badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` int(11) DEFAULT NULL,
  `badge_id` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_users_friends
-- ----------------------------
DROP TABLE IF EXISTS `sting_users_friends`;
CREATE TABLE `sting_users_friends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_clef` int(11) DEFAULT NULL,
  `friend_clef` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_users_online
-- ----------------------------
DROP TABLE IF EXISTS `sting_users_online`;
CREATE TABLE `sting_users_online` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `last_connection` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_users_settings
-- ----------------------------
DROP TABLE IF EXISTS `sting_users_settings`;
CREATE TABLE `sting_users_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` int(11) DEFAULT NULL,
  `facebook` text,
  `twitter` text,
  `snapchat` text,
  `instagram` text,
  `statut` enum('1','0') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_view
-- ----------------------------
DROP TABLE IF EXISTS `sting_view`;
CREATE TABLE `sting_view` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` int(11) DEFAULT NULL,
  `étoile` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for sting_warnings
-- ----------------------------
DROP TABLE IF EXISTS `sting_warnings`;
CREATE TABLE `sting_warnings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clef_user` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

SET FOREIGN_KEY_CHECKS = 1;
