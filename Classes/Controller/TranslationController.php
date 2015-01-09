<?php
namespace MONOGON\TranslationTools\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 R3 H6 <r3h6@outlook.com>
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

use \MONOGON\TranslationTools\Utility\FileUtility;

/**
 * TranslationController
 */
class TranslationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 *
	 * @var MONOGON\TranslationTools\Domain\Repository\TranslationRepository
	 * @inject
	 */
	protected $translationRepository;

	/**
	 * action list
	 *
	 * @param \MONOGON\TranslationTools\Domain\Model\Dto\Demand $demand
	 * @return void
	 */
	public function listAction(\MONOGON\TranslationTools\Domain\Model\Dto\Demand $demand = NULL) {
		if ($demand === NULL){
			$demand = $this->objectManager->get('MONOGON\\TranslationTools\\Domain\\Model\\Dto\\Demand');
		}

		$translations = $this->translationRepository->findDemanded($demand);
		$files = FileUtility::getLocallangFiles();
		$files2 = array();
		foreach ($files as $file){
			$files2[$file] = $file;
		}

		$this->view->assign('translations', $translations);
		$this->view->assign('demand', $demand);
		$this->view->assign('files', $files2);
	}

	/**
	 * action
	 *
	 * @return void
	 */
	public function Action() {

	}

}