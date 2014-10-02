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

    public function __construct(LoaderInterface $loader) {
        $this->loader = $loader;
    }

    public function start() {

        if($this->findElement()) {
            // Place the stream right before the first object.
            $this->getObject();

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
                $this->trimStream();
                return true;
            }
        }

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
                }

            }
        }

    }

    /**
     * This will eat all whitespace before the start of an object
     * and set everything to the proper state.
     */
    private function eatWhitespace() {

    }

}