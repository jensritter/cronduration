<?php

/* Postgres Settings */
$dsn_p ='postgres8://post:post@matrix.jens.org/post';


/* How long do we let the sqlite - database grow ? */
$DELETE_OLDER = 30; # days
/* How long does the longest job run ? ( for finding matching start-stop rows ) */
$MAX_DURATION = 24; # hours

/* System-Constants -- do not alter ! */

/* SQLite Settings */
$DBFILE = dirname(apache_getenv("SCRIPT_FILENAME")) . "/data/post.db";
$dsn_l ="sqlite://" . urlencode($DBFILE) . "/";

$STATE_START=0;
$STATE_STOP=1;
?>