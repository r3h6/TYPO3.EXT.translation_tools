<?php
namespace MONOGON\TranslationTools\Service;


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
 *
 */
class SessionService {

	protected $storageKey;

	protected $sessionObject;

	public function __construct ($storageKey = 'tx_translationtools'){
		$this->storageKey = $storageKey;

		$this->sessionObject = (TYPO3_MODE === 'BE') ? $GLOBALS['BE_USER']: $GLOBALS['TSFE'];
	}

	public function get ($key, $persistent = FALSE){
		$sessionData = $this->getSessionData($persistent);
		return (isset($sessionData[$key])) ? $sessionData[$key]: NULL;
	}

	public function set ($key, $data, $persistent = FALSE){
		$sessionData = $this->getSessionData($persistent);
		$sessionData[$key] = $data;
		$this->setSessionData($sessionData, $persistent);
	}

	public function delete ($key, $persistent = FALSE){
		$sessionData = $this->getSessionData($persistent);
		unset($sessionData[$key]);
		$this->setSessionData($sessionData, $persistent);
	}

	public function deleteAll ($persistent){
		$this->setSessionData(array(), $persistent);
	}

	protected function setSessionData (array $sessionData, $persistent){
		if ($persistent && TYPO3_MODE === 'FE'){
			$this->sessionObject->setKey('user', $data);
			$this->sessionObject->storeSessionData();
		} else {
			$this->sessionObject->setAndSaveSessionData($this->storageKey, $sessionData);
		}
	}

	protected function getSessionData ($persistent){
		if ($persistent && TYPO3_MODE === 'FE'){
			$sessionData = $this->sessionObject->getKey('user', $this->storageKey);
		} else {
			$sessionData = $this->sessionObject->getSessionData($this->storageKey);
		}
		return (array) $sessionData;
	}



}