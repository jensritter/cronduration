CREATE TABLE events (
	id int,
	host varchar(255),
	command varchar(255),
	state int,
	log TEXT,
	statedate timestamp,
	PRIMARY KEY (id)
);


create table parsed (
	id int,
	host varchar(255),
	command varchar(255),
	start timestamp,
	stop  timestamp,
	duration int,
	log_start TEXT,
	log_stop  TEXT,

	PRIMARY KEY (id)
);

create index idx_host on events(host,command,state);

grant all on events to post;
grant all on parsed to post;