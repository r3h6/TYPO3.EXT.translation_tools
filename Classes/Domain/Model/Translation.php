<?php
namespace MONOGON\TranslationTools\Domain\Model;

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
 * Translation
 */
class Translation extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * [$source description]
	 *
	 * @var string
	 */
	protected $source = NULL;

	/**
	 * [$target description]
	 *
	 * @var string
	 */
	protected $target = NULL;

	/**
	 * [$sourceLanguage description]
	 *
	 * @var string
	 */
	protected $sourceLanguage = NULL;

	/**
	 * [$targetLanguage description]
	 *
	 * @var string
	 */
	protected $targetLanguage = NULL;

	/**
	 * [$id description]
	 *
	 * @var string
	 */
	protected $id = NULL;

	/**
	 * [$file description]
	 *
	 * @var string
	 */
	protected $file = NULL;

	/**
	 * Returns the  source
	 *
	 * @return string $source
	 */
	public function getSource() {
		return $this->source;
	}

	/**
	 * Sets the source
	 *
	 * @param string $source
	 * @return object $this
	 */
	public function setSource($source) {
		$this->source = $source;
		return $this;
	}

	/**
	 * Returns the  target
	 *
	 * @return string $target
	 */
	public function getTarget() {
		return $this->target;
	}

	/**
	 * Sets the target
	 *
	 * @param string $target
	 * @return object $this
	 */
	public function setTarget($target) {
		$this->target = $target;
		return $this;
	}

	/**
	 * Returns the  sourceLanguage
	 *
	 * @return string $sourceLanguage
	 */
	public function getSourceLanguage() {
		return $this->sourceLanguage;
	}

	/**
	 * Sets the sourceLanguage
	 *
	 * @param string $sourceLanguage
	 * @return object $this
	 */
	public function setSourceLanguage($sourceLanguage) {
		$this->sourceLanguage = $sourceLanguage;
		return $this;
	}

	/**
	 * Returns the  targetLanguage
	 *
	 * @return string $targetLanguage
	 */
	public function getTargetLanguage() {
		return $this->targetLanguage;
	}

	/**
	 * Sets the targetLanguage
	 *
	 * @param string $targetLanguage
	 * @return object $this
	 */
	public function setTargetLanguage($targetLanguage) {
		$this->targetLanguage = $targetLanguage;
		return $this;
	}

	/**
	 * Returns the  file
	 *
	 * @return string $file
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 * Sets the file
	 *
	 * @param string $file
	 * @return object $this
	 */
	public function setFile($file) {
		$this->file = $file;
		return $this;
	}

	/**
	 * Returns the  id
	 *
	 * @return string $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Sets the id
	 *
	 * @param string $id
	 * @return object $this
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

}