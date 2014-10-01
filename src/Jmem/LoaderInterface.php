<?php namespace Jmem;

Interface LoaderInterface {

	public function setFile($file);

	public function setBytes($bytes);

	public function setElement($elements);

    public function getFile();

    public function getBytes();

    public function getElement();

	public function parse();

}