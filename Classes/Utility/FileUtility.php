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
use MONOGON\TranslationTools\Configuration\ExtConf;

/**
 * FileUtility
 */
class FileUtility {


	/**
	 * [getLocallangFiles description]
	 * @param  boolean $cached [description]
	 * @return array          [description]
	 */
	public static function getLocallangFiles ($cached = TRUE){
		global $BE_USER;

		if ($cached){
			$files = $BE_USER->getSessionData('tx_translationtools:files');
		}

		if (!is_array($files)){
			$files = static::_getLocallangFiles();
			$BE_USER->setAndSaveSessionData('tx_translationtools:files', $files);
		}
		return $files;
	}

	protected static function _getLocallangFiles (){
		$extLocations = GeneralUtility::trimExplode(',', ExtConf::get('locallangDirectories'));
		$files = array();

		// Traverse extension locations:
		foreach($extLocations as $path) {
			$path = GeneralUtility::getFileAbsFileName(static::trailingSlash($path));
			if (is_dir($path)) {
				$files = GeneralUtility::getAllFilesAndFoldersInPath(array(), $path, 'xml,xlf', FALSE, 99, 'Tests');
			}
		}

		// Remove prefixes
		$files = GeneralUtility::removePrefixPathFromList($files, PATH_site);

		// Remove all non-locallang files (looking at the prefix)
		foreach($files as $key => $value)   {
			if (strpos(basename($value), 'locallang') !== 0) {
				unset($files[$key]);
			} else {
				$files[$key] = static::makeExtPath($value);
			}
		}

		return $files;
	}

	public static function getExtensionDirectories (){
		$path = GeneralUtility::getFileAbsFileName('typo3conf/ext/');
		$directories = GeneralUtility::get_dirs($path);
		foreach ($directories as $key => $directory) {
			$directories[$key] = 'typo3conf/ext/' . $directory;
		}
		return $directories;
	}

	/**
	 * [determineLanguageFile description]
	 *
	 * 1. If can write to extension
	 * 2. If can write to l10n
	 * 3. Write to extension l10n_overwrite as xlf,xml or ts
	 *
	 * @param  [type] $sourcePath [description]
	 * @param  [type] $language   [description]
	 * @return [type]             [description]
	 */
	public static function determineLanguageFile ($sourcePath, $language){
		$targetPath = static::addLanguageToPath($sourcePath, $language);

		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		// $extConfManager = $objectManager->get('MONOGON\\TranslationTools\\Configuration\\ExtConfManager');
		$extKey = static::extractExtKey($sourcePath);

		// Check write permission for extension directory
		$allowWriteToExtension = ExtConf::get('getAllowWriteToExtension');
		if (in_array($extKey, $allowWriteToExtension)){
			return $targetPath;
		}

		// Check write permission for l10n directory
		$allowWriteToL10nDir = ExtConf::get('getAllowWriteToL10nDir');
		if (in_array($extKey, $allowWriteToL10nDir)){
			return static::makeL10nPath($sourcePath, $language);
		}

		// Write to TypoScript file if configured this way
		// $useTypeScript = ExtConf::get('getUseTypeScript');
		// if ($useTypeScript){
		// 	return 'EXT:l10n_overwrite/Configuration/TypoScript/l10n/setup.txt';
		// }

		$extensionRepository = $objectManager->get('TYPO3\\CMS\\Extensionmanager\\Domain\\Repository\\ExtensionRepository');
		$isInTER = (boolean) $extensionRepository->countByExtensionKey($extKey);

		// Overwrite
		if (file_exists(GeneralUtility::getFileAbsFileName($targetPath))){
			return static::makeOverwritePath($sourcePath, $language);
		}

		// Write to l10n directory if extension is not in TER
		if (!$isInTER){
			return static::makeL10nPath($sourcePath, $language);
		}

		// Overwrite
		return static::makeOverwritePath($sourcePath, $language);
	}

	public static function makeOverwritePath ($path, $language){
		$overwritePath = 'EXT:l10n_overwrite/Resources/Private/l10n/';

		$path = preg_replace('#^(EXT:|typo3conf/ext/)#', $overwritePath, $path);

		$path = static::addLanguageToPath($path, $language);

		return $path;
	}

