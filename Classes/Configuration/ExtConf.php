<?php
namespace MONOGON\TranslationTools\Configuration;

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

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 *
 * @package MONOGON
 * @subpackage code_library
 */
class ExtConf {

	/**
	 * @var string
	 */
	const EXT_KEY = 'translation_tools';


	private static $instance;

	/**
	 * @var array
	 */
	protected $configuration = array();

	public static function get ($key){
		$extConf = self::makeInstance();
		$getter = 'get' . ucfirst($key);
		if (method_exists($extConf, $getter)){
			return $extConf->$getter();
		}
		return $extConf->_get($key);
	}

	private static function makeInstance (){
		if (!self::$instance){
			self::$instance = new ExtConf();
		}
		return self::$instance;
	}

	private function __construct() {
		if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::EXT_KEY])) {
			$this->configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::EXT_KEY]);
		}
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	private function _get ($key) {
		if (is_array($this->configuration) && array_key_exists($key, $this->configuration)) {
			return $this->configuration[$key];
		}
		return NULL;
	}

	private function getAllowWriteToExtension (){
		return GeneralUtility::trimExplode(',', $this->_get('allowWriteToExtension'));
	}

	private function getAllowWriteToL10nDir (){
		return GeneralUtility::trimExplode(',', $this->_get('getAllowWriteToL10nDir'));
	}
}

?>