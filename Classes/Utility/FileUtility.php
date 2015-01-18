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

	/**
	 * [determineLanguageFile description]
	 *
	 * 1. If can write to extension
	 * 2. If can write to l10n
	 * 3. Write to extension l10n_overwrite as xlf,xml or ts
	 *
	 * @param  [type] $identifier [description]
	 * @param  [type] $language   [description]
	 * @return [type]             [description]
	 */
	public static function determineLanguageFile ($identifier, $language){

		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$extConfManager = $objectManager->get('MONOGON\\TranslationTools\\Configuration\\ExtConfManager');
		$extKey = self::extractExtKey($identifier);

		// 1. Check write permission for extension directory
		$allowWriteToExtension = $extConfManager->getAllowWriteToExtension();
		if (in_array($extKey, $allowWriteToExtension)){
			return self::addLanguageToPath($identifier, $language);
		}

		// 2. Check write permission for l10n directory
		$allowWriteToL10nDir = $extConfManager->getAllowWriteToL10nDir();
		if (in_array($extKey, $allowWriteToL10nDir)){
			return self::addLanguageToPath(self::makeL10nPath($identifier), $language);
		}

		// 3. Write to TypoScript file if configured this way
		$useTypeScript = $extConfManager->getUseTypeScript();
		if ($useTypeScript){
			return 'EXT:l10n_overwrite/Configuration/TypoScript/l10n/setup.txt';
		}

		$extensionRepository = $objectManager->get('TYPO3\\CMS\\Extensionmanager\\Domain\\Repository\\ExtensionRepository');
		$isInTER = (boolean) $extensionRepository->countByExtensionKey($extKey);

		// 4. Write to l10n directory if extension is not in TER
		if (!$isInTER){
			return self::addLanguageToPath(self::makeL10nPath($identifier), $language);
		}

		// 5. Write to EXT:l10n_overwrite
		return self::addLanguageToPath(str_replace('typo3conf/ext/', 'EXT:l10n_overwrite/Resources/Private/l10n/', $identifier), $language);
	}

	public static function addLanguageToPath ($identifier, $language){
		return str_replace('/locallang.', "/$language.locallang.", $identifier);
	}

	public static function makeL10nPath ($identifier){
		if (strpos($identifier, 'typo3conf/ext/') === 0){
			return str_replace('typo3conf/ext/', 'typo3conf/l10n/', $identifier);
		}
		throw new \InvalidArgumentException("Could not make l10n path from $identifier!", 1421611864);
	}

	/**
	 * [extractExtKey description]
	 *
	 * @param  string $identifier [description]
	 * @return string|NULL             [description]
	 */
	public static function extractExtKey ($identifier){
		if (preg_match('#^typo3conf[/\\\\]{1,}ext[/\\\\]{1,}([^/\\\\]+)[/\\\\]{1,}#i', $identifier, $matches)){
			return $matches[1];
		}
		return NULL;
	}
}