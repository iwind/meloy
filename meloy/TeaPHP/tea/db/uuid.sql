-- ----------------------------
--  Table structure for `tea_uuid`
-- ----------------------------
DROP TABLE IF EXISTS `tea_uuid`;
CREATE TABLE `tea_uuid` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stub` char(1) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `stub` (`stub`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
--  Table structure for `tea_uuid_copy`
-- ----------------------------
DROP TABLE IF EXISTS `tea_uuid_copy`;
CREATE TABLE `tea_uuid_copy` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stub` char(1) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `stub` (`stub`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
--  Event structure for `update_uuid_copy`
-- ----------------------------
DROP EVENT IF EXISTS `update_uuid_copy`;
delimiter ;;
CREATE DEFINER=`root`@`localhost` EVENT `update_uuid_copy` ON SCHEDULE EVERY 1 SECOND STARTS '1970-01-01 00:00:01' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
	SET @uid = 0;
	SELECT id FROM tea_uuid INTO @uid;
	IF @uid = 0 THEN
		SELECT id FROM tea_uuid_copy INTO @uid;
		INSERT INTO tea_uuid (id,stub) VALUES (@uid,'a');
	ELSEIF @uid > 0 THEN
		UPDATE tea_uuid_copy SET id=@uid LIMIT 1;

	END IF;
END
 ;;
delimiter ;