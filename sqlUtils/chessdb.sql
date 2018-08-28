drop database if exists ormachess;
create database ormachess ;
use ormachess ;

create table user (
    uid int(16) auto_increment not null,
    username varchar(32) not null,
    password varchar(32) not null,
    email varchar(64) not null,
    wins int(64) default 0,
    draws int(64) default 0,
    fails int(64) default 0,
    progress int(64) default 0,
    primary key (uid),
    unique (username)
) engine=InnoDB default charset=utf8 ;

create table chessmatch (
    mid int(16) auto_increment not null,
    white int(16) not null,
    black int(16) not null,
    duration int(16) default null,
    startTime datetime default null,
    status tinyint default null,
        -- 0: in progress,
        -- 1: white won,
        -- 2: black won,
        -- 3: draw
    primary key (mid),
    constraint user_white
        foreign key (white)
        references user(uid)
        on update cascade
        on delete cascade,
    constraint user_black
        foreign key (black)
        references user(uid)
        on update cascade
        on delete cascade
) engine=InnoDB default charset=utf8;

create table matchrequest (
    rid int(16) not null auto_increment,
    white int(16) not null,
    black int(16) not null,
    proposer tinyint,
    duration int(16) not null,
    proposalDate datetime not null,
    status tinyint not null default 0,
        -- 0: pending,
        -- 1: accepted,
        -- 2: declined
    primary key (rid),
    unique (white, black, proposalDate),
    constraint user_request_black
        foreign key (black)
        references user(uid)
        on update cascade
        on delete cascade,
    constraint user_request_white
        foreign key (white)
        references user(uid)
        on update cascade
        on delete cascade
) engine=InnoDB default charset=utf8;

source credentials.sql ;
source chessdbprocedures.sql ;