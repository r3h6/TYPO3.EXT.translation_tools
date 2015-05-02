<?php
namespace MONOGON\TranslationTools\Domain\Model;

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

use DOMDocument;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use MONOGON\TranslationTools\Utility\FileUtility;
use MONOGON\TranslationTools\Localization\LocalizationFactory;

/**
 * FileXliff
 */
class FileXliff extends File implements FileLocallangInterface {

	protected static $blankTemplate = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
		<xliff version="1.0">
			<file source-language="en" datatype="plaintext" original="messages" date="2015-01-21T19:05:10Z">
				<header>
					<generator>LFEditor</generator>
				</header>
				<body>
				</body>
			</file>
		</xliff>';

	/**
	 * [$dom description]
	 * @var DOMDocument
	 */
	protected $dom;

	public function __construct ($path = NULL){
		parent::__construct($path);
		if ($this->exists()){
			$this->dom = DOMDocument::load($this->getAbsolutePath());
		} else {
			$this->dom = DOMDocument::loadXML(FileXliff::$blankTemplate);
			$language = FileUtility::extractLanguageFromPath($path);
			if ($language){
				$fileNode = $this->dom->getElementsByTagName('file')->item(0);

				$targetLanguageAttribute = $this->dom->createAttribute('target-language');
				$targetLanguageAttribute->value = $language;
				$fileNode->appendChild($targetLanguageAttribute);

			}
		}
	}

	public function __toString(){
		return $this->dom->saveXML();
	}

	public function addTranslation (\MONOGON\TranslationTools\Domain\Model\Translation $translation){



		$fileNode = $this->dom->getElementsByTagName('file')->item(0);
		$bodyNode = $fileNode->getElementsByTagName('body')->item(0);

		$transUnitNode = $this->dom->getElementById($translation->getId());
// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($transUnitNode);
		if ($transUnitNode){
			$sourceNode = $transUnitNode->getElementsByTagName('source')->item(0);
			$sourceNode->value = $translation->getSource();
			if (!$translation->isSource()){
				$targetNode = $transUnitNode->getElementsByTagName('target')->item(0);
				$targetNode->value = $translation->getTarget();
			}
		} else {
			$transUnitNode = $this->dom->createElement('trans-unit');
			$transUnitNode->setAttribute('id', $translation->getId());

			$sourceNode = $this->dom->createElement('source', $translation->getSource());
			$transUnitNode->appendChild($sourceNode);

			if (!$translation->isSource()){
				$targetNode = $this->dom->createElement('target', $translation->getTarget());
				$transUnitNode->appendChild($targetNode);
			}


			$bodyNode->appendChild($transUnitNode);
		}
	}

	public function getTranslations (){
		//return $this->dom->getElementsByTagName('file')->item
	}
}