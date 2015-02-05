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

	// private static $localizationFactory;

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
			} else {
				$files[$key] = self::makeExtPath($value);
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
	 * @param  [type] $sourcePath [description]
	 * @param  [type] $language   [description]
	 * @return [type]             [description]
	 */
	public static function determineLanguageFile ($sourcePath, $language){
		$targetPath = self::addLanguageToPath($sourcePath, $language);

		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		// $extConfManager = $objectManager->get('MONOGON\\TranslationTools\\Configuration\\ExtConfManager');
		$extKey = self::extractExtKey($sourcePath);

		// Check write permission for extension directory
		$allowWriteToExtension = ExtConf::get('getAllowWriteToExtension');
		if (in_array($extKey, $allowWriteToExtension)){
			return $targetPath;
		}

		// Check write permission for l10n directory
		$allowWriteToL10nDir = ExtConf::get('getAllowWriteToL10nDir');
		if (in_array($extKey, $allowWriteToL10nDir)){
			return self::makeL10nPath($sourcePath, $language);
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
			return self::makeOverwritePath($sourcePath, $language);
		}

		// Write to l10n directory if extension is not in TER
		if (!$isInTER){
			return self::makeL10nPath($sourcePath, $language);
		}

		// Overwrite
		return self::makeOverwritePath($sourcePath, $language);
	}

	public function makeOverwritePath ($path, $language){
		$overwritePath = 'EXT:l10n_overwrite/Resources/Private/l10n/';

		$path = preg_replace('#^(EXT:|typo3conf/ext/)#', $overwritePath, $path);

		$path = self::addLanguageToPath($path, $language);

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

	public static function makeExtPath ($path){
		return 'EXT:' . preg_replace('#^.+?/ext/#', '', $path);
	}

	/**
	 * [makeL10nPath description]
	 * @param  string $identifier [description]
	 * @param  string $language   [description]
	 * @return string             [description]
	 * @throws InvalidArgumentException
	 */
	public static function makeL10nPath ($identifier, $language){
		if (strpos($identifier, 'typo3conf/ext/') === 0){
			return self::addLanguageToPath(str_replace('typo3conf/ext/', "typo3conf/l10n/$language/", $identifier), $language);
		}
		if (strpos($identifier, 'EXT:') === 0){
			return self::addLanguageToPath(str_replace('EXT:', "typo3conf/l10n/$language/", $identifier), $language);
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
		if (preg_match('#^EXT:([^/\\\\]+)[/\\\\]{1,}#i', $identifier, $matches)){
			return $matches[1];
		}
		return NULL;
	}

	/**
	 * [mekeBackupPath description]
	 * @param  string $identifier [description]
	 * @return string             [description]
	 * @throws InvalidArgumentException
	 */
	public static function makeBackupPath ($identifier){
		if (strpos($identifier, 'typo3conf/ext/') === 0){
			return str_replace('typo3conf/ext/', 'uploads/tx_translationtools/', $identifier);
		}
		throw new \InvalidArgumentException("Could not make backup path from $identifier!", 1421997699);
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
	 * [getRelativePath description]
	 * @param  string $path [description]
	 * @return string       [description]
	 */
	public static function getRelativePath ($path){
		return str_replace(PATH_site, '', $path);
	}

	public static function trailingSlash ($path){
		return rtrim($path, '/') . '/';
	}


	public static function findTranslations ($path){
		$path = GeneralUtility::getFileAbsFileName($path);
		$content = @file_get_contents($path);

		$translations = array();


		// if (pathinfo($path, PATHINFO_EXTENSION) === 'php'){
		// 	preg_match_all('#translate\([^\)]+\)#')
		// }

		// Tag
		preg_match_all('#<f:translate.+?(/>|</f:translate>)#i', $content, $matches);
		if (isset($matches[0])){
			foreach ($matches[0] as $match) {
				$properties = array();
				if (preg_match(self::REGEX_ATTRIBUTE_KEY, $match, $id)){
					$properties['id'] = end($id);
					if (preg_match(self::REGEX_ATTRIBUTE_DEFAULT, $match, $default)){
						$properties['default'] = end($default);
					}
				}
				if (!empty($properties)){
					$translations[] = $properties;
				}
			}
		}

		// Inline
		preg_match_all(\TYPO3\CMS\Fluid\Core\Parser\TemplateParser::$SPLIT_PATTERN_SHORTHANDSYNTAX_VIEWHELPER, $content, $matches);
		if (isset($matches[0])){
			foreach ($matches[0] as $match){
				$properties = array();
				if (preg_match(self::REGEX_SHORTSYNTAX_ATTRIBUTE_KEY, $match, $id)){
					$properties['id'] = end($id);
					if (preg_match(self::REGEX_SHORTSYNTAX_ATTRIBUTE_DEFAULT, $match, $default)){
						$properties['default'] = end($default);
					}
				}
				if (!empty($properties)){
					$translations[] = $properties;
				}
			}
		}


		return $translations;
		// $objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

		// $dataMapper = $objectManager->get('TYPO3\\CMS\\Extbase\\Property\\PropertyMapper');
		// return $dataMapper->map('MONOGON\\TranslationTools\\Domain\\Model\\Translation', $translations);
		// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($translations);
		// exit;
	}

	const REGEX_ATTRIBUTE_KEY = '#(id|key)="([^"]*)"#i';
	const REGEX_ATTRIBUTE_DEFAULT = '#(default="([^"]*)")|(>([^<]*)<)#i';
	const REGEX_SHORTSYNTAX_ATTRIBUTE_KEY = '#(id|key)\s*:\s*\'([^\']+)\'#i';
	const REGEX_SHORTSYNTAX_ATTRIBUTE_DEFAULT = '#default\s*:\s*\'([^\']+)\'#i';
}