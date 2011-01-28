CREATE TABLE `dkp_char` (
`char_id`  smallint(5) NOT NULL AUTO_INCREMENT ,
`user_id`  smallint(5) UNSIGNED NOT NULL ,
`char_name`  varchar(12) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ,
`char_guild`  varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' ,
`char_level`  tinyint(2) UNSIGNED NOT NULL DEFAULT 1 ,
`char_race_id`  tinyint(2) UNSIGNED NULL DEFAULT 0 ,
`char_class_id`  tinyint(2) UNSIGNED NULL DEFAULT 0 ,
`char_gender`  tinyint(1) UNSIGNED NULL DEFAULT 0 ,
`char_achievments`  smallint(5) UNSIGNED NULL DEFAULT 0 ,
`char_skill_1_1`  tinyint(2) UNSIGNED NULL DEFAULT 0 ,
`char_skill_1_2`  tinyint(2) UNSIGNED NULL DEFAULT 0 ,
`char_skill_1_3`  tinyint(2) UNSIGNED NULL DEFAULT 0 ,
`char_skill_2_1`  tinyint(2) UNSIGNED NULL DEFAULT 0 ,
`char_skill_2_2`  tinyint(2) UNSIGNED NULL DEFAULT 0 ,
`char_skill_2_3`  tinyint(2) UNSIGNED NULL DEFAULT 0 ,
`char_skill_id`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' ,
`char_prof_1_k`  smallint(5) UNSIGNED NULL DEFAULT NULL ,
`char_prof_1_v`  smallint(3) UNSIGNED NULL DEFAULT 0 ,
`char_prof_2_k`  smallint(5) UNSIGNED NULL DEFAULT NULL ,
`char_prof_2_v`  smallint(3) UNSIGNED NULL DEFAULT 0 ,
`char_prof_archaeology`  smallint(3) UNSIGNED NULL DEFAULT 0 ,
`char_prof_cooking`  smallint(3) UNSIGNED NULL DEFAULT 0 ,
`char_prof_firstaid`  smallint(3) UNSIGNED NULL DEFAULT 0 ,
`char_prof_fishing`  smallint(3) UNSIGNED NULL DEFAULT 0 ,
`char_health`  mediumint(7) UNSIGNED NULL DEFAULT 0 ,
`char_bar_k`  enum('runepower','rage','focus','energy','mana') CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT 'mana' ,
`char_bar_v`  mediumint(7) UNSIGNED NULL DEFAULT 0 ,
`char_2vs2_title`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' ,
`char_2vs2_v`  smallint(5) UNSIGNED NULL DEFAULT 0 ,
`char_3vs3_title`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' ,
`char_3vs3_v`  smallint(5) UNSIGNED NULL DEFAULT 0 ,
`char_5vs5_title`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT '' ,
`char_5vs5_v`  smallint(5) UNSIGNED NULL DEFAULT 0 ,
`char_update`  int(11) UNSIGNED NULL DEFAULT 0 ,
`char_firstraid`  int(11) NULL DEFAULT NULL ,
`char_lastraid`  int(11) NULL DEFAULT NULL ,
PRIMARY KEY (`char_id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_bin
AUTO_INCREMENT=1
ROW_FORMAT=COMPACT
;
