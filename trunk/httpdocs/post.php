<?
error_reporting(E_ALL);

if (!isset($_POST["command"])) {
	require("inc/error.inc.php");
}

/* Settings : */
$MAXFILESIZE = 5 * 1024;


/* Database checks */
if (!file_exists("data/post.db")) {
	touch("data/post.db");
	chmod("data/post.db",666);
}

/* Getting Data from Post */
$file = $_FILES['upload']['tmp_name'];

$filecontent = "";
$command = $_POST["command"];
$host    = $_POST["host"];
$state	= $_POST["state"];

/* one blob of sourcecode :*/

if ($con = sqlite3_open('data/post.db')) {
	/* create table, if !exists */
	$q = @sqlite3_query($con,'SELECT host FROM events WHERE id = 1');
	if ($q === false) {
		print "CREATE TABLE - ";
		sqlite3_exec($con,'CREATE TABLE events ( id INTEGER PRIMARY KEY, host varchar(255), command varchar(255), state int, log TEXT, statedate date);');
	} else {
		sqlite3_query_close($q);
	}

	/* read logfile */
	$size = filesize($file);
	if ($size > $MAXFILESIZE ) {
		$size = $MAXFILESIZE;
		$filecontent = "--TRUNCATED--\n";
	}
	$fh = fopen($file,'r');
	$filecontent = $filecontent . fread($fh,$size);
	fclose($fh);

	/* Poor-Mens-Security */
	$filecontent = str_replace("'","\\",$filecontent);
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
