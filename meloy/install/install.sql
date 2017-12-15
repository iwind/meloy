CREATE TABLE IF NOT EXISTS `meloy_moduleUsers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `userId` int(11) unsigned DEFAULT '0' COMMENT '用户ID',
  `module` varchar(64) DEFAULT NULL COMMENT '模块代号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='模块权限设置';

CREATE TABLE IF NOT EXISTS `pp_serverUsers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `userId` int(11) unsigned DEFAULT '0' COMMENT '用户ID',
  `serverId` int(11) unsigned DEFAULT '0' COMMENT '主机ID',
  `allow` varchar(128) DEFAULT NULL COMMENT '允许的操作列表：insert,update,read,delete,drop,alter,create',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='服务器权限设置';

CREATE TABLE IF NOT EXISTS `pp_serverDbUsers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `userId` int(11) unsigned DEFAULT '0' COMMENT '用户ID',
  `serverId` int(11) unsigned DEFAULT '0' COMMENT '主机ID',
  `db` varchar(128) DEFAULT NULL COMMENT '数据库名',
  `allow` varchar(128) DEFAULT NULL COMMENT '允许的操作列表：insert,update,read,delete,drop,alter,create',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='数据库权限设置';

CREATE TABLE IF NOT EXISTS `pp_servers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `userId` int(11) unsigned DEFAULT '0' COMMENT '添加者用户ID',
  `typeId` int(11) unsigned DEFAULT '0' COMMENT '类型：1 ES，2 MongoDB，3 Redis',
  `name` varchar(64) DEFAULT NULL COMMENT '名称',
  `host` varchar(64) DEFAULT NULL COMMENT '地址',
  `port` varchar(64) DEFAULT NULL COMMENT '端口',
  `options` varchar(1024) DEFAULT NULL COMMENT '其他参数，用JSON数据格式表示',
  `createdAt` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `state` tinyint(1) unsigned DEFAULT '1' COMMENT '状态：1启用，0禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主机服务器';

CREATE TABLE IF NOT EXISTS `pp_serverTableUsers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `userId` int(11) unsigned DEFAULT '0' COMMENT '用户ID',
  `serverId` int(11) unsigned DEFAULT '0' COMMENT '主机ID',
  `db` varchar(128) DEFAULT NULL COMMENT '数据库名',
  `table` varchar(128) DEFAULT NULL COMMENT '表名',
  `allow` varchar(128) DEFAULT NULL COMMENT '允许的操作列表：insert,update,read,delete,drop,alter',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='表权限设置';

CREATE TABLE IF NOT EXISTS `pp_serverTypes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(128) DEFAULT NULL COMMENT '类型名',
  `code` varchar(128) DEFAULT NULL COMMENT '代号，如es,redis,mongo',
  `order` int(11) unsigned DEFAULT '0' COMMENT '排序',
  `state` tinyint(1) unsigned DEFAULT '1' COMMENT '状态：0禁用，1启用 ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主机类型';

CREATE TABLE IF NOT EXISTS `pp_teams` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `userId` int(11) unsigned DEFAULT '0' COMMENT '创建者用户ID',
  `name` varchar(64) DEFAULT NULL COMMENT '名称',
  `createdAt` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `state` tinyint(1) unsigned DEFAULT '1' COMMENT '状态：1启用，0禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='团队';

CREATE TABLE IF NOT EXISTS `pp_teamUsers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `userId` int(11) unsigned DEFAULT '0' COMMENT '用户ID',
  `teamId` int(11) unsigned DEFAULT '0' COMMENT '团队ID',
  `state` tinyint(1) unsigned DEFAULT '1' COMMENT '状态',
  `isAdmin` tinyint(1) unsigned DEFAULT '0' COMMENT '是否为管理员',
  `createdAt` int(11) unsigned DEFAULT '0' COMMENT '加入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户加入的团队';

CREATE TABLE IF NOT EXISTS `pp_userLogs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `userId` int(11) unsigned DEFAULT '0' COMMENT '用户ID',
  `description` varchar(128) DEFAULT NULL COMMENT '操作描述',
  `createdAt` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='操作记录';

CREATE TABLE IF NOT EXISTS `pp_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `mobile` varchar(11) DEFAULT NULL COMMENT '手机号',
  `nickname` varchar(64) DEFAULT '' COMMENT '昵称',
  `email` varchar(128) DEFAULT NULL COMMENT '登录邮箱',
  `password` varchar(32) DEFAULT NULL COMMENT '密码',
  `createdAt` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `state` tinyint(1) unsigned DEFAULT '1' COMMENT '状态：1启用，0禁用',
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户';

CREATE TABLE IF NOT EXISTS `pp_userSettings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `userId` int(11) unsigned DEFAULT '0' COMMENT '用户ID',
  `name` varchar(64) DEFAULT NULL COMMENT '选项名',
  `value` varchar(2048) DEFAULT NULL COMMENT '选项值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户设置';

