<?
error_reporting(E_ALL);

if (!isset($_POST["command"])) {
	require("inc/error.php.inc");
}

$file = $_FILES['upload']['tmp_name'];
$MAXFILESIZE = 5 * 1024;
$filecontent = "";
$command = $_POST["command"];
$host    = $_POST["host"];
$state	= $_POST["state"];

# SQLIte Datenbank ...
if ($con = sqlite3_open('data/post.db')) {
	# Tabelle ggf. erzeugen
	$q = @sqlite3_query($con,'SELECT host FROM events WHERE id = 1');
	if ($q === false) {
		print "CREATE TABLE - ";
		sqlite3_exec($con,'CREATE TABLE events ( id INTEGER PRIMARY KEY, host varchar(255), command varchar(255), state int, log TEXT, statedate date);');
	} else {
		sqlite3_query_close($q);
	}

	# Logfile einlesen
	$size = filesize($file);
	if ($size > $MAXFILESIZE ) {
		$size = $MAXFILESIZE;
		$filecontent = "--TRUNCATED--\n";
	}
	$fh = fopen($file,'r');
	$filecontent = $filecontent . fread($fh,$size);
	fclose($fh);
	$filecontent = str_replace("'","\\",$filecontent); # sql santizie

	$filecontent = preg_replace("/\s$/","",$filecontent);

	if ($state <> 1 && $state <> 0 ) {
		$filecontent = "UNKNWON STATE : $state\n$filecontent";
		$state = 0;
	}


   if (!sqlite3_exec($con,"insert into events(host,command,state,log,statedate) values('$host','$command','$state','$filecontent',DATETIME('now','localtime') );") ) {
		print sqlite3_error($con) ;
		exit(0);
	}
	sqlite3_close($con);
	print "INSERTED\n";
} else {
	print "ERROR\n";
	exit(1);
}
?>
