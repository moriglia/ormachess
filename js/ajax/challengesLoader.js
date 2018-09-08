var sessionDataRetriever = null;
var matchRequestHandler = null;

function challengesInit(){
    sessionDataRetriever = SessionDataRetriever();
    matchRequestHandler = MatchRequestHandler(
        "matchBoard",
        "matchBoardSection",
        sessionDataRetriever.getUsername()
    );

    matchRequestHandler.loadMatches();
}
