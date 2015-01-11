<?php
namespace MONOGON\TranslationTools\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Remo Häusler <remo.haeusler@hotmail.com>
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

use \MONOGON\TranslationTools\Exception\ExecutionTimeException;

/**
 * TranslationController
 */
class TranslationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * translationRepository
	 *
	 * @var \MONOGON\TranslationTools\Domain\Repository\TranslationRepository
	 * @inject
	 */
	protected $translationRepository = NULL;

	/**
	 * action list
	 *
	 * @param \MONOGON\TranslationTools\Domain\Model\Dto\Demand $demand
	 * @return void
	 */
	public function listAction(\MONOGON\TranslationTools\Domain\Model\Dto\Demand $demand = NULL) {
		if ($demand === NULL) {
			$demand = $this->objectManager->get('MONOGON\\TranslationTools\\Domain\\Model\\Dto\\Demand');
		}
		try {
			$translations = $this->translationRepository->findDemanded($demand);
		} catch (ExecutionTimeException $exception){
			$translations = NULL;
			$this->addFlashMessage($exception->getMessage());
		}
		$this->view->assign('translations', $translations);
		$this->view->assign('demand', $demand);
	}

	/**
	 * action update
	 *
	 * @param \MONOGON\TranslationTools\Domain\Model\Translation $translation
	 * @return void
	 */
	public function updateAction(\MONOGON\TranslationTools\Domain\Model\Translation $translation) {
		// $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See <a href="http://wiki.typo3.org/T3Doc/Extension_Builder/Using_the_Extension_Builder#1._Model_the_domain" target="_blank">Wiki</a>', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR);
		// $this->translationRepository->update($translation);
		// $this->redirect('list');
	}

}