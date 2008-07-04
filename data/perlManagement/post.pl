#!/usr/bin/perl -w
use strict;
use LWP::UserAgent;
use HTTP::Request::Common;
use HTTP::Headers;
use Sys::Hostname;
use Data::Dumper;
use File::Temp;

my $url = "http://www.jens.org/~hacker/post/post.php";

my $state = "START";
my $command = "UNKNOWN";
$state = shift @ARGV if (@ARGV);
$command = shift @ARGV if (@ARGV);



my $host = hostname();

if ($state =~/^START$/i) {
	$state = "0";
} elsif ($state =~/^STOP$/i) {
	$state = "1";
} else {
	print STDERR "UNKNOWN state\n";
	exit 1;
}




my ($fh, $file) = mkstemp("XXXXX");
while (<>) {
	print $fh $_;
}
close $fh;

my $ua = LWP::UserAgent->new();
my $request = HTTP::Request->new();
my $response = $ua->request(POST $url, Content_Type => 'form-data', 
		Content => [ 
			upload => [$file],
			command => $command ,
			state   => $state, 
			host    => $host
		]
);

my $ret = $response->content;


#print Dumper $response;

unlink $file;

if (!$response->is_success) {
	# HTTP-Errors : 
	print STDERR $response->status_line , "\n";
}

if ($ret =~/^INSERTED\s*$/) {
	exit(0); # Alles OK 
}
if ($ret =~/^CREATE TABLE - INSERTED\s$/) {
	print "WARN - created new Post-Database\n";
	exit(0);
}
print $ret;
exit 1;
