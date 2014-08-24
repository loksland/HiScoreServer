package  
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.external.ExternalInterface;
	import flash.geom.Point;
	import flash.net.navigateToURL;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import flash.text.TextField;
	import flash.text.TextFormat;
	import flash.events.IOErrorEvent;
	import flash.events.SecurityErrorEvent;
	import flash.net.SharedObject;
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.utils.setTimeout;
	import flash.events.FocusEvent;
	import flash.events.KeyboardEvent;
	import flash.ui.Keyboard;
	
	public class Test extends MovieClip
	{
		
		// SETTINGS
		
		private const SAVE_SCORE_PATH:String = 'http://%myserver%/saveScore.php';
		private const GET_HISCORES_PATH:String = 'http://%myserver%/getHiScores.php';		
		private const REQUEST_METHOD:String = 'POST';
		private const GAME_SLUG:String = 'mygameb';
		// Gateway field names
		private var FIELDNAME_GAMESLUG:String = 'game_slug';
		private var FIELDNAME_USERNAME:String = 'user_name';
		private var FIELDNAME_USERSCORE:String = 'user_score';		
		// Delay server response. For testing.
		private const SLEEP:Number = 0;
		private var THROW_TEST_ERROR:Boolean = false;
		
		// UI
		
		public var name_tf:TextField;
		public var score_tf:TextField;
		public var btn_save:Sprite;
		public var loading_mc:Sprite;
		public var btn_load:Sprite;		
		public var load_tf:TextField;
		
		public function Test(){
			
			if (stage == null){
				addEventListener(Event.ADDED_TO_STAGE,init,false,0,true);			
			} else {
				init();
			}
		}
		
		private function init(e:Event = null){
		
			btn_save.mouseChildren = false;
			btn_save.buttonMode = true;
			btn_save.addEventListener(MouseEvent.CLICK, saveHiScore, false, 0, true);
			
			btn_load.mouseChildren = false;
			btn_load.buttonMode = true;
			btn_load.addEventListener(MouseEvent.CLICK, loadHiScores, false, 0, true);
			
			enableInput(false);
			showLoading(false);
			showError(false);
			
			var savedName = Read("username");
			if (savedName != null && savedName.length > 0){
				name_tf.text = savedName;
			}
			
		}
		
		private function enableInput(enable){
			
			name_tf.selectable = enable;
			score_tf.selectable = enable;
			
		}
		
		private function showLoading(show){
			
			loading_mc.visible = show;
			
		}
		
		private function showError(show){
			
			error_mc.visible = show;
			
		}
				
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// SAVE SCORE 
		
		private function saveHiScore(e:MouseEvent=null):void {
			
			var userName:String = name_tf.text;
			var userScore:int = int(score_tf.text)
			
			Write("username", userName);				
			enableInput(false);	
			showLoading(true);
			
			var vars:URLVariables = new URLVariables();
			var xml_loader:URLLoader = new URLLoader();
			var path:URLRequest = new URLRequest(SAVE_SCORE_PATH);
			
			path.method = REQUEST_METHOD == 'GET' ? URLRequestMethod.GET : URLRequestMethod.POST;
			
			vars[FIELDNAME_GAMESLUG] = GAME_SLUG;
			vars[FIELDNAME_USERNAME] = userName;
			vars[FIELDNAME_USERSCORE] = userScore;			
			
			if (SLEEP > 0){
				vars['sleep'] = SLEEP;
			}
			
			// Kill cache
			var ck:String = String(Math.round(Math.random()*10000000));
			if (REQUEST_METHOD == 'GET'){
				vars['ck'] = ck;
			} else {
				path.url+="?ck=" + ck;
			}			
			
			path.data = vars;
			
			if (THROW_TEST_ERROR){
				path.url = path.url.split("").reverse().join("");
			}
			
			xml_loader.addEventListener(Event.COMPLETE, onGameSaveComplete, false, 0, true);
			xml_loader.addEventListener(IOErrorEvent.IO_ERROR, onGameSaveError, false, 0, true);
			xml_loader.addEventListener(SecurityErrorEvent.SECURITY_ERROR, onGameSaveError, false, 0, true);	
			xml_loader.load(path);
			
		}
		
		private function onGameSaveError(e:Event){
				
			enableInput(true);	
			showLoading(false);
			showError(true);
		
		}
		
		private function onGameSaveComplete(e:Event):void 
		{
			
			enableInput(true);	
			showLoading(false);
			
			// Pass xml data to see results.
			onLoadHiScoresComplete(e);
			
		}
		
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// GET HIGH SCORES
		
		private function loadHiScores(e:MouseEvent = null):void {
			
			enableInput(false);	
			showLoading(true);	
			
			var path:URLRequest = new URLRequest(GET_HISCORES_PATH);
			path.method = REQUEST_METHOD == 'GET' ? URLRequestMethod.GET : URLRequestMethod.POST;
			
			var vars:URLVariables = new URLVariables();		
			vars[FIELDNAME_GAMESLUG] = GAME_SLUG;
			
			if (SLEEP > 0){
				vars['sleep'] = SLEEP;
			}
			
			// Kill cache
			var ck:String = String(Math.round(Math.random()*10000000));
			if (REQUEST_METHOD == 'GET'){
				vars['ck'] = ck;
			} else {
				path.url+="?ck=" + ck;
			}
			
			path.data = vars;
			
			if (THROW_TEST_ERROR){
				path.url = path.url.split("").reverse().join("");
			}
			
			//If this parameter is omitted, no load operation begins. If specified, the load operation begins immediately
			var loadHiScores:URLLoader = new URLLoader();
			loadHiScores.addEventListener(Event.COMPLETE, onLoadHiScoresComplete, false, 0, true);			
			loadHiScores.addEventListener(IOErrorEvent.IO_ERROR, onLoadHiScoresError, false, 0, true);
			loadHiScores.addEventListener(SecurityErrorEvent.SECURITY_ERROR, onLoadHiScoresError, false, 0, true);			
			
			loadHiScores.load(path);
			
		}
		
		private function onLoadHiScoresError(e:Event) {
			
			enableInput(true);	
			showLoading(false);
			showError(true);			
			
		}
				
		private function onLoadHiScoresComplete(e:Event):void {
			
			enableInput(true);	
			showLoading(false);
				
			var xml:XML = XML(e.currentTarget.data);
			
			var str:String = '';			
			var scores = [] ;
			for(var i:uint = 0 ; i < xml.player.length(); i++) {
			  scores.push({id:i + 1, name:xml..player[i].name, score:xml..player[i].score, is_new: xml..player[i].@is_new == '1',  not_ranked: xml..player[i].@not_ranked == '1'});
			}
			
			var resultStr:String = '';
			for (i = 0; i < scores.length; i++){
				
				if (i > 0){
					resultStr+='\n';
				}
				
				resultStr += '(' + (scores[i].not_ranked ? '-' : scores[i].id) + ')';
				resultStr += scores[i].name + (scores[i].is_new ? "*" : "");
				resultStr += "[" + scores[i].score + "]";				
				resultStr += scores[i].is_new ? " NEW" : "";
			}
			
			load_tf.text = resultStr;
			trace(resultStr);
			
		}
		
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// RETAIN INITIALS
		
		public function Read(name:String):String {
			var lso:SharedObject;
			lso = SharedObject.getLocal(GAME_SLUG + "-hiscores/" + name, "/");
			var value = (lso.data.text == null) ? "" : lso.data.text;
			lso.flush();
			
			return value;
		}
		
		public function Write(name:String, value:String) {
			var lso:SharedObject;
			lso = SharedObject.getLocal(GAME_SLUG + "-hiscores/" + name, "/");
			lso.data.text = value;
			lso.flush();
		}
	}
}