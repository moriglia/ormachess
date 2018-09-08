var scoreboardLoader = null;
var sessionDataRetriever = null;
var matchRequestHandler = null;

function homeInit(){
    scoreboardLoader = ScoreboardLoader("scoreboard", "scoreboardSection");
    scoreboardLoader.loadScoreboard();

    matchRequestHandler = MatchRequestHandler();

    console.log("Initialization completed.");
}
