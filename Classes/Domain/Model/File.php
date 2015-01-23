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
use MONOGON\TranslationTools\Utility\FileUtility;

/**
 * File
 */
abstract class File {//extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	const EXT_KEY = 'translation_tools';


	protected $translations = array();

	protected $identifier;

	protected $content;

	protected $format = NULL;

	protected $targetLanguage;
	protected $sourceLanguage;

	protected $charset = 'utf8';


	/**
	 * translationRepository
	 *
	 * @var \MONOGON\TranslationTools\Domain\Repository\TranslationRepository
	 * @inject
	 */
	protected $translationRepository = NULL;

	/**
	 * [$view description]
	 * @var TYPO3\CMS\Fluid\View\StandaloneView
	 * @inject
	 */
	protected $view;

	/**
	 * [$configurationManager description]
	 * @var TYPO3\CMS\Extbase\Configuration\ConfigurationManager
	 * @inject
	 */
	protected $configurationManager;

	public function __construct ($identifier = NULL){
		$this->identifier = $identifier;
	}

	/**
	 * Called after inject depencies.
	 * @see http://typo3.org/api/typo3cms/_container_8php_source.html#l00149
	 * @return [type] [description]
	 */
	public function initializeObject (){

		$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath(self::EXT_KEY);
		$templateRootPath = $extPath . 'Resources/Private/Backend/Templates/File/Render.' . $this->format;

		$this->view->setTemplatePathAndFilename($templateRootPath);
		$this->view->setFormat($this->format);

		// $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);
		// $templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['templateRootPath']);
		// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($extbaseFrameworkConfiguration); exit;

	}

	/**
	 * Returns the sourceLanguage
	 *
	 * @return string $sourceLanguage
	 */
	public function getSourceLanguage(){
		return $this->sourceLanguage;
	}

	/**
	 * Sets the sourceLanguage
	 *
	 * @param string $sourceLanguage
	 * @return object $this
	 */
	public function setSourceLanguage($sourceLanguage){
		$this->sourceLanguage = $sourceLanguage;
		return $this;
	}

	/**
	 * Returns the targetLanguage
	 *
	 * @return string $targetLanguage
	 */
	public function getTargetLanguage(){
		return $this->targetLanguage;
	}

	/**
	 * Sets the targetLanguage
	 *
	 * @param string $targetLanguage
	 * @return object $this
	 */
	public function setTargetLanguage($targetLanguage){
		$this->targetLanguage = $targetLanguage;
		return $this;
	}

	/**
	 * Returns the identifier
	 *
	 * @return string $identifier
	 */
	public function getIdentifier(){
		return $this->identifier;
	}

	/**
	 * Sets the identifier
	 *
	 * @param string $identifier
	 * @return object $this
	 */
	public function setIdentifier($identifier){
		$this->identifier = $identifier;
		return $this;
	}

	public function getContent (){
		return $this->content;
	}

	public function addTranslation (\MONOGON\TranslationTools\Domain\Model\Translation $translation){
		$this->translations[] = $translation;
		return $this;
	}

	public function getTranslations (){
		$this->parse();
		return $this->translations;
	}

	public function removeTranslation (\MONOGON\TranslationTools\Domain\Model\Translation $translation){

	}
	// abstract protected function parse ();
	protected function parse (){
		$sourceLanguage = 'default';
		//$translationRepository = GeneralUtility::makeInstance('MONOGON\\TranslationTools\\Domain\\Repository\\TranslationRepository');
		$localizationFactory = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Localization\\LocalizationFactory');
		$parsedData = $localizationFactory->getParsedData($this->identifier, $this->targetLanguage, $this->charset, 2);
		foreach ($parsedData[$sourceLanguage] as $id => $value) {
			$target = isset($parsedData[$this->targetLanguage][$id][0]['target']) ? $parsedData[$this->targetLanguage][$id][0]['target'] : NULL;
			$source = $parsedData[$sourceLanguage][$id][0]['source'];
			$this->translations[] = $this->translationRepository->createTranslation(array(
				'source' => $source,
				'target' => $target,
				'id' => $id,
				'file' => $this->identifier,
				'sourceLanguage' => $this->sourceLanguage,
				'targetLanguage' => $this->targetLanguage
			));
		}
	}

	public function render (){
		$this->view->assign('sourceLanguage', $this->sourceLanguage);
		$this->view->assign('targetLanguage', $this->targetLanguage);
		$this->view->assign('translations', $this->translations);
		$this->content = $this->view->render();
		return $this;
	}

	public function save (){
		$this->saveAs($this->identifier, TRUE);
	}

	public function saveAs ($path, $overwrite = FALSE){
		$path = GeneralUtility::getFileAbsFileName($path);

		if (!GeneralUtility::isAllowedAbsPath($path)){
			throw new \InvalidArgumentException("Could not save file because path '$path' is not allowed!", 1422004887);
		}

		if (!$overwrite && file_exists($path)){
			throw new \Exception("Not allowed to overwrite file '$path'!", 1421790718);
		}

		if (!$this->content){
			$this->render();
		}

		FileUtility::createDirectory(dirname($path));

		if (!GeneralUtility::writeFile($path, $this->content)){
			throw new \Exception("Could not save file '$path'!", 1422005075);
		}

		$this->identifier = FileUtility::getRelativePath($path);
	}

	public function getAbsolutePath (){
		return GeneralUtility::getFileAbsFileName($this->identifier, FALSE);
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