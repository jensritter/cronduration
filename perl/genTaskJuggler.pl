#!/usr/bin/perl -w
use strict;
use DBI;
DBI->trace(0);

my $dbh_p   = DBI->connect("dbi:Pg:dbname=post;host=matrix.jens.org","hacker","linux") || die("Kein connect to Postgresql\n");

my $sth = $dbh_p->prepare('select * from parsed order by start desc');
$sth->execute();
open OUT,">/tmp/task.tjp" or die($@);
print OUT <<EOF;
project post "Post" "1.0" 2008-01-01 - 2009-01-01 {
	timingresolution 10min
}

taskreport "Gantt Chart" {
  headline "Project Gantt Chart"
  columns hierarchindex, name, start, end, effort, duration, chart
  timeformat "%Y-%m-%d"
  loadunit shortauto
  hideresource 1 
}

task ubuntu "Ubuntu" {
EOF


while ( my $row = $sth->fetchrow_hashref) {	
	my $start = formatTime($row->{start});
	my $duration = $row->{duration};
	$duration = sprintf("%0.0f",$duration / 60);
	if ($duration < 1 ) {
		$duration = 1;
	}
	$duration = $duration . "min";
	print OUT <<EOF;
task x$row->{id} "$row->{host} $row->{command}" {
	start $start
	duration $duration
}
EOF
}

print OUT "}\n";
close OUT;

sub formatTime {
	my $time = shift;
	$time =~ s/\ /-/;

	$time =~s/:\d\d$//; # seconds
#	$time =~s/:\d\d$//;

#	return $time ; # . ":00";

	$time =~s /\d$//;
	$time = $time . "0";
	return $time;
}
