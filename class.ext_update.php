<?php

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

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * TranslationController
 */
class ext_update {
	protected $pageEffectNone = array();
	//protected $ll = 'LLL:EXT:boilerplate/locallang.xml:updater.';

	const EXT_KEY = 'translation_tools';

	/**
	 * Array of flash messages (params) array[][status,title,message]
	 *
	 * @var array
	 */
	protected $messageArray = array();

	protected $fileHandlingUtility;
	protected $installUtility;
	protected $objectManager;

	public function __construct (){
		$this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

		$this->fileHandlingUtility = $this->objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\FileHandlingUtility');
		$this->installUtility = $this->objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\InstallUtility');
	}

	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return	string		HTML
	 */
	public function main() {
		//$this->installDepencies();
		return $this->generateOutput();
	}

	protected function installDepencies (){
		$title = 'Install depencies';
		try {

			$extKey = 'l10n_overwrite';

			if ($this->installUtility->isLoaded($extKey)){
				$this->messageArray[] = array(FlashMessage::NOTICE, $title, "Extension $extKey already installed.");
			} else {

				$extPath = ExtensionManagementUtility::extPath(self::EXT_KEY);

				$file = $extPath . 'Resources/Private/Install/' . $extKey . '.zip';
				$this->fileHandlingUtility->unzipExtensionFromFile($file, $extKey);

				// if (!$this->installUtility->isAvailable($extKey)){
				// 	throw new Exception("Extension $extKey is not available", 1421441255);
				// }

				$this->installUtility->install($extKey);

				if (!$this->installUtility->isLoaded($extKey)){
					throw new Exception("Extension $extKey is not loaded", 1421441294);
				}

				$this->messageArray[] = array(FlashMessage::OK, $title, "Extension $extKey successfully installed.");
			}
		} catch (\Exception $exception){
			$this->messageArray[] = array(FlashMessage::ERROR, $title, sprintf("Could not install $extKey because %s!", $exception->getMessage()));
		}
	}

	/**
	 * Checks how many rows are found and returns true if there are any
	 * (this function is called from the extension manager)
	 *
	 * @param	string		$what: what should be updated
	 * @return	boolean
	 */
	public function access($what = 'all')
	{
		return TRUE;
	}

	/**
	 * Generates output by using flash messages
	 *
	 * @return string
	 */
	protected function generateOutput() {
		$output = '';
		foreach ($this->messageArray as $messageItem) {
			/** @var \TYPO3\CMS\Core\Messaging\FlashMessage $flashMessage */
			$flashMessage = GeneralUtility::makeInstance(
				'TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
				$messageItem[2],
				$messageItem[1],
				$messageItem[0]);
			$output .= $flashMessage->render();
		}
		return $output;
	}
}

?>