	/**
	 * [addLanguageToPath description]
	 * @param string $identifier [description]
	 * @param string $language   [description]
	 * @return string
	 */
	public static function addLanguageToPath ($identifier, $language){
		if ($language == 'default'){
			return $identifier;
		}
		return str_replace('/locallang.', "/$language.locallang.", $identifier);
	}

	public static function extractLanguageFromPath ($path){
		if (preg_match('#/([a-z]{2,})\.locallang#i', $path, $matches)){
			return $matches[1];
		}
		return NULL;
	}

	public static function convertToOriginalPath ($path){
		$path = GeneralUtility::getFileAbsFileName($path);
		$l10nDir = GeneralUtility::getFileAbsFileName(ExtConf::get('storageFolder'));
		$path = str_replace($l10nDir, '', $path);

		$path = preg_replace('#/([a-z]{2,}\.)(locallang)#i', '/$2', $path);

		return 'EXT:' . $path;
	}

	/**
	 * Returns a TYPO3 extension path starting with EXT:
	 *
	 * @param  [type] $path [description]
	 * @return [type]       [description]
	 */
	public static function makeExtPath ($path){
		return 'EXT:' . preg_replace('#^.+?/ext/#', '', $path);
	}

	/**
	 * [makeL10nPath description]
	 * @param  string $path [description]
	 * @param  string $language   [description]
	 * @return string             [description]
	 * @throws InvalidArgumentException
	 */
	public static function makeL10nPath ($path, $language){
		if (strpos($path, 'typo3conf/ext/') === 0){
			return static::addLanguageToPath(str_replace('typo3conf/ext/', "typo3conf/l10n/$language/", $path), $language);
		}
		if (strpos($path, 'EXT:') === 0){
			return static::addLanguageToPath(str_replace('EXT:', "typo3conf/l10n/$language/", $path), $language);
		}
		throw new \InvalidArgumentException("Could not make l10n path from $path!", 1421611864);
	}

	/**
	 * [extractExtKey description]
	 *
	 * @param  string $path [description]
	 * @return string|NULL             [description]
	 */
	public static function extractExtKey ($path){
		$path = static::getRelativePath($path);
		if (preg_match('#^typo3conf[/\\\\]{1}ext[/\\\\]{1}([^/\\\\]+)[/\\\\]{1}#i', $path, $matches)){
			return $matches[1];
		}
		if (preg_match('#^EXT:([^/\\\\]+)[/\\\\$]{1}#i', $path, $matches)){
			return $matches[1];
		}
		return NULL;
	}

	/**
	 * [mekeBackupPath description]
	 * @param  string $path [description]
	 * @return string             [description]
	 * @throws InvalidArgumentException
	 */
	public static function makeBackupPath ($path){
		if (strpos($path, 'typo3conf/ext/') === 0){
			return str_replace('typo3conf/ext/', 'uploads/tx_translationtools/', $path);
		}
		throw new \InvalidArgumentException("Could not make backup path from $path!", 1421997699);
	}

	/**
	 * Creates a directory deep
	 * @param  string $directory [description]
	 */
	public static function createDirectory ($directory){
		$absoluteDirectory = GeneralUtility::getFileAbsFileName($directory);

		if (!GeneralUtility::isAllowedAbsPath($absoluteDirectory)){
			throw new \InvalidArgumentException("Create directory '$directory' is not allowed!", 1422004899);
		}

		GeneralUtility::mkdir_deep(dirname($absoluteDirectory) . '/', basename($absoluteDirectory));
	}

	/**
	 * Returns relative path to PATH_site.
	 *
	 * @param  string $path [description]
	 * @return string       [description]
	 */
	public static function getRelativePath ($path){
		return str_replace(PATH_site, '', $path);
	}

	/**
	 * Adds a trailing slash to path.
	 *
	 * @param  string $path [description]
	 * @return string       [description]
	 */
	public static function trailingSlash ($path){
		return rtrim($path, '/') . '/';
	}




}