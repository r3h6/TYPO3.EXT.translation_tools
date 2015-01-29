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

	public function makeInstance ($identifier){
		$extension = pathinfo($identifier, PATHINFO_EXTENSION);
		switch ($extension) {
			case 'xlf': $class = 'MONOGON\\TranslationTools\\Domain\\Model\\FileXliff'; break;
			case 'xml': $class = 'MONOGON\\TranslationTools\\Domain\\Model\\FileXml'; break;
			// case 'txt': $class = 'MONOGON\\TranslationTools\\Domain\\Model\\File\\TypoScript'; break;
			default: throw new \InvalidArgumentException ("Could not find a class for $identifier", 1421615253);
		}
		return $this->objectManager->get($class, $identifier);
	}

	public function findAllRaw (){
		$files = FileUtility::getLocallangFiles();
		return $files;
	}

	public function findByIdentifier ($identifier){
		return $this->makeInstance($identifier);
	}

	public function save ($file){
		$file->save();
	}

	public function backup ($file){
		if ($file->exists()){
			$backupPath = FileUtility::makeBackupPath($file->getIdentifier());
			FileUtility::createDirectory(dirname($backupPath));
			$file->copy($backupPath);
		}
	}
}