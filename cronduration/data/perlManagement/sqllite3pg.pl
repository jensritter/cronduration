#!/usr/bin/perl -w
use DBI;
use strict;
DBI->trace(0);
my $DELETE_OLDER = 30; # days 
my $MAX_DURATION = 24; # hours

my $STATE_START=0;
my $STATE_STOP=1;

my $dbh_l =   DBI->connect("dbi:SQLite:dbname=post.db","","") || die("Kein SQLite gefunden\n");
my $dbh_p   = DBI->connect("dbi:Pg:dbname=post;host=localhost","hacker","linux") || die("Kein connect to Postgresql\n");
$|=1;

$dbh_p->do("delete from events") or die($dbh_p->errstr);

my $sth_l = $dbh_l->prepare("select * from events order by id") or die($dbh_l->errstr);
my $sth_p = $dbh_p->prepare("insert into events(host,command,state,log,statedate,id) values (?,?,?,?,?,?)") or die($dbh_p->errstr);

print "Inserting\n";
$sth_l->execute or die($dbh_l->errstr);

while ( my $row = $sth_l->fetchrow_hashref) {
	$sth_p->bind_param(1,$row->{"host"});
	$sth_p->bind_param(2,$row->{"command"});
	$sth_p->bind_param(3,$row->{"state"});
	$sth_p->bind_param(4,$row->{"log"});
	$sth_p->bind_param(5,$row->{"statedate"});
	$sth_p->bind_param(6,$row->{"id"});
	$sth_p->execute() or die($dbh_p->errstr);
	print ".";
}
print "\n";

if ($DELETE_OLDER) {
	$dbh_l->do("delete from events where statedate < datetime('now','localtime','-$DELETE_OLDER')")
		or die($dbh_l->errstr);
}

$dbh_p->do('delete from parsed');
my $sth_entries = $dbh_p->prepare('select * from events where state = 0 order by statedate desc');
$sth_entries->execute();
$sth_p = $dbh_p->prepare('insert into parsed(id,host,command,start,stop,log_start,log_stop,duration)values(?,?,?,?,?,?,?,cast(extract(epoch from age(?,?)) as int))')
	or die($dbh_p->errstr);
my $sth_clean = $dbh_p->prepare("delete from events where id = ? or id = ?")
	or die($dbh_p->errstr);

my $sql = "select * from events where 
	host = ? and command = ? 
	and state = $STATE_STOP
	and (statedate >= ?) 
	and (statedate <= cast(? as timestamp) + interval '$MAX_DURATION hour' ) 
order by statedate limit 1
";

print "Parsing\n";
my $sth_find = $dbh_p->prepare($sql) or die($dbh_p->errstr);

while ( my $row = $sth_entries->fetchrow_hashref ) {
	print "x";
	my $host = $row->{"host"};
	my $command = $row->{"command"};
	my $log = $row->{"log"};
	my $date = $row->{"statedate"};

	$sth_find->bind_param(1,$row->{"host"});
	$sth_find->bind_param(2,$row->{"command"});
	$sth_find->bind_param(3,$row->{"statedate"});
	$sth_find->bind_param(4,$row->{"statedate"});

	$sth_find->execute() or die($dbh_p->errstr);
	#print $row->{"host"} , " " , $row->{"command"} ," " , $row->{"statedate"} , "\n";
	while (my $find = $sth_find->fetchrow_hashref) {
		print "X";
		$sth_p->bind_param(1,$row->{"id"});
		$sth_p->bind_param(2,$row->{"host"});
		$sth_p->bind_param(3,$row->{"command"});
		$sth_p->bind_param(4,$row->{"statedate"});
		$sth_p->bind_param(5,$find->{"statedate"});
		$sth_p->bind_param(6,$row->{"log"});
		$sth_p->bind_param(7,$find->{"log"});
		$sth_p->bind_param(8,$find->{"statedate"});
		$sth_p->bind_param(9,$row->{"statedate"});
		$sth_p->execute() or die($dbh_p->errstr);

		$sth_clean->bind_param(1,$find->{"id"});
		$sth_clean->bind_param(2,$row->{"id"});
		$sth_clean->execute() or die($dbh_p->errstr);
	}
}
print "\n";

#$sth_l->finish or die($dbh_l->errstr);
#$sth_p->finish or die($dbh_p->errstr);
#$dbh_l->disconnect or die($dbh_l->errstr);
#$dbh_p->disconnect or die($dbh_p->errstr);

