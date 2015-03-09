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
 * Page
 */
class Page extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	const DOKTYPE_SYSFOLDER = 254;

	/**
	 * Title
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * TS Config
	 *
	 * @var string
	 */
	protected $tsConfig = '';

	/**
	 * Is siteroot?
	 *
	 * @var boolean
	 */
	protected $siteRoot = FALSE;

	/**
	 * doktype
	 *
	 * @var integer
	 */
	protected $doktype = 0;

	protected $parsedTsConfig;

	/**
	 * Returns the title
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Returns the tsConfig
	 *
	 * @return string $tsConfig
	 */
	public function getTsConfig() {
		if (!$this->parsedTsConfig){
			$typoScriptParser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\TypoScript\\Parser\\TypoScriptParser');
			$this->parsedTsConfig = $typoScriptParser->parse($this->tsConfig);
		}
		return $this->tsConfig;
	}

	/**
	 * Sets the tsConfig
	 *
	 * @param string $tsConfig
	 * @return void
	 */
	public function setTsConfig($tsConfig) {
		$this->tsConfig = $tsConfig;
	}

	/**
	 * Returns the siteRoot
	 *
	 * @return boolean $siteRoot
	 */
	public function getSiteRoot() {
		return $this->siteRoot;
	}

	/**
	 * Sets the siteRoot
	 *
	 * @param boolean $siteRoot
	 * @return void
	 */
	public function setSiteRoot($siteRoot) {
		$this->siteRoot = $siteRoot;
	}

	/**
	 * Returns the boolean state of siteRoot
	 *
	 * @return boolean
	 */
	public function isSiteRoot() {
		return $this->siteRoot;
	}

	/**
	 * Returns the doktype
	 *
	 * @return integer $doktype
	 */
	public function getDoktype() {
		return $this->doktype;
	}

	/**
	 * Sets the doktype
	 *
	 * @param integer $doktype
	 * @return void
	 */
	public function setDoktype($doktype) {
		$this->doktype = $doktype;
	}

}