<?php
namespace MONOGON\TranslationTools\Domain\Model\Dto;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 R3 H6 <r3h6@outlook.com>
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

use MONOGON\TranslationTools\Utility\FileUtility;

/**
 * Translation
 */
class Demand extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	const FILTER_NONE = 'none';
	const FILTER_MISSING = 'missing';
	const FILTER_TRANSLATED = 'translated';
	const FILTER_CHANGED = 'changed';

	/**
	 *
	 * @var array
	 */
	protected $files = array();

	/**
	 *
	 * @var string
	 */
	protected $label;

	/**
	 *
	 * @var string
	 */
	protected $id;

	/**
	 *
	 * @var string
	 */
	protected $filter = Demand::FILTER_NONE;

	/**
	 *
	 * @var array
	 * @validate MONOGON\TranslationTools\Domain\Validator\AccessableLanguagesValidator
	 */
	protected $languages = array();

	/**
	 * @var MONOGON\TranslationTools\Domain\Repository\SystemLanguageRepository
	 * @inject
	 */
	protected $systemLanguageRepository = NULL;

	/**
	 * Returns the  files
	 *
	 * @return string $files
	 */
	public function getFiles(){
		return $this->files;
	}

	/**
	 * Sets the files
	 *
	 * @param string $files
	 * @return object $this
	 */
	public function setFiles($files){
		$this->files = $files;
		return $this;
	}

	/**
	 * Returns the  label
	 *
	 * @return [type] $label
	 */
	public function getLabel(){
		return $this->label;
	}

	/**
	 * Sets the label
	 *
	 * @param [type] $label
	 * @return object $this
	 */
	public function setLabel($label){
		$this->label = $label;
		return $this;
	}

	/**
	 * Returns the  id
	 *
	 * @return [type] $id
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Sets the id
	 *
	 * @param [type] $id
	 * @return object $this
	 */
	public function setId($id){
		$this->id = $id;
		return $this;
	}

	/**
	 * Returns the languages
	 *
	 * @return array $languages
	 */
	public function getLanguages(){
		if (empty($this->languages)){
			return array('default');
		}
		return $this->languages;
	}

	/**
	 * Sets the languages
	 *
	 * @param array $languages
	 * @return object $this
	 */
	public function setLanguages($languages){
		$this->languages = $languages;
		return $this;
	}

	/**
	 * Returns the filter
	 *
	 * @return string $filter
	 */
	public function getFilter(){
		return $this->filter;
	}

	/**
	 * Sets the filter
	 *
	 * @param string $filter
	 * @return object $this
	 */
	public function setFilter($filter){
		$this->filter = $filter;
		return $this;
	}

	public function getFilterOptions (){
		return array(
			Demand::FILTER_TRANSLATED => 'Translated',
			Demand::FILTER_MISSING => 'Missing',
			Demand::FILTER_CHANGED => 'Changed',
		);
	}

	public function getLanguageOptions (){
		$options = array();
		$systemLanguages = $this->systemLanguageRepository->findAllAccessableSystemLanguages();

		foreach ($systemLanguages as $systemLanguage) {
			$options[$systemLanguage->getFlag()] = $systemLanguage->getTitle();
		}

		return $options;
	}

	public function getFileOptions (){
		$options = array();
		$files = FileUtility::getLocallangFiles();

		foreach ($files as $file) {
			$options[$file] = $file;
		}

		return $options;
	}
}