CREATE TABLE `dkp_boss` (
`boss_id`  mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT ,
`raid_id`  mediumint(8) UNSIGNED NOT NULL ,
`npc_id`  mediumint(8) NOT NULL ,
`boss_time`  int(10) UNSIGNED NOT NULL ,
`boss_difficult`  enum('40','25H','25','10H','10','5H','5') CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL COMMENT 'Boss-Schwierigkeitsgrad' ,
PRIMARY KEY (`boss_id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin
;

ALTER TABLE `dkp_boss` 
	ADD CONSTRAINT `fk_raid_id` 
		FOREIGN KEY (`raid_id`) REFERENCES `dkp_raid` (`raid_id`) 
		ON DELETE CASCADE ON UPDATE CASCADE;
