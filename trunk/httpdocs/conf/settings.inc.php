<?php

/* Postgres Settings */
$dsn_p ='postgres8://post:post@matrix.jens.org/post?persist';


/* How long do we let the sqlite - database grow ? */
$DELETE_OLDER = 30; # days
/* How long does the longest job run ? ( for finding matching start-stop rows ) */
$MAX_DURATION = 24; # hours

/* System-Constants -- do not alter ! */

/* SQLite Settings */
$dsn_l ="sqlite://" . urlencode("/tmp/jens.db") . "/?debug";
var_dump($dsn_l);
$STATE_START=0;
$STATE_STOP=1;
?>