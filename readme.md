#Readme

*DB-Config (includes/db_conf.php):*
---
file is not in repo
contains only things like:

* db-connection informations
* system-specific options

for example like:

        <?php
            if(!defined('intern'))
                die();
            $dbconf = array(
                'host'  => 'localhost', //DB-Host
                'user'  => 'root',      //DB-Username
                'pwd'   => '',          //DB-User password
                'pre'	=> 'dkp_',      //DB-Table prefix
                'name'  => 'guilddkp'   //DB-Name
            );
        ?>

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

