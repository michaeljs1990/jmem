<?php namespace Jmem;

Interface ParserInterface {

	public function setFile($file);

	public function setBytes($bytes);

	public function setElement($elements);

	public function parse();

}