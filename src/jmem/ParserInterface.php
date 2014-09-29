<?php namespace Jmem;

Interface ParserInterface {

	public function setFile($file);

	public function setBytes($bytes);

	public function setElement($elemnts);

	public function parse();

}