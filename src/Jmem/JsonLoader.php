<?php namespace Jmem;

use Jmem\Parser\Parser;

class JsonLoader implements LoaderInterface {

    use ExceptionTrait;

	private $file;

	private $bytes;

	private $element;
	
	public $fclose_on_destruct=false;
	
	/**
	* $file is the path to the file that you would like to parse though.
	* If the file does not exist an exception will be thrown. Element is
	* the array of objects you would like to have broken up and returned
	* to you.
	*
	* @param String|resource $file
	* @param String $element
	* @param int $bytes (1024 default)
	*/
	public function __construct($file, $element, $bytes = 1024) {
		$this->setFile($file);
		$this->setElement($element);
		$this->setBytes($bytes);

	}
        public function __destruct(){
        if($this->fclose_on_destruct){
            fclose($this->file);
        }
    }
    /**
     * Set the file and open up a steam. If the file
     * cannot be opened for reading throw an error.
     *
     * @param string|resource $file
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function setFile($file) {
        if(is_resource($file) && get_resource_type($file)==='stream' && stream_get_meta_data($file)['seekable']===true){
            $this->file=$file;
            $this->fclose_on_destruct=false;
        }elseif(is_string($file)){
            $f = $file;
            if (preg_match('#^compress\.(.*)://(.*)#', $file, $r)) {
                $f = $r[2];
            }
            if(is_readable($f)) {
                $open = fopen($file, "rb");
                $this->file = $open;
                $this->fclose_on_destruct=true;
            } else {
                $this->handleError("{$file} is not readable.");
            }
        }else{
            throw new \InvalidArgumentException('$file must be either a path to a readable file, or a seekable stream');
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

    /**
     * Return an open stream of the file that has been
     * passed in.
     *
     * @return mixed
     */
    public function getFile() {

        return $this->file;

    }

    /**
     * Return the number of bytes we will
     * be reading in at a time.
     *
     * @return Integer
     */
    public function getBytes() {

        return $this->bytes;

    }

    /**
     * Return the element that contains an array that
     * we are looking for.
     *
     * @return String
     */
    public function getElement() {

        return $this->element;

    }

    // Get ready to do some heavy lifting.
	public function parse() {

        return new Parser($this);

	}

}
