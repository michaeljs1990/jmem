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

Contribute
===

Please feel free to post bugs you may find or submit a pull request. I love feedback good and bad!
