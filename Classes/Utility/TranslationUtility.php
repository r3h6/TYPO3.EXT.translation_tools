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
use MONOGON\TranslationTools\Configuration\ExtConf;

/**
 * TranslationUtility
 */
class TranslationUtility {

	const REGEX_ATTRIBUTE_KEY = '#(id|key)="([^"]*)"#i';
	const REGEX_ATTRIBUTE_DEFAULT = '#(default="([^"]*)")|(>([^<]*)<)#i';
	const REGEX_SHORTSYNTAX_ATTRIBUTE_KEY = '#(id|key)\s*:\s*\'([^\']+)\'#i';
	const REGEX_SHORTSYNTAX_ATTRIBUTE_DEFAULT = '#default\s*:\s*\'([^\']+)\'#i';

	public static function getIdFromTranslationKey ($key){
		if (preg_match('/LLL:.+\.(xml|xlf):(.+)/i', $key, $matches)){
			return $matches[2];
		}
		return $key;
	}

	public static function getLocallangFromTranslationKey ($key, $default = NULL){
		if (preg_match('/LLL:(FILE:)?(.+\.(xml|xlf)):/i', $key, $matches)){
			return $matches[2];
		}
		return $default;
	}

	public static function getLocallangFiles (array $translations){
		$locallangFiles = array();
		foreach ($translations as $translation){
			if (!in_array($translation->getFile(), $locallangFiles)){
				$locallangFiles[] = $translation->getFile();
			}
		}
		return $locallangFiles;
	}

	public static function extractFromFile ($path){
		$path = GeneralUtility::getFileAbsFileName($path);
		$content = @file_get_contents($path);
		$translations = array();
		$extKey = FileUtility::extractExtKey($path);
		$locallang = ($extKey) ? "EXT:$extKey/Resources/Private/Language/locallang.xlf": NULL;

		/** @todo */
		if (pathinfo($path, PATHINFO_EXTENSION) === 'php'){
			// preg_match_all('#translate\([^\)]+\)#')
			return $translations;
		}

		// Tag
		preg_match_all('#<f:translate.+?(/>|</f:translate>)#i', $content, $matches);
		if (isset($matches[0])){
			foreach ($matches[0] as $match) {
				if (preg_match(self::REGEX_ATTRIBUTE_KEY, $match, $id)){
					$translation = GeneralUtility::makeInstance('MONOGON\\TranslationTools\\Domain\\Model\\Translation');
					$translation
						->setId(self::getIdFromTranslationKey(end($id)))
						->setFile(self::getLocallangFromTranslationKey(end($id), $locallang));
					if (preg_match(self::REGEX_ATTRIBUTE_DEFAULT, $match, $default)){
						$translation->setSource(end($default));
					}
					$translations[] = $translation;
				}
			}
		}

		// Inline
		preg_match_all(\TYPO3\CMS\Fluid\Core\Parser\TemplateParser::$SPLIT_PATTERN_SHORTHANDSYNTAX_VIEWHELPER, $content, $matches);
		if (isset($matches[0])){
			foreach ($matches[0] as $match){
				if (preg_match(self::REGEX_SHORTSYNTAX_ATTRIBUTE_KEY, $match, $id)){
					$translation = GeneralUtility::makeInstance('MONOGON\\TranslationTools\\Domain\\Model\\Translation');
					$translation
						->setId(self::getIdFromTranslationKey(end($id)))
						->setFile(self::getLocallangFromTranslationKey(end($id), $locallang));
					if (preg_match(self::REGEX_SHORTSYNTAX_ATTRIBUTE_DEFAULT, $match, $default)){
						$translation->setSource(end($default));
					}
					$translations[] = $translation;
				}
			}
		}

		return $translations;
	}
}