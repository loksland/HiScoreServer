<?php

include('_init.php');

if (!$ALLOW_TESTING){
	if ($OUTPUT_ERRORS){
		echo "[test.php] ALLOW_TESTING is disabled in settings.";	
	}
	exit();
}

?>

<html>
<head>
<title>High score test form</title>
<style>

html { font-size: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }

body { margin: 0; font-size: 1em; line-height: 1.4; }

html, body, div, span, h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, img, em, strong, sub, sup, tt, b, u, i, dl, dt, dd, ol, ul, li,
fieldset, form, label, table, caption, tbody, tfoot, thead, tr, th, td {
	margin:0;
	padding:0;	
	font-family: "Lucida Console", Monaco, monospace, sans-serif ;
	font-size: 11px;
	color: #444444;
}

.frm div {
	margin:10px;
}

.show {

}

.hide {
	display:none;
}

</style>

<script>

function e(id){ 
	return document.getElementById(id);
}

function handleActionClick(radioCntrl) {
    if (radioCntrl.value == 'saveScore.php'){
    	e('username_field').className = 'show';
    	e('userscore_field').className = 'show';
    } else {
   		e('username_field').className = 'hide';
    	e('userscore_field').className = 'hide';
    }
    
}

function submitForm(){
	
	window.document.testForm.action = window.document.testForm.target_action.value + '?' + String(Math.round(Math.random()*100000));
	window.document.testForm.method = window.document.testForm.method.value;
	window.document.testForm.submit();
	
}

function randUserName(){

	var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	var randVal = '';
	
	for (var i = 0;i < 3; i++){
		var randEle = Math.floor(Math.random()*chars.length);
		var aChar = chars.charAt(randEle);
		randVal = randVal + aChar;
	}

	e('user_name').value = randVal;

}

function randUserScore(){

	var chars = '1234567890';
	
	var randVal = '';
	
	for (var i = 0; i < 3; i++){
		var randEle = Math.floor(Math.random()*chars.length);
		var aChar = chars.charAt(randEle);
		randVal = randVal + aChar;
	}

	e('user_score').value = randVal;

} 

function resetData(){
	
	if (confirm('Clear scores for this game?')){
		window.document.testForm.action = 'purge.php?' + String(Math.round(Math.random()*100000));
		window.document.testForm.method = window.document.testForm.method.value;
		window.document.testForm.purge.value = '1';
		window.document.testForm.submit();
	}
	
	window.document.testForm.purge.value = '0';

}

</script>

<head>
<body>


<div style="float:left; width:260px; height:450px; border:1px solid black; " >

<div style="margin:20px;" class="frm" >

<form name="testForm" method="post" target="output_frame" action="getHiScores.php" >

<input type="hidden" name="purge" value="0" >

<div>
<strong>Game</strong><br>
<select name="game_slug" >
<?php
foreach($VALID_GAME_SLUGS as $gameSlug){
?><option value="<?=$gameSlug ?>" ><?=$gameSlug ?></option><?php
}
?>
</select> <input type="button" value="clear" onclick="resetData();" >
</div>

<div>
<strong>Action</strong><br>
<label><input type="radio" name="target_action" value="getHiScores.php" onclick="handleActionClick(this);" checked="checked" >Get high scores</label><br>
<label><input type="radio" name="target_action" value="saveScore.php" onclick="handleActionClick(this);" >Save score</label>
</div>


<div id="username_field" class="hide" >
<strong>User name</strong> <a href="#" onclick="randUserName();return false;" >rnd</a><br>
<input type="text" name="user_name" id="user_name" >
</div>

<div id="userscore_field" class="hide" >
User score <a href="#" onclick="randUserScore();return false;" >rnd</a><br>
<input type="text" name="user_score" id="user_score" >
</div>

<div>
<strong>Method</strong><br>
<label><input type="radio" name="method" value="post" checked="checked" >POST</label><br>
<label><input type="radio" name="method" value="get" >GET</label>
</div>

<div>
<strong>XML Output</strong><br>
<label><input type="radio" name="output_html" value="1" checked="checked" >HTML</label><br>
<label><input type="radio" name="output_html" value="0" >Normal</label>
</div>

<div>
<strong>Sleep</strong> 
<input type="text" name="sleep" value="0" >
</div>


<div>
<input type="button" value="submit" onClick="submitForm();" >
</div>

</form>

</div>

</div> 

<div style="float:left; mergin-left:20px;" >
<iframe src="about:blank" name="output_frame" id="output_frame" width="700" height="450" ></iframe>
</div>
</body>
</html>