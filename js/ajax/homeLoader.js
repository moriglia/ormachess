var scoreboardLoader = null;

function homeInit(){
    scoreboardLoader = ScoreboardLoader("scoreboard", "scoreboardSection");
    scoreboardLoader.loadScoreboard();
    console.log("Initialization completed.");
}
