CREATE TABLE `dkp_raid` (
`raid_id`  mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
`raid_type`  mediumint(8) UNSIGNED NOT NULL ,
`raid_leader`  smallint(5) NOT NULL ,
`raid_start`  int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Time: Raidbeginn' ,
`raid_end`  int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Time: Raidende' ,
`raid_note`  text CHARACTER SET utf8 COLLATE utf8_bin NULL ,
`raid_plan`  bit NOT NULL DEFAULT b'1' COMMENT '0: durchgefuehrter Raid, 1: geplanter Raid; keine Auflistung unter letzte Raid' ,
PRIMARY KEY (`raid_id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin
;

CREATE TABLE `dkp_raid_type` (
`raid_type`  mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
`raid_name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'Raidname' ,
`raid_short_name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' COMMENT 'Raidabkuerzung' ,
`raid_difficult`  enum('40','25H','25','10H','10','5H','5') CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL COMMENT 'Schwierigkeitsgrad, wenn keine Instanz: NULL' ,
`raid_zone`  mediumint(8) UNSIGNED NULL DEFAULT NULL COMMENT 'MapID, wenn keine Instanz: NULL' ,
`raid_icon`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' ,
PRIMARY KEY (`raid_type`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin
;

ALTER TABLE `dkp_raid` 
	ADD CONSTRAINT `fk_raid_type` 
		FOREIGN KEY (`raid_type`) REFERENCES `dkp_raid_type` (`raid_type`) 
		ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `dkp_raid` 
	ADD CONSTRAINT `fk_raid_leader` 
		FOREIGN KEY (`raid_leader`) REFERENCES `dkp_user` (`user_id`) 
		ON DELETE RESTRICT ON UPDATE CASCADE;
