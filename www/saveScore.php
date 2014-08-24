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

$gameSlug = getRequestVar('game_slug', 
						$REQUEST_METHOD, 
						$allowNumerals = false, 
						$allowUpperCaseAtoZ = false, 
						$allowLowerCaseAtoZ = true, 
						$minCharLength = 1, 
						$maxCharLength = -1);
$gameSlug = validateGameSlug($gameSlug);
if (strlen($gameSlug) == 0){
	if ($OUTPUT_ERRORS){
		echo '[saveScore.php] Invalid game slug provided.';
	}
	exit();
}

// GET SCORE

$score = getRequestVar(	'user_score', 
						$REQUEST_METHOD,
						$allowNumerals = true, 
						$allowUpperCaseAtoZ = false, 
						$allowLowerCaseAtoZ = true, 
						$minCharLength = 1, 
						$maxCharLength = 10);


if (strlen($score) == 0){
	if ($OUTPUT_ERRORS){
		echo '[saveScore.php] Invalid score provided.';
	}
	exit();
}
$score = intVal($score);

// GET USER NAME

$name = getRequestVar(	'user_name', 
						$REQUEST_METHOD, 						
						$allowNumerals = true, 
						$allowUpperCaseAtoZ = true, 
						$allowLowerCaseAtoZ = true, 
						$minCharLength = 1, 
						$maxCharLength = 3);

$name = strToUpper($name);

if (strlen($name) == 0){
	if ($OUTPUT_ERRORS){
		echo '[saveScore.php] Invalid name provided.';
	}
	exit();
}

// GET OUTPUT AS HTML

$outputAsHTML = getRequestVar(	'output_html',
								$REQUEST_METHOD,
								$allowNumerals = true, 
								$allowUpperCaseAtoZ = false, 
								$allowLowerCaseAtoZ = false, 
								$minCharLength = 1, 
								$maxCharLength = 1);
$outputAsHTML = $outputAsHTML == '1';		

// SAVE XML

$xmlFileName = $DATA_DIR . $gameSlug . '.xml';

if (!file_exists($xmlFileName)){
	if ($OUTPUT_ERRORS){
		echo '[saveScore.php] Xml data path not found ('.$xmlFileName.').';
	}
	exit();
}
	
$xml_file = new DOMDocument('1.0', 'UTF-8');
$xml_root = $xml_file->createElement("root");
$xml_file->appendChild( $xml_root );

$xml = simplexml_load_file($xmlFileName);
$main_child = $xml->getName();

$user_node = null;
$last_non_user_node = null;

// Make a node for this player's entry
$xml_this_player = $xml_file->createElement("player");
$xml_name   = $xml_file->createElement('name', $name);
$xml_score  = $xml_file->createElement('score', $score);
$xml_this_player->appendChild( $xml_name );
$xml_this_player->appendChild( $xml_score );


$isEnter = false;
$isEnterToLoop = false;
$scoreEntryCounter = 0;
foreach($xml->children() as $child)
{
	
	$isEnterToLoop = true;
	if ($child->score <= $score && !$isEnter)
	{
		$scoreEntryCounter++;		
		$user_node = $xml_root->appendChild( $xml_this_player );
		
		$isEnter = true;
	}
	
	if ($scoreEntryCounter < $MAX_SCORE_ENTRIES){
	
		$scoreEntryCounter++;
		$xml_player = $xml_file->createElement("player");
		
		$xml_name   = $xml_file->createElement('name',$child->name );
		$xml_score  = $xml_file-> createElement('score', $child->score);
		
		$xml_player->appendChild( $xml_name );
		$xml_player->appendChild( $xml_score );	
		
		$last_non_user_node = $xml_root->appendChild( $xml_player );
		
	}
}

if (!$isEnter && $scoreEntryCounter < $MAX_SCORE_ENTRIES)
{
	
	$user_node = $xml_root->appendChild( $xml_this_player );
	
	$isEnter = true;
} 

// Save the XML file
if ($isEnter){
	// Only save if they made changes to the file.
	$xml_file->save($xmlFileName);
} 

// Customise XML for this user

if (!$isEnter && $last_non_user_node != null){
	// Replace last entry with this user to make them feel good
	$xml_root->removeChild($last_non_user_node);
	$user_node = $xml_root->appendChild( $xml_this_player );		
	$user_node->setAttribute('not_ranked', '1');
}

if ($user_node != null){
	$user_node->setAttribute('is_new', '1');
}

outputXmlDomDoc($xml_file, $outputAsHTML);

?>