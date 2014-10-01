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

    public function __construct(LoaderInterface $loader) {
        $this->loader = $loader;
    }

    public function start() {

        if($this->findElement()) {

        }

    }

    private function findElement() {

        while(!feof($this->loader->getFile())){
            $stream .= file($this->loader->getFile(), $this->loader->getBytes());

            $streamLength = mb_strlen($stream);
            // Check to make sure that we do not miss the string because it has been broken in half.
            if($streamLength > mb_strlen($this->loader->getElement()) && $streamLength > (3 * $this->loader->getBytes())) {
                $stream = substr($stream, $this->loader->getBytes());
            }
        }
    }

}