CREATE TABLE `dkp_loot` (
`loot_id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
`char_id`  smallint(5) UNSIGNED NOT NULL ,
`boss_id`  mediumint(8) UNSIGNED NOT NULL ,
`item_id`  mediumint(8) UNSIGNED NOT NULL ,
`loot_time`  int(10) UNSIGNED NULL DEFAULT 0 ,
PRIMARY KEY (`loot_id`),
FOREIGN KEY (`char_id`) REFERENCES `dkp_char` (`char_id`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`boss_id`) REFERENCES `dkp_boss` (`boss_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
FOREIGN KEY (`item_id`) REFERENCES `dkp_items` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin
;
