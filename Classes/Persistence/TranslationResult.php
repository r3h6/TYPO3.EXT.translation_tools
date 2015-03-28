<?php
namespace MONOGON\TranslationTools\Persistence;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 R3 H6 <r3h6@outlook.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 */
class TranslationResult implements \Countable, \Iterator, \ArrayAccess {

	// private $offset;
	private $data;
	// private $whiteList;

	public function __construct() {
		// $this->offset = 0;
		$this->data = array();
		$this->whiteList = array();
	}

	public function addTranslation (\MONOGON\TranslationTools\Domain\Model\Translation $translation){
		// FileUtility::determineLanguageFile($file, $language)
		$offset = $translation->getHashKey();

		if (!isset($this->data[$offset])){
			$this->data[$offset] = array(
				'units' => array(),
				'file' => $translation->getFile(),
				'id' => $translation->getId(),
			);
		}



		$this->data[$offset]['units'][$translation->getTargetLanguage()] = $translation;
	}

	// public function addToWhiteList(\MONOGON\TranslationTools\Domain\Model\Translation $translation){
	// 	$this->whiteList[$translation->getHashKey()] = TRUE;
	// }


	public function rewind() {
		//$this->offset = 0;
		rewind($this->data);
	}

	public function current() {
		return current($this->data);//[$this->offset];
	}

	public function key() {
		return key($this->data);//$this->offset;
	}

	public function next() {
		//$this->offset++;
		next($this->data);
	}

	public function valid() {
		return (key($this->data) !== NULL);
		//return isset($this->data[$this->offset]);
	}

	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->[] = $value;
		} else {
			$this->data[$offset] = $value;
		}
	}

	public function offsetExists($offset) {
		return isset($this->data[$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->data[$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->data[$offset]) ? $this->data[$offset] : NULL;
	}

	public function count (){
		return count($this->data);
	}

	public function toArray(){
		return $this->data;
	}
}