<?php
namespace MONOGON\TranslationTools\Controller;

use MONOGON\TranslationTools\Exception\ExecutionTimeException;
use MONOGON\TranslationTools\Utility\TranslationUtility;
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
 * TranslationController
 */
abstract class ActionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	protected $isAjax;

	/**
	 * @param $view
	 */
	protected function initializeAction () {
		$this->isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Mvc\RequestInterface $request
	 * @param \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response
	 * @return void
	 * @throws \Exception
	 * @override \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
	 */
	public function processRequest(\TYPO3\CMS\Extbase\Mvc\RequestInterface $request, \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response) {
		try {
			parent::processRequest($request, $response);
		} catch(\Exception $exception) {
			if ($this->isAjax){
				if ($this->response instanceof \TYPO3\CMS\Extbase\Mvc\Web\Response) {
					$this->response->setStatus(500);
				}
				$this->response->appendContent($exception->getMessage());
			} else {
				throw $exception;
			}
		}
	}

	/**
	 * @return void
	 * @override \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
	 */
	protected function callActionMethod() {
		try {
			parent::callActionMethod();
		}
		catch(\Exception $exception) {
			if ($this->isAjax){
				if ($this->response instanceof \TYPO3\CMS\Extbase\Mvc\Web\Response) {
					$this->response->setStatus(500);
				}
				$this->response->appendContent($exception->getMessage());
			} else {
				throw $exception;
			}
		}
	}

	protected function getLogger (){
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager')->getLogger(__CLASS__);
	}
}