<?php
namespace MONOGON\TranslationTools\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use MONOGON\TranslationTools\Configuration\PhpIni;
use MONOGON\TranslationTools\Exception\ExecutionTimeException;
use MONOGON\TranslationTools\Utility\FileUtility;
use MONOGON\TranslationTools\Utility\LocalconfUtility;
use MONOGON\TranslationTools\Utility\TranslationUtility;
use MONOGON\TranslationTools\Localization\LocalizationFactory;
use MONOGON\TranslationTools\Domain\Model\Dto\Demand;
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

	// const ERROR_MODE_EXCEPTION = 2;
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
	 * [$localizationFactory description]
	 * TYPO3\CMS\Core\Localization\LocalizationFactory
	 * \MONOGON\TranslationTools\Localization\LocalizationFactory
	 *
	 * @var \MONOGON\TranslationTools\Localization\LocalizationFactory
	 * @inject
	 */
	protected $localizationFactory = NULL;

	/**
	 * @param \MONOGON\TranslationTools\Domain\Model\Dto\Demand $demand
	 */
	public function findDemanded(\MONOGON\TranslationTools\Domain\Model\Dto\Demand $demand = NULL) {
		$translations = array();
		if (!$demand) {
			return $translations;
		}
		// Load files¦
		if ($file = $demand->getFile()) {
			// $files = array($this->fileRepository->findByIdentifier($file));
			$files = array($file);
		} else {
			$files = $this->fileRepository->findAllRaw();
		}
		//$localizationFactory = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Localization\\LocalizationFactory');
		$sourceLanguage = 'default';
		$languages = $demand->getLanguages();
		$charset = 'utf8';
		$microTimeLimit = $GLOBALS['TYPO3_MISC']['microtime_start'] + 0.75 * PhpIni::getMaxExecutionTime();

		$filtered = array();

		foreach ($files as $file) {
			if (microtime(TRUE) > $microTimeLimit) {
				throw new ExecutionTimeException('Error Processing Request', 1420919679);
			}
			foreach ($languages as $language) {
				$parsedData = $this->localizationFactory->getParsedData($file, $language, $charset, LocalizationFactory::ERROR_MODE_EXCEPTION);
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

					if ($demand->getFilter() == Demand::FILTER_MISSING && !$target){
						$filtered[$key] = TRUE;
					}
					if ($demand->getFilter() == Demand::FILTER_TRANSLATED && $target){
						$filtered[$key] = TRUE;
					}
				}
			}
		}

		if (!empty($filtered)){
			$translations = array_intersect_key($translations, $filtered);
		}

		return $translations;
	}

	/**
	 * @param $translation
	 */
	public function update($translation) {
		$identifier = FileUtility::determineLanguageFile($translation->getFile(), $translation->getTargetLanguage());
		$file = $this->fileRepository->makeInstance($identifier);
		$file->parse();
		$file->addTranslation($translation);
		$this->fileRepository->save($file);
		LocalconfUtility::update();
	}

	/**
	 * [findInSourceCode description]
	 *
	 * @param string $path [description]
	 * @return array       [description]
	 */
	public function findInSourceCode($path) {
		$path = GeneralUtility::getFileAbsFileName(
			FileUtility::trailingSlash($path)
		);
		$files = GeneralUtility::getAllFilesAndFoldersInPath(array(), $path, 'xhtml,html,xml,json,txt,md,vcf,vcard,php', FALSE, 99, 'Tests|Locallang|Configuration');
		$translations = array();
		foreach ($files as $file) {
			//$translations = array_merge($translations, TranslationUtility::extractFromFile($file));
			foreach (TranslationUtility::extractFromFile($file) as $translation) {
				$translations[$translation->getHashKey()] = $translation;
			}
		}
		return $translations;
	}

	/**
	 * @param $locallangFiles
	 */
	public function findInLocallangFiles($locallangFiles) {
		$translations = array();
		foreach ($locallangFiles as $path) {
			$file = $this->fileRepository->makeInstance($path);
			$file->parse();
			foreach ($file->getTranslations() as $translation) {
				$translations[$translation->getHashKey()] = $translation;
			}
		}
		return $translations;
	}

	/**
	 * @param $properties
	 */
	public function createTranslation($properties) {
		return $this->propertyMapper->convert($properties, $this->model);
	}

}