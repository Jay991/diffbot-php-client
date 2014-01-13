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
    
    $this->log_msg("calling $api for $url");
    
    return @$this->diffbot_call($poll_uri);
  }
  
  /* handle the response of the final HTTP API call */
  private function diffbot_call($poll_uri){
    $this->dotrace("request: $poll_uri");
    
    /* we use HTTP GET, so to minimize dependencies, file_get_contents is enouguh */
    $content = @file_get_contents($poll_uri);
    $this->dotrace("response headers: ".json_encode($http_response_header) );
    $this->dotrace("response body: $content");
    if(!$content)return $this->log_error("cannot read Diffbot api URL");
    if(!$ob=json_decode($content))$this->log_error("response is not a JSON object");
    
    return $ob;
  }
  
  
  

  /*
      Public API calls follow here
  
      One function for each Diffbot API (parameters may change in the future)
  
  */
  
  public function analyze($url, $fields=array()){
    return @$this->api_call(__FUNCTION__, $url, $fields);
  }
  
  public function article($url, $fields=array()){
    return @$this->api_call(__FUNCTION__, $url, $fields);
  }
  
  public function frontpage($url, $fields=array() ){
    return @$this->api_call(__FUNCTION__, $url, $fields, array("format"=>"json") );	/* forcing JSON format as the default is XML */
  }
  
  public function product($url, $fields=array()){
    return @$this->api_call(__FUNCTION__, $url, $fields);
  }
  
  public function image($url, $fields=array()){
    return @$this->api_call(__FUNCTION__, $url, $fields);
  }
  
  /* submit a crawl job */
  public function crawlbot_start($name,$seeds,$apiQuery=false,$options=array() ){
    $ME = __FUNCTION__;
    
    if(!$name)return log_error("$ME: no name given");
    if(!$seeds)return log_error("$ME: no seed URL  given");
    if(is_array($seeds))$seeds=implode(" ",$seeds);
    
    if(!$apiQuery){	// crawling in auto mode
      $apiUrl = sprintf($this->diffbot_base, $this->version, "analyze")."mode=auto";
    }else{
      if(!$api=$apiQuery['api'])return log_error("no apiQuery api given");
      $apiUrl = sprintf($this->diffbot_base, $this->version, $api);
      if(is_array($apiQuery['fields']))$apiUrl.=implode(",",$apiQuery['fields']);
    }
    
    $poll_uri = sprintf($this->diffbot_base, $this->version, "crawl")
      ."token={$this->token}&name={$name}&seeds={$seeds}"
      ."&apiUrl=".urlencode($apiUrl)
      ;
    if(is_array($options)&& count($options))
      foreach($options as $key=>$val)
        $poll_uri.="&$key=".urlencode($val);
    
    $this->log_msg("submit crawl job '$name'");
    
    return @$this->diffbot_call($poll_uri);
  }
  
  /* common function to handle pause, continue, restart and delete commands */
  private function crawlbot_control($name,$control){
    $poll_uri = sprintf($this->diffbot_base, $this->version, "crawl")
      ."token={$this->token}&name={$name}&{$control}";
    return @$this->diffbot_call($poll_uri);
  }
  
  public function crawlbot_pause($name){
    return @$this->crawlbot_control($name,"pause=1");
  }
  
  public function crawlbot_continue($name){
    return @$this->crawlbot_control($name,"pause=0");
  }
  
  public function crawlbot_restart($name){
    return @$this->crawlbot_control($name,"restart=1");
  }
  
  public function crawlbot_delete($name){
    return @$this->crawlbot_control($name,"delete=1");
  }
  
}
