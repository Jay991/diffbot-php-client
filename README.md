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
$diffbot = new diffbot("DEVELOPER_TOKEN");
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

        public object analyze(string $Url [, array $Fields] )
        public object article(string $Url [, array $Fields] )
        public object frontpage(string $Url [, array $Fields] )
        public object product(string $Url [, array $Fields] )
        public object image(string $Url [, array $Fields] )
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

### Example: Call the Analyze API

Code:

```php
require_once 'diffbot.class.php';
$d = new diffbot("DEVELOPER_TOKEN");
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

### Example: Call the Article API

Code:

```php
require_once 'diffbot.class.php';
$d = new diffbot("DEVELOPER_TOKEN");
$fields = array("icon","text","title");
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

* http://diffbot.com/products/automatic/article/
* http://diffbot.com/products/automatic/frontpage/
* http://diffbot.com/products/automatic/product/
* http://diffbot.com/products/automatic/image/

