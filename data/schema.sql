--
-- Table structure for table `user`
--
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(60) NOT NULL,
  `accountId` varchar(20) NOT NULL,
  `createdAt` datetime NULL DEFAULT NULL,
  `updatedAt` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`nickname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `lifetime`;
CREATE TABLE `lifetime` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `top1` int(10) NOT NULL,
  `top10` int(10) NOT NULL,
  `top25` int(10) NOT NULL,
  `matches` int(10) NOT NULL,
  `kills` int(20) NOT NULL,
  `score` int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `stats`;
CREATE TABLE `stats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `top1` int(10) NOT NULL,
  `top10` int(10) NOT NULL,
  `top25` int(10) NOT NULL,
  `matches` int(10) NOT NULL,
  `kills` int(20) NOT NULL,
  `score` int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;