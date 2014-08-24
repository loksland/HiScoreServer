<?php

// SETTINGS

$MAX_SCORE_ENTRIES = 5;
$REQUEST_METHOD = 'post';
$OUTPUT_ERRORS = true;
$DATA_DIR = 'data/';
$VALID_GAME_SLUGS = array("mygamea","mygameb");
$ALLOW_TESTING = true;
 
// FUNCTIONS 

function validateGameSlug($gameSlug){

	global $VALID_GAME_SLUGS;
	
	$gameSlug = strToLower($gameSlug);
	$gameSlug = preg_replace("/[^a-z]+/", "", $gameSlug);
	$foundGameSlug = false;
	
	foreach ($VALID_GAME_SLUGS as $slug){
	
		if ($slug == $gameSlug){
			$foundGameSlug = true;
			break;
		}
	}
	
	if (!$foundGameSlug){
		$gameSlug = '';
	}
	
	return $gameSlug;
}

function getRequestVar($varname, $method = 'post', $allowNumerals = true, $allowUpperCaseAtoZ = true, $allowLowerCaseAtoZ = true, $minCharLength = 0, $maxCharLength = -1){
	
	$method = strToLower($method);
	if ($method != 'get'){
		$method = 'post';
	}

	$value = "";
	$success = false;
	
	$foundVar = false;
	if($method=='post' && isset($_POST[$varname])){
		$foundVar = true;
		$value = $_POST[$varname];		
	} else if ($method=='get' && isset($_GET[$varname])){
		$foundVar = true;
		$value = $_GET[$varname];
	}
	
	if ($foundVar){
		
		$value = strip_tags($value);
	
		$regString = '';
		if ($allowNumerals){
			$regString.='0-9';
		}
	
		if ($allowUpperCaseAtoZ){
			$regString.='A-Z';
		}
	
		if ($allowLowerCaseAtoZ){
			$regString.='a-z';
		}
	
		//$value = preg_replace("/[^a-zA-Z0-9]+/", "", $value);
		$value = preg_replace("/[^".$regString."]+/", "", $value);
		
		if ($maxCharLength != -1 && strlen($value) > $maxCharLength){
			$value = substr($value, 0, $maxCharLength);
		}
		
		if (strlen($value) < $minCharLength){
			$success = false;
		} else {
			$success = true;
		}
		
	} 
	
	if (!$success){
		return "";
	} else {
		return $value;
	}
	
}


function outputXMLFile($xmlFileName, $outputAsHTML){
	
	//$dom = new DOMDocument("1.0");
	$dom = dom_import_simplexml(simplexml_load_file($xmlFileName))->ownerDocument;
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$contents = $dom->saveXML();
	
	if ($outputAsHTML){
		pr($contents);
	} else {
		echo $contents;
	}

}

function outputXmlDomDoc($domDoc, $outputAsHTML){
	
	//$dom = new DOMDocument("1.0");
	$dom = $domDoc; //dom_import_simplexml($domDoc)->ownerDocument;
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$contents = $dom->saveXML();
	
	if ($outputAsHTML){
		pr($contents);
	} else {
		echo $contents;
	}

}

// GLOBAL METHODS

// Oututs data in structured HTML format	
function pr($str){
	echo "<pre>";
	ob_start();
	print_r($str);
	$result = ob_get_contents();
	ob_end_clean();	
	$result = str_replace(">","&gt;",$result);
	$result = str_replace("<","&lt;",$result);
	$result = str_replace(" ","&nbsp;",$result);
	$result = str_replace("\n","<br/>",$result); 
	echo $result;
	echo "</pre>";
}

?>