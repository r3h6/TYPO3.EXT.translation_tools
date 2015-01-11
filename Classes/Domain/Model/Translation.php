<?php
namespace MONOGON\TranslationTools\Domain\Model;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Remo Häusler <remo.haeusler@hotmail.com>
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
	 * id
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * file
	 *
	 * @var string
	 */
	protected $file = '';

	/**
	 * [$source description]
	 * @var string
	 */
	protected $source = '';

	/**
	 * Units
	 *
	 * @var array
	 */
	protected $units = array();

	/**
	 * Returns the id
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
	 * @return void
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * Returns the file
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
	 * @return void
	 */
	public function setFile($file) {
		$this->file = $file;
	}

	/**
	 * Returns the source
	 *
	 * @return string $source
	 */
	public function getSource(){
		return $this->source;
	}

	/**
	 * Sets the source
	 *
	 * @param string $source
	 * @return object $this
	 */
	public function setSource($source){
		$this->source = $source;
		return $this;
	}

	/**
	 * Adds a TranslationUnit
	 *
	 * @param \MONOGON\TranslationTools\Domain\Model\TranslationUnit $unit
	 * @return void
	 */
	public function addUnit(\MONOGON\TranslationTools\Domain\Model\TranslationUnit $unit) {
		$this->units[$unit->getTargetLanguage()] = $unit;
	}

	/**
	 * Removes a TranslationUnit
	 *
	 * @param \MONOGON\TranslationTools\Domain\Model\TranslationUnit $unitToRemove The TranslationUnit to be removed
	 * @return void
	 */
	public function removeUnit(\MONOGON\TranslationTools\Domain\Model\TranslationUnit $unitToRemove) {
		//$this->units->detach($unitToRemove);
	}

	/**
	 * Returns the units
	 *
	 * @return array $units
	 */
	public function getUnits() {
		return $this->units;
	}

	/**
	 * Sets the units
	 *
	 * @param array $units
	 * @return void
	 */
	public function setUnits(array $units) {
		$this->units = $units;
	}

	public function getUnit ($language){
		return isset($this->units[$language]) ? $this->units[$language]: NULL;
	}
}