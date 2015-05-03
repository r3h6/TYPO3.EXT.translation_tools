<?php
namespace MONOGON\TranslationTools\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use MONOGON\TranslationTools\Configuration\PhpIni;
use MONOGON\TranslationTools\Exception\ExecutionTimeException;
use MONOGON\TranslationTools\Utility\FileUtility;
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
	 * Localization factory
	 * @var \MONOGON\TranslationTools\Localization\LocalizationFactory
	 * @inject
	 */
	protected $localizationFactory = NULL;

	/**
	 * [$signalSlotDispatcher description]
	 * @var TYPO3\CMS\Extbase\SignalSlot\Dispatcher
	 * @inject
	 */
	protected $signalSlotDispatcher = NULL;

	/**
	 * @param \MONOGON\TranslationTools\Domain\Model\Dto\Demand $demand
	 * @return \MONOGON\TranslationTools\Persistence\TranslationResult [description]
	 */
	public function findDemanded(\MONOGON\TranslationTools\Domain\Model\Dto\Demand $demand = NULL) {
		// $translations = array();
		$translations = GeneralUtility::makeInstance('MONOGON\\TranslationTools\\Persistence\\TranslationResult');

		if (!$demand) {
			return $translations;
		}
		if (!$demand->getFiles() && !$demand->getId() && !$demand->getLabel()){
			return $translations;
		}
		// Load files¦
		$files = $demand->getFiles();
		if (empty($files)){
			$files = $this->fileRepository->findAllRaw();
		}

		$sourceLanguage = 'default';
		$languages = $demand->getLanguages();
		$charset = 'utf8';
		$microTimeLimit = $GLOBALS['TYPO3_MISC']['microtime_start'] + 0.75 * PhpIni::getMaxExecutionTime();

		foreach ($files as $file) {
			if (microtime(TRUE) > $microTimeLimit) {
				throw new ExecutionTimeException('Running out of time...', 1420919679);
			}
			foreach ($languages as $language) {
				$parsedData = $this->localizationFactory->getParsedData($file, $language, $charset, LocalizationFactory::ERROR_MODE_EXCEPTION);
				// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($parsedData);
				foreach ($parsedData[$sourceLanguage] as $id => $value) {
					$defaultSource = $parsedData[$sourceLanguage][$id][0]['source'];

					$target = isset($parsedData[$language][$id][0]['target']) ? $parsedData[$language][$id][0]['target'] : NULL;

					$source = isset($parsedData[$language][$id][0]['source']) ? $parsedData[$language][$id][0]['source'] : NULL;


					// Search, filter
					if ($demand->getLabel() && stripos("{$source},{$target}", $demand->getLabel()) === FALSE) {
						continue;
					}

					if ($demand->getId() && stripos($id, $demand->getId()) === FALSE) {
						continue;
					}

					if ($demand->getFilter() === Demand::FILTER_MISSING && $target){
						continue;
					}

					if ($demand->getFilter() === Demand::FILTER_TRANSLATED && !$target){
						continue;
					}

					if ($demand->getFilter() === Demand::FILTER_CHANGED && $source !== NULL && $source === $defaultSource){
						continue;
					}

					$translation = GeneralUtility::makeInstance('MONOGON\\TranslationTools\\Domain\\Model\\Translation');
					$translation->setId($id)
						->setSource($source)
						->setTarget($target)
						->setSourceFile($file)
						->setSourceLanguage($sourceLanguage)
						->setTargetLanguage($language);

					$translations->addTranslation($translation);
				}
			}
		}

		return $translations;
	}

	/**
	 * @param $translation
	 */
	public function update(\MONOGON\TranslationTools\Domain\Model\Translation $translation) {
		$file = $this->fileRepository->findByIdentifier($translation->getTargetFile());
		$file->addTranslation($translation);
		$this->fileRepository->save($file);

		$this->emitAfterUpdateSignal($translation);
	}

	protected function emitAfterUpdateSignal (\MONOGON\TranslationTools\Domain\Model\Translation $translation){
		$this->signalSlotDispatcher->dispatch(__CLASS__, 'afterUpdate', array($translation));
	}

}