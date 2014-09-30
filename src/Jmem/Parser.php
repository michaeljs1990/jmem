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

    /**
     * Set the file and open up a steam. If the file
     * cannot be opened for reading throw an error.
     *
     * @param $file
     * @throws \Exception
     */
	public function setFile($file) {

        if(is_readable($file)) {
            $open = fopen($file, "rb");
            $this->file = $open;
        } else {
            $this->handleError("{$file} is not readable.");
        }

	}

    /**
     * Set the amount of bytes that should be read in
     * at a time. 1024 bytes is the default value.
     *
     * @param $bytes
     */
	public function setBytes($bytes) {

		$this->bytes = $bytes;

	}

    /**
     * Set the element that we will look for in the json.
     * If this is not a string throw an error.
     *
     * @param $element
     * @throws \Exception
     */
	public function setElement($element) {

        if(is_string($element)) {
            $this->element = $element;
        } else {
            $this->handleError(" element {$element} is not a string.");
        }

	}

    // Get ready to do some heavy lifting.
	public function parse() {

	}

}