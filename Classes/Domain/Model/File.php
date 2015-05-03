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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use MONOGON\TranslationTools\Configuration\ExtConf;
use MONOGON\TranslationTools\Localization\LocalizationFactory;
use MONOGON\TranslationTools\Utility\FileUtility;

/**
 * File
 */
class File {

	/**
	 * Translations
	 * @var array
	 */
	protected $translations = array();

	/**
	 * [$localizationFactory description]
	 * @var \MONOGON\TranslationTools\Localization\LocalizationFactory
	 * @inject
	 */
	protected $localizationFactory = NULL;

	/**
	 * [$path description]
	 * @var string
	 */
	protected $path = '';

	/**
	 * [$view description]
	 * @var TYPO3\CMS\Fluid\View\StandaloneView
	 * @inject
	 */
	protected $view;

	protected $charset = 'utf8';
	protected $targetLanguage;
	protected $sourceLanguage = 'en';
	protected $extensionKey;

	public function __construct ($path = NULL){
		$this->path = $path;
		$this->targetLanguage = FileUtility::extractLanguageFromPath($path);
		$this->extensionKey = FileUtility::extractExtKey($path);
	}

	public function initializeObject (){
		$this->parse();
	}

	/**
	 * Returns the path
	 *
	 * @return [type] $path
	 */
	public function addTranslation (\MONOGON\TranslationTools\Domain\Model\Translation $translation){
		$this->translations[$translation->getId()] = $translation;
		return $this;
	}

	public function getTranslations (){
		return $this->translations;
	}

	public function getPath(){
		return $this->path;
	}

	public function parse (){
		$sourceLanguage = 'default';

		$parsedData = $this->localizationFactory->getParsedData($this->path, $this->targetLanguage, $this->charset, LocalizationFactory::ERROR_MODE_EXCEPTION);

		foreach ($parsedData[$sourceLanguage] as $id => $value) {
			$target = isset($parsedData[$this->targetLanguage][$id][0]['target']) ? $parsedData[$this->targetLanguage][$id][0]['target'] : NULL;
			$source = $parsedData[$sourceLanguage][$id][0]['source'];

			$translation = GeneralUtility::makeInstance('MONOGON\\TranslationTools\\Domain\\Model\\Translation');
			$translation->setId($id)
				->setSource($source)
				->setTarget($target)
				->setSourceLanguage($this->sourceLanguage)
				->setTargetLanguage($this->targetLanguage);

			$this->translations[$id] = $translation;
		}
	}

	public function __toString (){
		ksort($this->translations);

		$format = pathinfo($this->path, PATHINFO_EXTENSION);

		$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath(ExtConf::EXT_KEY);
		$templateRootPath = $extPath . 'Resources/Private/Backend/Templates/File/Render.' . $format;

		$this->view->setTemplatePathAndFilename($templateRootPath);
		$this->view->setFormat($format);

		$this->view->assign('extensionKey', $this->extensionKey);
		$this->view->assign('sourceLanguage', $this->sourceLanguage);
		$this->view->assign('targetLanguage', $this->targetLanguage);
		$this->view->assign('translations', $this->translations);

		return $this->view->render();
	}

	public function save (){
		$this->saveAs($this->path, TRUE);
		return $this;
	}

	public function saveAs ($path, $overwrite = FALSE){
		$path = GeneralUtility::getFileAbsFileName($path);

		if (!GeneralUtility::isAllowedAbsPath($path)){
			throw new \InvalidArgumentException("Could not save file because path '$path' is not allowed!", 1422004887);
		}

		if (!$overwrite && file_exists($path)){
			throw new \Exception("Not allowed to overwrite file '$path'!", 1421790718);
		}

		FileUtility::createDirectory(dirname($path));

		if (!GeneralUtility::writeFile($path, $this->__toString())){
			throw new \Exception("Could not save file '$path'!", 1422005075);
		}

		$this->path = FileUtility::getRelativePath($path);
		return $this;
	}

	public function getAbsolutePath (){
		return GeneralUtility::getFileAbsFileName($this->path);
	}

	public function exists (){
		return file_exists($this->getAbsolutePath());
	}

	public function copy ($destination){
		$destination = GeneralUtility::getFileAbsFileName($destination);
		$source = $this->getAbsolutePath();
		if (!GeneralUtility::isAllowedAbsPath($source)){
			throw new \InvalidArgumentException("Could not copy file because source '$source' is not allowd!");
		}
		if (!GeneralUtility::isAllowedAbsPath($destination)){
			throw new \InvalidArgumentException("Could not copy file because destination '$destination' is not allowd!");
		}
		return copy($source, $destination);
	}
}