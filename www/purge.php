<?php

include('_init.php');

// GET GAME SLUG

$gameSlug = getRequestVar(	'game_slug',
							$REQUEST_METHOD,
							$allowNumerals = false, 
							$allowUpperCaseAtoZ = true, 
							$allowLowerCaseAtoZ = true, 
							$minCharLength = 1, 
							$maxCharLength = -1);

$gameSlug = validateGameSlug($gameSlug);

if (strlen($gameSlug) == 0){
	if ($OUTPUT_ERRORS){
		echo '[purge.php] Invalid game slug provided.';
	}
	exit(); 
}

// PURGE?

$purge = getRequestVar(	'purge',
								$REQUEST_METHOD,
								$allowNumerals = true, 
								$allowUpperCaseAtoZ = false, 
								$allowLowerCaseAtoZ = false, 
								$minCharLength = 1, 
								$maxCharLength = 1);
$purge = $purge == '1';	

// GET OUTPUT AS HTML

$outputAsHTML = getRequestVar(	'output_html',
								$REQUEST_METHOD,
								$allowNumerals = true, 
								$allowUpperCaseAtoZ = false, 
								$allowLowerCaseAtoZ = false, 
								$minCharLength = 1, 
								$maxCharLength = 1);
$outputAsHTML = $outputAsHTML == '1';		

// ACTION

if ($purge){

	// SAVE XML

	$xmlFileName = $DATA_DIR . $gameSlug . '.xml';

	if (!file_exists($xmlFileName)){
		if ($OUTPUT_ERRORS){
			echo '[purge.php] Xml data path not found ('.$xmlFileName.').';
		}
		exit();
	}
	
	$xml_file = new DOMDocument('1.0', 'UTF-8');
	$xml_root = $xml_file->createElement("root");
	$xml_file->appendChild($xml_root);
	
	$xml_file->save($xmlFileName);
	
	// READ XML
	
	outputXMLFile($xmlFileName, $outputAsHTML);	
	
}

?>