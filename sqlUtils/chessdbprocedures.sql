drop procedure if exists addUser;
drop procedure if exists authUser;
drop procedure if exists allUsers;
drop procedure if exists userChallenges;
drop procedure if exists addMatchRequest;

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

create procedure addMatchRequest(
    in _white varchar(32),
    in _black varchar(32),
    in _proposer tinyint,
    in _duration int(16)
)
    begin
        declare wid int(16) default null;
        declare bid int(16) default null;
        declare now_var datetime default now();
        declare rid_var int(16) default null;

        declare exit handler for 1062
            select 1 as error;

        select uid into wid
        from user
        where username = _white
        limit 1 ;

        select uid into bid
        from user
        where username = _black
        limit 1 ;

        insert into matchrequest(
            white, black, proposer,
            duration, proposalDate, status
        )
        values (
            wid, bid, _proposer,
            _duration, now_var, 0
        );

        select rid into rid_var
        from matchrequest
        where
            white = wid
            and black = bid
            and proposalDate = now_var
        limit 1 ; -- of course, the tuple is a key

        -- outputs only unknown variables
        select
            rid_var,
            now_var;

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

        select
            d.id as id,
            w.username as white,
            b.username as black,
            d.proposer as proposer,
            d.duration as duration,
            d.moment as moment,
            d.status as status
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
                proposer,
                duration,
                startTime as moment,
                status
            from chessmatch
            where
                white = _uid or black = _uid
        ) as d
            join user w on w.uid = d.white
            join user b on b.uid = d.black
        order by d.moment desc;
    end $$


delimiter ;
