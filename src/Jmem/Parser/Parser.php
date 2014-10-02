<?php namespace Jmem\Parser;

use Jmem\LoaderInterface;

class Parser implements ParserInterface {

    /**
     * Allow us access to the objects
     * that contains all the Information
     * we need to parse the JSON.
     *
     * @var LoaderInterface
     */
    private $loader;

    /**
     * Hold the current stream we are working with.
     *
     * @var String
     */
    private $stream;

    /**
     * Keep track of when we have hit the end of the
     * array that we want to read in.
     *
     * @var bool
     */
    private $arrayEnd = false;

    /**
     * Keep track of the number of times we have seen
     * an opening and closing bracket.
     *
     * @var int
     */
    private $openingBrackets = 0;

    private $closingBrackets = 0;

    public function __construct(LoaderInterface $loader) {
        $this->loader = $loader;
    }

    public function start() {

        if($this->findElement()) {
            // Place the stream right before the first object.
            $this->getObject();

            // Find all the objects!
            while(!$this->arrayEnd) {
                if(!$this->eatWhitespace()) break;

            }
        }

    }

    /**
     * Return bool if we have found the element
     * set by the user or not.
     *
     * @return bool
     */
    private function findElement() {

        // Keep looping while we look for the set element
        while(!feof($this->loader->getFile())){
            $this->stream .= file($this->loader->getFile(), $this->loader->getBytes());

            $streamLength = mb_strlen($this->stream);
            // Check to make sure that we do not miss the string because it has been broken in half.
            if($streamLength > mb_strlen($this->loader->getElement()) && $streamLength > (3 * $this->loader->getBytes())) {
                $this->stream = substr($this->stream, $this->loader->getBytes());
            }

            $found_element = strpos($this->stream, $this->loader->getElement());

            if($found_element !== false) {
                return $this->trimStream();
            }
        }

        $this->arrayEnd = true;
        return false;
    }

    /**
     * Trim the current stream so that everything that is
     * before the current object has been removed.
     */
    private function trimStream() {

        // Get the current position after we have found the element.
        $position = strpos($this->stream, $this->loader->getElement()) + 1;

        while(!feof($this->loader->getFile())) {
            $this->stream .= fread($this->loader->getFile(), $this->loader->getBytes());

            $streamLength = mb_strlen($this->stream);

            for($i = $position; $i < $streamLength; $i++) {

                if(!ctype_space($this->stream{$i}) && $this->stream{$i} == '[' ) {
                    // The stream will now read in the entire object
                    $this->stream = substr($this->stream, ++$i);
                    // Edge case where array was last char in string
                    if($this->stream === false) $this->stream = "";
                    // We are done here.
                    return true;
                }

            }
        }

        $this->arrayEnd = true;
        return false;
    }

    /**
     * This will eat all whitespace before the start of an object
     * and set everything to the proper state.
     */
    private function eatWhitespace() {
        // Eat up array until next object is found.
        while(!feof($this->loader->getFile()) || !$this->arrayEnd) {

            $this->stream .= fread($this->loader->getFile(), $this->loader->getBytes());

            for($i = 0; $i < strlen($this->stream); $i++) {
                if($this->stream{$i} == "{") {
                    // Remove the old part of the object
                    $this->stream = substr($this->stream, $i);
                    $this->openingBrackets = 1;
                    $this->closingBrackets = 0;
                    return true;
                } else if ($this->stream{$i} == "]") {
                    $this->arrayEnd = true;
                    return false;
                }
            }
        }

        $this->arrayEnd = true;
        return false;
    }

    private function readObject() {

    }

}