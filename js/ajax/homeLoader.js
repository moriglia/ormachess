var scoreboardLoader = null;
var sessionDataRetriever = null;
var matchRequestHandler = null;

function homeInit(){
    scoreboardLoader = ScoreboardLoader("scoreboard", "scoreboardSection");
    scoreboardLoader.loadScoreboard();
    
    sessionDataRetriever = SessionDataRetriever();
    matchRequestHandler = MatchRequestHandler(
        "matchBoard",
        "matchBoardSection",
        sessionDataRetriever.getUsername()
    );

    matchRequestHandler.loadMatches();

    console.log("Initialization completed.");
}
