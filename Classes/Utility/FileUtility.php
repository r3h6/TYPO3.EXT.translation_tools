<?php
namespace MONOGON\TranslationTools\Utility;


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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Localization\Parser\XliffParser;

/**
 * FileUtility
 */
class FileUtility {

	public static function getLocallangFiles ($cached = TRUE){
		global $BE_USER;

		if ($cached){
			$files = $BE_USER->getSessionData('tx_translationtools:files');
		}

		if (!is_array($files)){
			$files = self::searchLocallangFiles();
			$BE_USER->setAndSaveSessionData('tx_translationtools:files', $files);
		}
		return $files;
	}

	protected static function searchLocallangFiles (){
		// Initialize:
		$extLocations = explode(',', 'typo3/sysext/,typo3/ext/,typo3conf/ext/');
		$extLocations = explode(',', 'typo3conf/ext/');
		$files = array();

		// Traverse extension locations:
		foreach($extLocations as $path) {
			if (is_dir(PATH_site . $path)) {
				$files = GeneralUtility::getAllFilesAndFoldersInPath($files, PATH_site . $path, 'xml,xlf', FALSE, 99, 'Tests');
			}
		}

		// Remove prefixes
		$files = GeneralUtility::removePrefixPathFromList($files, PATH_site);

		// Remove all non-locallang files (looking at the prefix)
		foreach($files as $key => $value)   {
			if (strpos(basename($value), 'locallang') !== 0) {
				unset($files[$key]);
			}
		}

		return $files;
	}

	public static function determineLanguageFile ($sourceFile, $language){

	}
}