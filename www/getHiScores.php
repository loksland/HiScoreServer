<?php

include('_init.php');

// SLEEP

$sleepDuration = getRequestVar('sleep', 
						$REQUEST_METHOD, 
						$allowNumerals = true, 
						$allowUpperCaseAtoZ = false, 
						$allowLowerCaseAtoZ = false, 
						$minCharLength = 1, 
						$maxCharLength = -1);
if (strlen($sleepDuration) > 0 && intval($sleepDuration) > 0){
	sleep(intval($sleepDuration));
}

// GET GAME SLUG

$gameSlug = getRequestVar(	'game_slug',
							$REQUEST_METHOD,
							$allowNumerals = false, 
							$allowUpperCaseAtoZ = true, 
							$allowLowerCaseAtoZ = true, 
							$minCharLength = 1, 
							$maxCharLength = -1);

$gameSlug = validateGameSlug($gameSlug);

// GET OUTPUT AS HTML

$outputAsHTML = getRequestVar(	'output_html',
								$REQUEST_METHOD,
								$allowNumerals = true, 
								$allowUpperCaseAtoZ = false, 
								$allowLowerCaseAtoZ = false, 
								$minCharLength = 1, 
								$maxCharLength = 1);
$outputAsHTML = $outputAsHTML == '1';		

if (strlen($gameSlug) == 0){
	if ($OUTPUT_ERRORS){
		echo '[getHighScores.php] Invalid game slug provided.';
	}
	exit(); 
}

// READ XML

$xmlFileName = $DATA_DIR . $gameSlug . '.xml';

if (!file_exists($xmlFileName)){
	if ($OUTPUT_ERRORS){
		echo '[getHighScores.php] Xml data path not found ('.$xmlFileName.').';
	}
	exit();
}

outputXMLFile($xmlFileName, $outputAsHTML);			

?>