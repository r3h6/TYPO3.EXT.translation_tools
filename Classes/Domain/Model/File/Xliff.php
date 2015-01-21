<?php
namespace MONOGON\TranslationTools\Domain\Model\File;

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

use \TYPO3\CMS\Core\Utility\GeneralUtility;;

/**
 * Xliff
 */
class Xliff extends \MONOGON\TranslationTools\Domain\Model\File {

	protected $format = 'xlf';

	// protected function parse (){
	// 	$parser = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Localization\\Parser\\XliffParser');
	// 	$data = $parser->getParsedData($this->identifier, 'de');
	// 	\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($data);
	// }

	// protected function parse (){
	// 	$sourceLanguage = 'default';
	// 	//$translationRepository = GeneralUtility::makeInstance('MONOGON\\TranslationTools\\Domain\\Repository\\TranslationRepository');
	// 	$localizationFactory = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Localization\\LocalizationFactory');
	// 	$parsedData = $localizationFactory->getParsedData($this->identifier, $this->targetLanguage, $this->charset, 2);
	// 	foreach ($parsedData[$sourceLanguage] as $id => $value) {
	// 		$target = isset($parsedData[$this->targetLanguage][$id][0]['target']) ? $parsedData[$this->targetLanguage][$id][0]['target'] : NULL;
	// 		$source = $parsedData[$sourceLanguage][$id][0]['source'];
	// 		$this->translations[] = $this->translationRepository->createTranslation(array(
	// 			'source' => $source,
	// 			'target' => $target,
	// 			'id' => $id,
	// 			'file' => $this->identifier,
	// 			'sourceLanguage' => $this->sourceLanguage,
	// 			'targetLanguage' => $this->targetLanguage
	// 		));
	// 	}
	// 	\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->translations);
	// }
}