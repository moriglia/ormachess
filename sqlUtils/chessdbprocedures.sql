drop procedure if exists addUser;
drop procedure if exists authUser;
drop procedure if exists allUsers;
drop procedure if exists userMatches; -- renamed in userRequests()
drop procedure if exists userRequests; -- renamed in userChallenges()
drop procedure if exists userChallenges;

delimiter $$

create procedure addUser (
    in _username varchar(32),
    in _password varchar(32),
    in _email varchar(64),
    out error_ tinyint
)
    begin
        declare exit handler for 1062 set error_:= 1;

        insert into `user` (`username`, `password`, `email`) values
            (_username, _password, _email);

        set error_ := 0 ;

    end $$

create procedure authUser (
    in _username varchar(32),
    in _password varchar(32),
    out auth_ boolean,
    out uid_ int(16)
)
    begin
        declare var_pass varchar(32) default null;

        declare exit handler for not found
        begin
            set auth_ := false;
            set uid_ := null;
        end ;

        select uid, password into uid_, var_pass
        from user
        where username = _username
        limit 1; -- not necessary but for oversafety

        select (var_pass = _password) into auth_;

    end $$


create procedure allUsers(
    in _uid int(16)
)
    begin
        declare exit handler for sqlexception
        select 1 as error ;

        select username, wins, draws, fails, progress
        from user
        where uid != _uid ;

    end $$


create procedure userChallenges (
    in _uid int(16)
)
    begin
        declare exit handler for sqlexception
        select 1 as error;

        select *
        from
        (
            select
                rid as id,
                white,
                black,
                proposer,
                duration,
                proposalDate as moment,
                status
            from matchrequest
            where
                white = _uid or black = _uid
            union
            select
                mid as id,
                white,
                black,
                null as proposer,
                duration,
                startTime as moment,
                status
            from chessmatch
            where
                white = _uid or black = _uid
        ) as d
        order by d.moment desc;
    end $$


delimiter ;
