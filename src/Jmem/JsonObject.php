<?php namespace Jmem;



class JsonObject {

    public $number = 0;

	public $stream = "";

    public function __construct($stream, $num) {
        $this->stream = $stream;
        $this->number = $num;
    }

}