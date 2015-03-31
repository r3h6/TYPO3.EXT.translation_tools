<?php
namespace MONOGON\TranslationTools\Domain\Repository;

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
use MONOGON\TranslationTools\Utility\FileUtility;
use MONOGON\TranslationTools\Utility\TranslationUtility;


/**
 * FileRepository
 */
class FileRepository {

	/**
	 * [$objectManager description]
	 *
	 * @var TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	protected function makeInstance ($identifier){
		$extension = pathinfo($identifier, PATHINFO_EXTENSION);
		// switch ($extension) {
		// 	case 'xlf': $className = 'MONOGON\\TranslationTools\\Domain\\Model\\FileXliff'; break;
		// 	case 'xml': $className = 'MONOGON\\TranslationTools\\Domain\\Model\\FileXml'; break;
		// 	// case 'txt': $className = 'MONOGON\\TranslationTools\\Domain\\Model\\File\\TypoScript'; break;
		// 	default: throw new \InvalidArgumentException ("Could not find a class for $identifier", 1421615253);
		// }
		$className = 'MONOGON\\TranslationTools\\Domain\\Model\\File';
		$file = $this->objectManager->get($className, $identifier);
		$file->setFormat($extension);
		$file->parse();
		return $file;
	}

	public function findAllRaw (){
		$files = FileUtility::getLocallangFiles();
		return $files;
	}

	public function findByIdentifier ($identifier){
		return $this->makeInstance($identifier);
	}

	public function save (\MONOGON\TranslationTools\Domain\Model\File $file){
		$file->save();
	}

	public function backup (\MONOGON\TranslationTools\Domain\Model\File $file){
		if ($file->exists()){
			$backupPath = FileUtility::makeBackupPath($file->getIdentifier());
			FileUtility::createDirectory(dirname($backupPath));
			$file->copy($backupPath);
		}
	}

	public function findSourceCodeFiles ($path){
		$path = GeneralUtility::getFileAbsFileName(
			FileUtility::trailingSlash($path)
		);
		return GeneralUtility::getAllFilesAndFoldersInPath(array(), $path, 'xhtml,html,xml,json,txt,md,vcf,vcard,php', FALSE, 99, 'Tests|Locallang|Configuration');
	}

	// public function analyseTranslations ($path){
	// 	$path = GeneralUtility::getFileAbsFileName($path);
	// 	$files = GeneralUtility::getAllFilesAndFoldersInPath(array(), $path, 'xhtml,html,xml,json,txt,md,vcf,vcard,php', FALSE, 99, 'Tests|Locallang|Configuration');

	// 	$requiredTranslations = array();
	// 	foreach ($files as $file){
	// 		$requiredTranslations = array_merge($requiredTranslations, TranslationUtility::extractFromFile($file));
	// 	}

	// 	$locallangFiles = array();
	// 	foreach ($requiredTranslations as $translation){
	// 		if (!in_array($translation->getFile(), $locallangFiles)){
	// 			$locallangFiles[] = $translation->getFile();
	// 		}
	// 	}

	// 	$availableTranslations = array();
	// 	foreach ($locallangFiles as $identifier) {
	// 		$file = $this->makeInstance($identifier);
	// 		$file->parse();
	// 		foreach ($file->getTranslations() as $translation){
	// 			$availableTranslations[] = $translation;
	// 		}

	// 	}


	// 	return $requiredTranslations;
	// }
}