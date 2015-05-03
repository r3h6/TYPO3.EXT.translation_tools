<?php
namespace MONOGON\TranslationTools\Utility;

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
 * LocalizationUtility
 */
class LocalizationUtility {

	const EXTENSION_NAME = 'TranslationTools';

	/**
	 * [translate description]
	 * @param  string $key       [description]
	 * @param  array  $arguments [description]
	 * @param  string $default   [description]
	 * @return string            [description]
	 */
	public static function translate ($key, $arguments = array(), $default = NULL){

		$value = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, LocalizationUtility::EXTENSION_NAME, $arguments);

		if ($value === NULL){
			$value = ($default !== NULL) ? $default: $key;
			if (is_array($arguments) && $value !== NULL) {
				return vsprintf($value, $arguments);
			}
		}
		return $value;
	}
}