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

/**
 * File
 */
class File {//extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	protected $translations = array();

	protected $identifier;

	protected $content;

	protected $format = NULL;

	const EXT_KEY = 'translation_tools';

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
		$templateRootPath = $extPath . 'Resources/Private/Backend/Templates/File/File.' . $this->format;

		$this->view->setTemplatePathAndFilename($templateRootPath);
		$this->view->setFormat($this->format);

		// $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);
		// $templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['templateRootPath']);
		// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($extbaseFrameworkConfiguration); exit;

	}

	public function getIdentifier (){
		return $this->identifier;
	}

	public function getContent (){
		return $this->content;
	}

	public function setTranslation (\MONOGON\TranslationTools\Domain\Model\Translation $translation){
		$this->translations[] = $translation;
	}

	public function removeTranslation (\MONOGON\TranslationTools\Domain\Model\Translation $translation){

	}

	protected function load (){

	}

	public function render (){
		$this->view->assign('sourceLanguage', '');
		$this->view->assign('translations', $this->translations);
		$this->content = $this->view->render();
		return $this;
	}

	public function save (){
		$this->saveAs($this->identifier, TRUE);
	}

	public function saveAs ($path, $overwrite = FALSE){
		if (!$this->content){
			$this->render();
		}

		if (!$overwrite && file_exists($path)){
			throw new \Exception("Could not save file", 1421790718);
		}
		GeneralUtility::writeFile($path, $this->content);
		$this->identifier = $path;
	}


}