--
-- Table structure for table `user`
--
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(60) NOT NULL,
  `createdAt` datetime NOT NULL NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`nickname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `lifetime`;
CREATE TABLE `lifetime` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId`int(10) NOT NULL,
  `soloTop1`int(10) NOT NULL,
  `duoTop1`int(10) NOT NULL,
  `squadTop1`int(10) NOT NULL,
  `top3`int(10) NOT NULL,
  `top5`int(10) NOT NULL,
  `top6`int(10) NOT NULL,
  `top10`int(10) NOT NULL,
  `top12`int(10) NOT NULL,
  `top25`int(10) NOT NULL,
  `soloMatches`int(10) NOT NULL,
  `duoMatches`int(10) NOT NULL,
  `squadMatches`int(10) NOT NULL,
  `soloKills`int(10) NOT NULL,
  `duoKills`int(10) NOT NULL,
  `squadKills`int(10) NOT NULL,
  `soloScore`int(10) NOT NULL,
  `duoScore`int(10) NOT NULL,
  `squadScore`int(10) NOT NULL,
  `updatedAt` datetime NOT NULL,
  `rankSoloScore` int (10) NOT NULL,
  `rankSoloKills` int (10) NOT NULL,
  `rankDuoScore` int (10) NOT NULL,
  `rankDuoKills` int (10) NOT NULL,
  `rankSquadScore` int (10) NOT NULL,
  `rankSquadKills` int (10) NOT NULL,
  `rankSoloTop1` int (10) NOT NULL,
  `rankDuoTop1` int (10) NOT NULL,
  `rankSquadTop1` int (10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`,`updatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `rankSolo`;
CREATE TABLE `rankSolo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId`int(10) NOT NULL,
  `updatedAt` datetime NOT NULL,
  `rankScore` int (10) NOT NULL,
  `rankKills` int (10) NOT NULL,
  `rankTop1` int (10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`,`updatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `rankDuo`;
CREATE TABLE `rankDuo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId`int(10) NOT NULL,
  `updatedAt` datetime NOT NULL,
  `rankScore` int (10) NOT NULL,
  `rankKills` int (10) NOT NULL,
  `rankTop1` int (10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`,`updatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `rankSquad`;
CREATE TABLE `rankSquad` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId`int(10) NOT NULL,
  `updatedAt` datetime NOT NULL,
  `rankScore` int (10) NOT NULL,
  `rankKills` int (10) NOT NULL,
  `rankTop1` int (10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`,`updatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `solo`;
CREATE TABLE `solo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `top1` int(10) NOT NULL,
  `top10` int(10) NOT NULL,
  `top25` int(10) NOT NULL,
  `matches` int(10) NOT NULL,
  `kills` int(20) NOT NULL,
  `score` int(20) NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`,`updatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `duo`;
CREATE TABLE `duo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `top1` int(10) NOT NULL,
  `top5` int(10) NOT NULL,
  `top12` int(10) NOT NULL,
  `matches` int(10) NOT NULL,
  `kills` int(20) NOT NULL,
  `score` int(20) NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`,`updatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `squad`;
CREATE TABLE `squad` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `top1` int(10) NOT NULL,
  `top3` int(10) NOT NULL,
  `top6` int(10) NOT NULL,
  `matches` int(10) NOT NULL,
  `kills` int(20) NOT NULL,
  `score` int(20) NOT NULL,
  `updatedAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`,`updatedAt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;