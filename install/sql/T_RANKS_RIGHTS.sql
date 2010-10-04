CREATE TABLE `dkp_ranks_rights` (`rank_id`  smallint(5) UNSIGNED NOT NULL ,`rank_power`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'right-power to edit needed-power' ,`rank_login`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'right-power to access the page' ,`rank_edit_acc`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,`rank_read_news`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,`rank_write_news`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,`rank_read_forum`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,`rank_read_topic`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,`rank_add_forum`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,`rank_add_topic`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,`rank_add_post`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,`rank_edit_forum`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,`rank_edit_topic`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,`rank_edit_post`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,`rank_rm_forum`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,`rank_rm_topic`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,`rank_rm_post`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,`rank_read_comment`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,`rank_add_comment`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,`rank_rm_comment`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,`rank_edit_comment`  tinyint(3) UNSIGNED NOT NULL DEFAULT 0 ,PRIMARY KEY (`rank_id`),FOREIGN KEY (`rank_id`) REFERENCES `dkp_ranks` (`rank_id`) ON DELETE CASCADE ON UPDATE CASCADE,UNIQUE INDEX `rank_id` USING BTREE (`rank_id`) )ENGINE=InnoDBDEFAULT CHARACTER SET=utf8 COLLATE=utf8_binROW_FORMAT=COMPACT;INSERT INTO dkp_ranks_rights VALUES((SELECT rank_id FROM dkp_ranks WHERE rank_name = 'Administrator'),'255', /*rank_power*/'1', /*rank_login*/'255', /*rank_edit_acc`*/'255', /*rank_read_news`*/'255', /*rank_write_news`*/'255', /*rank_read_forum`*/'255', /*rank_read_topic`*/'255', /*rank_add_forum`*/'255', /*rank_add_topic`*/'255', /*rank_add_post`*/'255', /*rank_edit_forum`*/'255', /*rank_edit_topic`*/'255', /*rank_edit_post`*/'255', /*rank_rm_forum`*/'255', /*rank_rm_topic`*/'255', /*rank_rm_post`*/'255', /*rank_read_comment`*/'255', /*rank_add_comment`*/'255', /*rank_rm_comment`*/'255' /*rank_edit_comment`*/),((SELECT rank_id FROM dkp_ranks WHERE rank_name = 'User'),'25',/*rank_power*/'1', /*rank_login*/'25', /*rank_edit_acc`*/'25', /*rank_read_news`*/'0', /*rank_write_news`*/'25', /*rank_read_forum`*/'25', /*rank_read_topic`*/'0', /*rank_add_forum`*/'25', /*rank_add_topic`*/'25', /*rank_add_post`*/'0', /*rank_edit_forum`*/'0', /*rank_edit_topic`*/'0', /*rank_edit_post`*/'0', /*rank_rm_forum`*/'0', /*rank_rm_topic`*/'0', /*rank_rm_post`*/'25', /*rank_read_comment`*/'25', /*rank_add_comment`*/'0', /*rank_rm_comment`*/'0' /*rank_edit_comment`*/),((SELECT rank_id FROM dkp_ranks WHERE rank_name = 'Guest'),'0',/*rank_power*/'0', /*rank_login*/'0', /*rank_edit_acc`*/'0', /*rank_read_news`*/'0', /*rank_write_news`*/'0', /*rank_read_forum`*/'0', /*rank_read_topic`*/'0', /*rank_add_forum`*/'0', /*rank_add_topic`*/'0', /*rank_add_post`*/'0', /*rank_edit_forum`*/'0', /*rank_edit_topic`*/'0', /*rank_edit_post`*/'0', /*rank_rm_forum`*/'0', /*rank_rm_topic`*/'0', /*rank_rm_post`*/'0', /*rank_read_comment`*/'0', /*rank_add_comment`*/'0', /*rank_rm_comment`*/'0' /*rank_edit_comment`*/)