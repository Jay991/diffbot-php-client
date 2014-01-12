<?php /*


  *** PHP INTERFACE FOR DIFFBOT API ***

  Please check README.md for details and examples.

								    */

class diffbot {

  /* interface settings. you are free to change them after construct */
  var $logfile = "diffbot.log";
  var $timeformat = "Y-m-d H:i:sP";
  var $timezone = "PST";

  /* uncomment this if you want trace info */
  var $tracefile = "diffbot.trc";
  
  /* there should be no reason to change this */
  var $diffbot_base = "http://api.diffbot.com/v%d/%s?";


  /* these should not be changed after construct */
  private $token, $version;
  
  public function __construct($token, $version=2){

    if(!function_exists("json_decode"))
      throw new Exception("php5-json not installed! See: http://php.net/manual/en/json.installation.php for details.");
  
    $this->token	= $token;
    $this->version	= $version;
  }
  
  /* our logging functions */
  private function dolog($msg){
    if($this->logfile)
    return file_put_contents($this->logfile, $this->dateTime().": $msg\n", FILE_APPEND );
  }

  private function log_msg($msg){
    return $this->dolog("info: $msg") || true;		/* always true */
  }

  private function log_error($msg){
    return $this->dolog("error: $msg") && false;	/* always false */
  }

  private function dateTime(){
    $datetime = @new DateTime("now", new DateTimeZone($this->timezone));
    return $datetime->format($this->timeformat);
  }
  
  private function dotrace($msg){
    if($this->tracefile)
      return file_put_contents($this->tracefile, $this->dateTime().": $msg\n", FILE_APPEND );
  }

  /* the base of all API calls */
  private function api_call($api, &$url, &$fields=array(), $optargs=array() ){	/* optargs must be an associated array with key/value pairs to be passed*/
    $poll_uri = sprintf($this->diffbot_base, $this->version, $api)
      ."token=".$this->token
      ."&url=".urlencode($url)
      ."&fields=".implode(",",$fields)
      ;
    
    if(count($optargs))foreach($optargs as $key=>$value){
      $poll_uri.=sprintf("&%s=%s", urlencode($key), urlencode($value));
    }
    
    $this->dotrace("request: $poll_uri");
    
    /* we use HTTP GET, so to minimize dependencies, file_get_contents is enouguh */
    $content = @file_get_contents($poll_uri);
    $this->dotrace("response headers: ".json_encode($http_response_header) );
    $this->dotrace("response body: $content");
    if(!$content)return $this->log_error("cannot read Diffbot api URL");
    if(!$ob=json_decode($content))$this->log_error("response is not a JSON object");
    
    $this->log_msg("calling $api for $url");
    
    return $ob;
  }
  
  
  

  /*
      Public API calls follow here
  
      One function for each Diffbot API (parameters may change in the future)
  
  */
  
  public function analyze($url, $fields=array()){
    return $this->api_call("analyze", $url, $fields);
  }
  
  public function article($url, $fields=array()){
    return $this->api_call("article", $url, $fields);
  }
  
  public function frontpage($url, $fields=array() ){
    return $this->api_call("frontpage", $url, $fields, array("format"=>"json") );	/* forcing JSON format as the default is XML */
  }
  
  public function product($url, $fields=array()){
    return $this->api_call("product", $url, $fields);
  }
  
  public function image($url, $fields=array()){
    return $this->api_call("image", $url, $fields);
  }
  
/*
  public function classifier($url, $fields=array()){
    return $this->api_call("classifier", $url, $fields);
  }
*/
  
}
