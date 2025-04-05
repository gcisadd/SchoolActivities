-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1:3306
-- 生成日期： 2025-01-21 09:55:29
-- 服务器版本： 5.7.26
-- PHP 版本： 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `database_work`
--

DELIMITER $$
--
-- 存储过程
--
DROP PROCEDURE IF EXISTS `UpdateExpiredActivities`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateExpiredActivities` ()  BEGIN
    UPDATE activity
    SET status = 2
    WHERE time < CURDATE();
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `activity`
--

DROP TABLE IF EXISTS `activity`;
CREATE TABLE IF NOT EXISTS `activity` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` char(100) DEFAULT NULL,
  `time` date DEFAULT NULL,
  `location` char(100) DEFAULT NULL,
  `number_of_participant` int(11) DEFAULT NULL,
  `asks` text,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`activity_id`),
  KEY `idx_activity_time` (`time`)
) ENGINE=InnoDB AUTO_INCREMENT=2267 DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `activity`
--

INSERT INTO `activity` (`activity_id`, `title`, `time`, `location`, `number_of_participant`, `asks`, `status`) VALUES
(2265, 'first activity', '2025-01-21', 'home', 1, 'first test', 1),
(2266, 'second activity', '2025-01-26', '1', 2, '111', 1);

--
-- 触发器 `activity`
--
DROP TRIGGER IF EXISTS `set_status_to_closed_if_past`;
DELIMITER $$
CREATE TRIGGER `set_status_to_closed_if_past` BEFORE INSERT ON `activity` FOR EACH ROW BEGIN
    IF NEW.time < CURDATE() THEN
        SET NEW.status = 2;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `application`
--

DROP TABLE IF EXISTS `application`;
CREATE TABLE IF NOT EXISTS `application` (
  `common_user_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`common_user_id`,`activity_id`) USING BTREE,
  KEY `AK_activity_id` (`application_id`),
  KEY `FK_submit` (`activity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `application`
--

INSERT INTO `application` (`common_user_id`, `activity_id`, `application_id`) VALUES
(2022155026, 2266, 110),
(2022155025, 2265, 111),
(2022155025, 2266, 112);

-- --------------------------------------------------------

--
-- 表的结构 `application_result`
--

DROP TABLE IF EXISTS `application_result`;
CREATE TABLE IF NOT EXISTS `application_result` (
  `common_user_id` int(11) DEFAULT NULL,
  `application_id` int(11) NOT NULL,
  `result` int(11) NOT NULL,
  PRIMARY KEY (`application_id`),
  KEY `AK_activity_id` (`application_id`),
  KEY `FK_read_result` (`common_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `application_result`
--

INSERT INTO `application_result` (`common_user_id`, `application_id`, `result`) VALUES
(NULL, 103, 2),
(NULL, 104, 2),
(NULL, 105, 2),
(NULL, 106, 2),
(NULL, 107, 2),
(NULL, 108, 2),
(NULL, 109, 2),
(NULL, 110, 1),
(NULL, 111, 1),
(NULL, 112, 1);

-- --------------------------------------------------------

--
-- 表的结构 `common_user`
--

DROP TABLE IF EXISTS `common_user`;
CREATE TABLE IF NOT EXISTS `common_user` (
  `common_user_id` int(11) NOT NULL,
  `name` char(10) DEFAULT NULL,
  PRIMARY KEY (`common_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `common_user`
--

INSERT INTO `common_user` (`common_user_id`, `name`) VALUES
(2022155025, 'ouhaijie'),
(2022155026, 'gongchi');

-- --------------------------------------------------------

--
-- 表的结构 `managed_user`
--

DROP TABLE IF EXISTS `managed_user`;
CREATE TABLE IF NOT EXISTS `managed_user` (
  `managed_user_id` int(11) NOT NULL,
  `name` char(10) DEFAULT NULL,
  PRIMARY KEY (`managed_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `managed_user`
--

INSERT INTO `managed_user` (`managed_user_id`, `name`) VALUES
(111, 'root');

-- --------------------------------------------------------

--
-- 替换视图以便查看 `timeout_activities`
-- （参见下面的实际视图）
--
DROP VIEW IF EXISTS `timeout_activities`;
CREATE TABLE IF NOT EXISTS `timeout_activities` (
`activity_id` int(11)
,`title` char(100)
,`time` date
,`location` char(100)
,`number_of_participant` int(11)
,`asks` text
);

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL,
  `password` int(11) NOT NULL,
  `name` char(10) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`user_id`, `password`, `name`) VALUES
(111, 123456, 'root'),
(2022155025, 123456, 'ouhaijie'),
(2022155026, 123456, 'gongchi');

-- --------------------------------------------------------

--
-- 表的结构 `user_type`
--

DROP TABLE IF EXISTS `user_type`;
CREATE TABLE IF NOT EXISTS `user_type` (
  `user_id` int(11) NOT NULL,
  `type` char(10) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `user_type`
--

INSERT INTO `user_type` (`user_id`, `type`) VALUES
(111, '1'),
(2022155025, '0'),
(2022155026, '0');

-- --------------------------------------------------------

--
-- 视图结构 `timeout_activities`
--
DROP TABLE IF EXISTS `timeout_activities`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `timeout_activities`  AS  select `activity`.`activity_id` AS `activity_id`,`activity`.`title` AS `title`,`activity`.`time` AS `time`,`activity`.`location` AS `location`,`activity`.`number_of_participant` AS `number_of_participant`,`activity`.`asks` AS `asks` from `activity` where (`activity`.`status` = 2) ;

--
-- 限制导出的表
--

--
-- 限制表 `application`
--
ALTER TABLE `application`
  ADD CONSTRAINT `FK_submit` FOREIGN KEY (`activity_id`) REFERENCES `activity` (`activity_id`),
  ADD CONSTRAINT `FK_submit_application` FOREIGN KEY (`common_user_id`) REFERENCES `common_user` (`common_user_id`);

--
-- 限制表 `application_result`
--
ALTER TABLE `application_result`
  ADD CONSTRAINT `FK_read_result` FOREIGN KEY (`common_user_id`) REFERENCES `common_user` (`common_user_id`);

--
-- 限制表 `user_type`
--
ALTER TABLE `user_type`
  ADD CONSTRAINT `FK_type2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

DELIMITER $$
--
-- 事件
--
DROP EVENT `CheckAndUpdateExpiredActivities`$$
CREATE DEFINER=`root`@`localhost` EVENT `CheckAndUpdateExpiredActivities` ON SCHEDULE EVERY 1 MINUTE STARTS '2024-12-13 14:33:27' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
  UPDATE activity
  SET status = 2
  WHERE time < CURDATE();
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
