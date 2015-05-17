*__Note__: This library is now deprecated and outdated. For the version 3 API client, and for a more robust architecture, please see [this one](https://github.com/Swader/diffbot-php-client) instead (currently in beta). As soon as the new library is out of beta, it will fully replace this one.*

# PHP client for the Diffbot API

The Diffbot PHP interface is a class, named *diffbot*. You can create one or
more instances (if neccessary).

## Installation

* Ensure JSON PECL extension is installed on your system. As of PHP 5.2.0,
  the extension is bundled and compiled into PHP by default. For older
  versions, see [json installation](http://php.net/manual/en/json.installation.php) for details.
* Place diffbot.class.php to your PHP library directory (_e.g. /usr/share/php/_) 
* Include the file once to use its functions. E.g.: `require_once '/usr/share/php/diffbot.class.php';`

If JSON is not supported by your system, it will throw an exception.

## Configuration

First, create a diffbot object. The only mandatory parameter is your
personal developer token. The second, optional parameter is the API version.

```php
require_once 'diffbot.class.php';
$diffbot = new diffbot("DEVELOPER_TOKEN", 2);
```

Then, the diffbot object can be used to call the Diffbot API several times.

### Class synopsis

    diffbot {
        /* variables */
        var $logfile = "diffbot.log";
        var $timeformat = "Y-m-d H:i:sP";
        var $timezone = "PST";
        var $tracefile = "diffbot.trc";
        var $diffbot_base = "http://api.diffbot.com/v%d/%s?";
        
        /* methods */
        public __construct(string $Token [, int $Version=2] )
		
		/* automatic APIs */
        public object analyze(string $Url [, array $Fields] )
        public object article(string $Url [, array $Fields] )
        public object frontpage(string $Url [, array $Fields] )
        public object product(string $Url [, array $Fields] )
        public object image(string $Url [, array $Fields] )
        
        /* crawlbot API */
		public object crawlbot_start(string $name, mixed $seeds, mixed $apiQuery=false [, array $Options ] )
		public object crawlbot_pause(string $name)		// pause a runnning job
		public object crawlbot_continue(string $name)	// continue a paused job
		public object crawlbot_restart(string $name)	// restart a job, cleaning previous results
		public object crawlbot_delete(string $name)		// delete a job with all of its results
    }

### Options

Each option is a public variable, you can change its default value after the object is
created. 

* **$logfile** is the filename where API names and passed URLs are logged. If
 set to _false_, no logging performed.
* **$timeformat** is the format of _date()_ function, used in log file.
* **$timezone** is the timezone used in log file.
* **$tracefile** is the file name of the trace file where raw request and
 response data is saved for debugging purposes. In production environment,
 you should set it to _false_ to disable tracing.
* **$diffbot_base** contains the URL pattern to use when calling Diffbot
 API. First value will be replaced to version number, the second will be the
 api name. Usually, you do not need to change this. 

E.g., to disable trace information:

```php
$diffbot->tracefile = false;
```

## Usage

For each API, a different public function can be called. The function name
is the same as the API name. The first, mandatory parameter is the URL to be
analyzed, the second, optional parameter contains the fields to be returned.
Functions return an object hierarchy or _false_ if an error occurs.

### Example 1: Call the Analyze API

Code:

```php
require_once 'diffbot.class.php';
$d = new diffbot("DEVELOPER_TOKEN");
$d->timezone = "CET";	// set the logging timezone to Central European Time
$c = $d->analyze("http://diffbot.com/products/");
var_dump($c);
```

Returns:

    object(stdClass)#2 (4) {
      ["title"]=>           string(17) "Diffbot: Products"
      ["type"]=>            string(4) "serp"
      ["human_language"]=>  string(2) "en"
      ["url"]=>             string(28) "http://diffbot.com/products/"
    }

### Example 2: Call the Article API

Code:

```php
require_once 'diffbot.class.php';
$d = new diffbot("DEVELOPER_TOKEN");
$fields = array("icon","text","title");	// fields to be returned
$c = $d->article("http://diffbot.com/products/", $fields);
var_dump($c);
```

Returns:

    object(stdClass)#2 (6) {
      ["author"]=>
      string(0) ""
      ["icon"]=>
      string(34) "http://diffbot.com/favicon.ico?v=2"
      ["text"]=>
      string(294) ""name": "Automatic APIs", "type": "computer vision", "author": "Diffy", "target": "common web pages"
    "name": "Custom API Toolkit", "type": "custom extraction", "author": "Diffy", "target": "any kind of page"
    "name": "Crawlbot", "type": "spidering", "author": "Diffy", "target": "entire domains""
      ["title"]=>
      string(8) "Products"
      ["type"]=>
      string(7) "article"
      ["url"]=>
      string(28) "http://diffbot.com/products/"
    }

For choosing $Fields, see the official api documentation:

* http://diffbot.com/products/automatic/classifier/
* http://diffbot.com/products/automatic/article/
* http://diffbot.com/products/automatic/frontpage/
* http://diffbot.com/products/automatic/product/
* http://diffbot.com/products/automatic/image/

### Example 3: Submit and control a crawl job

Synopsys:

	public object crawlbot_start(string $name, mixed $seeds, mixed $apiQuery=false [, array $Options ] )

The parameters are:

* **name** - The name of your crawl job.
* **seeds** - The URL(s) to crawl. Pass one URL as a string, more URLs as an array.
* **apiQuery** - If you set this parameter to _false_ or just ignore it, your crawl will run in automatic mode.
 Here you can define what Diffbot API should the crawlbot use. It is an associated array where array keys are:
	* _api_ : one of Diffbot API name, e.g. "article"
	* _fields_ (optional) : array of field names to processed, e.g. array("meta","image")
* **Options** - An associated array for optional crawl arguments and/or refining your crawl. 
 See [crawl documentation](http://diffbot.com/dev/docs/crawl/) for details.

#### Start a job in automatic mode, crawl up to five pages:

```php
require_once 'diffbot.class.php';
$d = new diffbot("DEVELOPER_TOKEN");
$ret = $d->crawlbot_start("testJob","http://diffbot.com/"
  ,false
  ,array("maxToProcess"=>5)
);
print_r($ret->response);
```

Returns:

	Successfully added urls for spidering.

#### Start a job using _product_ api with fields _querystring_ and _meta_ :

```php
require_once 'diffbot.class.php';
$d = new diffbot("DEVELOPER_TOKEN");
$ret = $d->crawlbot_start("testJob","http://diffbot.com/"
  ,array(
    "api"=>"product",
    "fields"=>array("querystring","meta")
  )
  ,array("maxToProcess"=>5));
print_r($ret->response);
```

#### Pause a running crawl job:

```php
require_once 'diffbot.class.php';
$d = new diffbot("DEVELOPER_TOKEN");
$ret = $d->crawlbot_pause("testJob");
```

#### Delete a crawl job with its results:

```php
require_once 'diffbot.class.php';
$d = new diffbot("DEVELOPER_TOKEN");
$ret = $d->crawlbot_delete("testJob");
print_r($ret->response);
```

Returns:

	Successfully deleted job.

