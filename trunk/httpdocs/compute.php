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
	die("Postgresql Error : " . $dbh_p->ErrorMsg());
}

$dbh_l->BeginTrans();
$pst_InsertEvents = $dbh_p->prepare("insert into events(host,command,state,log,statedate,id) values (?,?,?,?,?,?);");

$rs = &$dbh_l->Execute("select * from events order by id");
if (!$rs) {
	$msg = $dbh_l->ErrorMsg();
	$dbh_l->RollbackTrans();
	die($msg);
}

/* Copy SQLITE -> Postgresql */
while (!$rs->EOF) {
	$param = array();
	array_push($param,$rs->fields["host"]);
	array_push($param,$rs->fields["command"]);
	array_push($param,$rs->fields["state"]);
	array_push($param,$rs->fields["log"]);
	array_push($param,$rs->fields["statedate"]);
	array_push($param,$rs->fields["id"]);
	$ok = $dbh_p->Execute($pst_InsertEvents,$param);
	if (!$ok) {
		die($dbh_p->ErrorMsg());
	}
	$rs->MoveNext();
}
$rs->close();
//TODO:1  -- flip this one
//$dbh_l->Execute("delete from events");

$dbh_p->BeginTrans();
// TODO:2 -- flip this one
$dbh_p->Execute("delete from parsed");

$pst_insertparsed = $dbh_p->Prepare('insert into parsed(host,command,start,stop,log_start,log_stop,duration)values(?,?,?,?,?,?,cast(extract(epoch from age(?,?)) as int))');
if (!$pst_insertparsed) {
	$msg = $dbh_p->ErrorMsg();
	$dbh_l->RollbackTrans();
	$dbh_p->RollbackTrans();
	die($msg);
}
$sql = "select * from events where
	host = ? and command = ?
	and state = $STATE_STOP
	and (statedate >= ?)
	and (statedate <= cast(? as timestamp) + interval '$MAX_DURATION hour' )
order by statedate limit 1";

$pst_query = $dbh_p->Prepare($sql);
if (!$pst_query) {
	$msg = $dbh_p->ErrorMsg();
	$dbh_l->RollbackTrans();
	$dbh_p->RollbackTrans();
	die($msg);
}

$pst_clear = $dbh_p->Prepare("delete from events where id = ? or id = ?");
if (!$pst_clear) {
	$msg = $dbh_p->ErrorMsg();
	$dbh_l->RollbackTrans();
	$dbh_p->RollbackTrans();
	die($msg);
}

$rs = $dbh_p->Execute("select * from events where state = 0 order by statedate desc");
if (!$rs) {
	$msg = $dbh_p->ErrorMsg();
	$dbh_l->RollbackTrans();
	$dbh_p->RollbackTrans();
	die($msg);
}
while (!$rs->EOF) {
	$param = array();
	array_push($param,$rs->fields["host"]);
	array_push($param,$rs->fields["command"]);
	array_push($param,$rs->fields["statedate"]);
	array_push($param,$rs->fields["statedate"]);

	$find = $dbh_p->Execute($pst_query,$param);
	if (!$find) {
		$msg = $dbh_p->ErrorMsg();
		$dbh_l->RollbackTrans();
		$dbh_p->RollbackTrans();
		die($msg);
	}
	if ($find->EOF) {
		$dbh_l->RollbackTrans();
		$dbh_p->RollbackTrans();
		die("Nur einen Datensatz f. Event gefunden :" . join(",",$param));
	}
	$id1 = $rs->fields["id"];
	$id2 = $find->fields["id"];
	$param = array();
	array_push($param,$rs->fields["host"]);
	array_push($param,$rs->fields["command"]);
	array_push($param,$rs->fields["statedate"]);
	array_push($param,$find->fields["statedate"]);
	array_push($param,$rs->fields["log"]);
	array_push($param,$find->fields["log"]);
	array_push($param,$find->fields["statedate"]);
	array_push($param,$rs->fields["statedate"]);
	$ok = $dbh_p->Execute($pst_insertparsed,$param);
	if (!$ok) {
		$msg = $dbh_p->ErrorMsg();
		$dbh_l->RollbackTrans();
		$dbh_p->RollbackTrans();
		die($msg);
	}
	$param = array($id1,$id2); // 2x ID
	$ok = $dbh_p->Execute($pst_clear,$param);
	if (!$ok) {
		$msg = $dbh_p->ErrorMsg();
		$dbh_l->RollbackTrans();
		$dbh_p->RollbackTrans();
		die($msg);
	}
	$find->close();
	$rs->MoveNext();
}
$rs->close();



$dbh_l->CommitTrans();
$dbh_l->close();
$dbh_p->CommitTrans();
$dbh_p->close();

?>
</body>
</html>