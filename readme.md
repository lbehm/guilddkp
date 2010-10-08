#Readme

*Config-Files*
---

_includes/config.php:_

		; <?php die(); ?>
		[general]
		main_page = "viewnews.php" ; Default-Page
		title = "Guild-DKP" ; Default Base-Title in every Page
		online = 1 ; is the Page online or offline
		offline_msg = "Die Seite ist zur Zeit nicht verfuegbar! Bitte versuchen Sie es spaeter wieder. Danke!" ; Messege when the Page is offline
		default_lang = "de_de" ; Default Language
		default_template = "clean" ; Default Template
		session_last_cleanup = 1286576313 ; Last cleanup of the Sessions-table

		[IDS]
		; Methodes to log attacks
		Database = 0
		Email = 0
		File = 1

		[define]
		; misc defines()
		ANONYMOUS = 0
		URI_SESSION = "s"

_includes/db_conf.php:_
Connection-Informations

		; <?php die(); ?>
		[general]
		host = "localhost"
		user = "root"
		pwd = ""
		name = "guilddkp"
		pre = "dkp_"

		[define]
		T_USER = "dkp_user"
		T_SESSIONS = "dkp_sessions"
		T_RANKS_RIGHTS = "dkp_ranks_rights"
		T_RANKS = "dkp_ranks"
		T_NEWS = "dkp_news"


---

*Setup*
---
currently only 5 table-templates are stable in /install/sql/ directory
install all the sql-files in this order:

* T_RANKS.sql
* T_RANKS_RIGHTS.sql
* T_USER.sql
* T_NEWS.sql
* T_SESSIONS.sql

