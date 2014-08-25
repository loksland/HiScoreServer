HiScoreServer
=============

A simple PHP highscore server for online games.

- Supports multiple games.

getHiScores.php
---------------

Returns an ordered list of high scores for a game.

- |game_slug| (String) Required. The identifier for the game. For multi-game setups.
- |sleep| (Integer) Optional. The server will delay its response. For simulating load times while developing your application.

saveScore.php
---------------

Attempts to add and rank score provided. Returns highscore list with new entry added, either ranked or unranked.

- |game_slug| (String) Required. The identifier for the game. For multi-game setups.
- |user_score| (Integer) Required. 
- |user_name| (String) Required.
- |sleep| (Integer) Optional. The server will delay its response by |sleep| seconds. For simulating load times while developing your application.
