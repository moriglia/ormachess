drop procedure if exists addUser;
drop procedure if exists authUser;
drop procedure if exists allUsers;
drop procedure if exists userChallenges;
drop procedure if exists addMatchRequest;
drop procedure if exists declineRequest;
drop procedure if exists acceptRequest;
drop procedure if exists canUserPlay;
drop procedure if exists getMatch;
drop procedure if exists updateMatch;

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

create procedure declineRequest(
    in _rid int(16),
    in _uid int(16) -- needed to check whether the user is really the player
)
    begin
        declare var_proposer tinyint default null;
        declare var_white int(16) default null;
        declare var_black int(16) default null;
        declare var_status tinyint default null;

        declare exit handler for sqlexception
            select 1 as error;

        select
            proposer,
            white,
            black,
            status
        into
            var_proposer,
            var_white,
            var_black,
            var_status
        from matchrequest
        where
            rid = _rid
            -- we do not want to update the status if it
            and (white = _uid or black = _uid)
        limit 1; -- not necessary actually

        if var_proposer is null or var_status != 0 then
            select 1 as error;
        elseif var_proposer = 0 and var_white = _uid then
            select 1 as error; -- very strange case...
        elseif var_proposer = 1 and var_black = _uid then
            select 1 as error; -- very strange as well ...
        else
            update matchrequest
            set status = 2 -- declined
            where rid = _rid ;
        end if ;

        select 0 as error;

    end $$

create procedure acceptRequest(
    in _rid int(16),
    in _uid int(16)
)
    begin
        declare var_proposer tinyint default null;
        declare var_white int(16) default null;
        declare var_black int(16) default null;
        declare var_status tinyint default null;
        declare var_duration int(16) default null;

        declare exit handler for sqlexception
        begin
            rollback;
            select 1 as error;
        end ;

        select
            proposer,
            white,
            black,
            status,
            duration
        into
            var_proposer,
            var_white,
            var_black,
            var_status,
            var_duration
        from matchrequest
        where
            rid = _rid
            -- we do not want to update the status if it
            and (white = _uid or black = _uid)
        limit 1; -- not necessary actually

        if var_proposer is null or var_status != 0 then
            select 2 as error, "Match request not foud" as message;
        elseif var_proposer = 0 and var_white = _uid then
            -- very strange case...
            select
                3 as error,
                "Proposer cannot accept his own request" as message;
        elseif var_proposer = 1 and var_black = _uid then
             -- very strange as well ...
            select
                4 as error,
                "Proposer cannot accept his own request" as message;
        else
            begin
                -- update request as accepted
                delete from matchrequest
                where rid = _rid ;

                -- insert new match in chessmatch table
                insert into
                    chessmatch(mid, white, black, proposer, duration, startTime,
                        status, chessboard)
                values (
                    _rid, var_white, var_black,
                    var_proposer, var_duration, now(), 3,
'[12,13,14,15,16,14,13,12,11,11,11,11,11,11,11,11,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,1,1,1,1,1,1,1,2,3,4,5,6,4,3,2]'
);
                select 0 as error;
            end;
        end if ;
    end $$

create procedure canUserPlay(
    in _mid int(16),
    in _uid int(16)
)
    begin
        declare n int default null;

        declare exit handler for sqlexception
            select 1 as error;

        select count(*) into n
        from chessmatch
        where
            mid = _mid
            and (white = _uid or black = _uid);

        if n <> 1 then
            select 1 as error;
        else
            select 0 as error;
        end if ;
    end $$

create procedure getMatch(
    in _mid int(16)
)
    begin
        declare exit handler for sqlexception
            select 1 as error;

        select *
        from chessmatch
        where mid = _mid;

    end $$

create procedure updateMatch(
    in _mid int(16),
    in _chessboard varchar(255),
    in _status tinyint,
    in _turn tinyint
)
    begin
        declare exit handler for sqlexception
            select true as error;

        update chessmatch
        set
            chessboard = _chessboard,
            status = _status,
            turn = _turn
        where mid = _mid ;

        select false as error;
    end $$

delimiter ;
