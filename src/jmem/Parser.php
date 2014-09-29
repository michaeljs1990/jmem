<?php namespace Jmem;

class Parser implements ParserInterface {

    use ExceptionTrait;

	private $file;

	private $bytes;

	private $element;

	public function __construct($file, $element, $bytes = 1024) {

		$this->setFile($file);
		$this->setElement($element);
		$this->setBytes($bytes);

	}

	public function setFile($file) {

        if(is_readable($file)) {
            $open = fopen($file, "rb");
            $this->file = $open;
        } else {
            $this->handleError("{$file} is not readable.");
        }

	}

	public function setBytes($bytes) {

		$this->bytes = $bytes;

	}

	public function setElement($element) {

        if(is_string($element)) {
            $this->element = $element;
        } else {
            $this->handleError(" element {$element} does not exist.");
        }

	}

	public function parse() {

	}

}