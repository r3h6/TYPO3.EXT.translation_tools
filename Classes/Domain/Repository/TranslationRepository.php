<?php
namespace MONOGON\TranslationTools\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use MONOGON\TranslationTools\Configuration\PhpIni;
use MONOGON\TranslationTools\Exception\ExecutionTimeException;
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

/**
 * TranslationRepository
 */
class TranslationRepository {

	const ERROR_MODE_EXCEPTION = 2;

	protected $model = 'MONOGON\\TranslationTools\\Domain\\Model\\Translation';

	/**
	 * [$fileRepository description]
	 *
	 * @var \MONOGON\TranslationTools\Domain\Repository\FileRepository
	 * @inject
	 */
	protected $fileRepository = NULL;

	/**
	 * [$propertyMapper description]
	 *
	 * @var TYPO3\CMS\Extbase\Property\PropertyMapper
	 * @inject
	 */
	protected $propertyMapper = NULL;

	/**
	 * @param \MONOGON\TranslationTools\Domain\Model\Dto\Demand $demand
	 */
	public function findDemanded(\MONOGON\TranslationTools\Domain\Model\Dto\Demand $demand) {
		$translations = array();
		// Load files¦
		if ($file = $demand->getFile()) {
			// $files = array($this->fileRepository->findByIdentifier($file));
			$files = array($file);
		} else {
			$files = $this->fileRepository->findAllRaw();
		}
		$localizationFactory = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Localization\\LocalizationFactory');
		$sourceLanguage = 'default';
		$languages = $demand->getLanguages();
		$charset = 'utf8';
		$microTimeLimit = $GLOBALS['TYPO3_MISC']['microtime_start'] + 0.75 * PhpIni::getMaxExecutionTime();
		foreach ($files as $file) {
			if (microtime(TRUE) > $microTimeLimit) {
				throw new ExecutionTimeException('Error Processing Request', 1420919679);
			}
			foreach ($languages as $language) {
				$parsedData = $localizationFactory->getParsedData($file, $language, $charset, self::ERROR_MODE_EXCEPTION);
				foreach ($parsedData[$sourceLanguage] as $id => $value) {
					$target = isset($parsedData[$language][$id][0]['target']) ? $parsedData[$language][$id][0]['target'] : NULL;
					$source = $parsedData[$sourceLanguage][$id][0]['source'];
					// Search, filter
					if ($demand->getLabel() && stripos("{$source},{$target}", $demand->getLabel()) === FALSE) {
						continue;
					}
					if ($demand->getId() && stripos($id, $demand->getId()) === FALSE) {
						continue;
					}
					$key = sha1($file . $id);
					if (!isset($translations[$key])) {
						$translations[$key] = array(
							'_id' => $id,
							'_file' => $file,
							'_source' => $source
						);
					}
					$translations[$key][$language] = $this->createTranslation(array(
							'source' => $source,
							'target' => $target,
							'id' => $id,
							'file' => $file,
							'sourceLanguage' => $sourceLanguage,
							'targetLanguage' => $language
						));
				}
			}
		}
		return $translations;
	}

	/**
	 * @param $translation
	 */
	public function update($translation) {

		$identifier = FileUtility::determineLanguageFile($translation->getFile(), $translation->getTargetLanguage());

		$file = $this->fileRepository->findByIdentifier($identifier);
		$file->setTranslation($translation);
		$this->fileRepository->update($file);
	}

	/**
	 * @param $properties
	 */
	public function createTranslation($properties) {
		return $this->propertyMapper->convert($properties, $this->model);
	}

}