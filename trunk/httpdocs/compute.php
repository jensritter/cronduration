<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Processing . . . </title>
</head>
<body>
<?php
error_reporting(E_ALL);
include('adodb/adodb.inc.php');
require("conf/settings.inc.php");



/* Db INIT */
$dbh_p = &ADONewConnection($dsn_p);
if (!$dbh_p) {
	die("No Connection to Postgresql");
}

$dbh_l = &ADONewConnection($dsn_l);
if (!$dbh_l) {
	die("No Connection to SQLite");
}

if (!$dbh_p->Execute("delete from events")) {
	die($dbh_p->ErrorMsg());
}
/*
pg_prepare($dbh_p,"insert-into-temp",'insert into events(host,command,state,log,statedate,id) values (?,?,?,?,?,?);');
$sth_l = sqlite3_query($dbh_l,"select * from events order by id");
if (!$sth_l) {
	die(sqlite3_error($dbh_l));
}

print "<div class='status'>Inserting . . . </div>\n";


*/
?>
</body>
</html>