Jmem
====

Iterate through large JSON arrays without eating up all your memory.

Example Use
===

To start using jmem add the following line to your composer.json file.

```json
{
    "require": {
        "mschuett/jmem": "dev-master"
    }
}
```

Next setup your autloaded and away you go.

```php
<?php require 'vendor/autoload.php';

$gen = new Jmem\JsonLoader("Hangouts.json", "conversation_state");

foreach($gen->parse()->start() as $obj) {

    $obj->stream;

}
```

About
===

Jmem was written to parse huge JSON files when trying to put google hangouts json data a 200MB+ file into a database. PHP is likely not the best tool for this job however when it's the only thing available it does just fine. It currently takes about 1.5 minutes to break up a 200MB file so running this in the background after an upload is your best bet. A generator is used as to not take up large chunks of memory at any time.

Documentation
===

I have comented the code extreamly well so it is very easy to look through the source code and make changes. With that said here is the main JsonLoader Class.

```php
/**
 * $file is the path to the file that you would like to parse though.
 * If the file does not exist an exception will be thrown. Element is
 * the array of objects you would like to have broken up and returned
 * to you.
 * 
 * @param String $file
 * @param String $element
 * @param int $bytes (1024 default)
 */
```

The generator returns a JSON object that gives you access to the following. Stream contains the full json object and number is the place of of the object in the array starting at 1.

```php
class JsonObject {

    public $number = 0;

	public $stream = "";

    public function __construct($stream, $num) {
        $this->stream = $stream;
        $this->number = $num;
    }

}
```

Contribute
===

Please feel free to post bugs you may find or submit a pull request. I love feedback good and bad!
