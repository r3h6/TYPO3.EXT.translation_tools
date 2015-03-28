<?php
namespace MONOGON\TranslationTools\Domain\Repository;

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
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use MONOGON\TranslationTools\Configuration\ExtConf;
/**
 * The repository for SystemLanguages
 */
class SystemLanguageRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {


	public function findAllAccessableSystemLanguages() {
		$systemLanguages = $this->getDefaultSystemLanguages();
		$systemLanguages = array_merge($systemLanguages, $this->findAll()->toArray());

		foreach ($systemLanguages as $key => $systemLanguage){
			if (!$GLOBALS['BE_USER']->checkLanguageAccess($systemLanguage->getUid())){
				unset($systemLanguages[$key]);
			}
		}
		return $systemLanguages;
	}

	public function getDefaultSystemLanguages() {
		$systemLanguages = array();
		$defaultLanguages = GeneralUtility::trimExplode(';', ExtConf::get('defaultLanguage'));
		foreach ($defaultLanguages as $defaultLanguage){
			$properties = GeneralUtility::trimExplode(':', $defaultLanguage);

			$systemLanguage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('MONOGON\\TranslationTools\\Domain\\Model\\SystemLanguage');
			$systemLanguage->setFlag($properties[0]);
			$systemLanguage->setTitle($properties[1]);

			$systemLanguages[] = $systemLanguage;
		}
		return $systemLanguages;
	}

}