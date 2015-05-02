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

use MONOGON\TranslationTools\Property\TypeConverter\FileUploadConverter;

/**
 * TranslationController
 */
class TranslationController extends ActionController {

	/**
	 * translationRepository
	 *
	 * @var \MONOGON\TranslationTools\Domain\Repository\TranslationRepository
	 * @inject
	 */
	protected $translationRepository = NULL;

	/**
	 * [$sessionService description]
	 * @var \MONOGON\TranslationTools\Service\SessionService
	 * @inject
	 */
	protected $sessionService = NULL;


	/**
	 * @param $view
	 */
	protected function initializeView($view) {
		$view->assign('layout', $this->isAjax ? 'Ajax' : 'Default');
		$view->assign('isAjax', $this->isAjax);
	}

	/**
	 * action list
	 *
	 * @param \MONOGON\TranslationTools\Domain\Model\Dto\Demand $demand
	 * @return void
	 */
	public function listAction(\MONOGON\TranslationTools\Domain\Model\Dto\Demand $demand = NULL) {

		// if (!$demand){
		// 	$demand = $this->sessionService->get('demand');
		// }

		try {
			$translations = $this->translationRepository->findDemanded($demand);
		} catch (ExecutionTimeException $exception) {
			$translations = NULL;
			$this->addFlashMessage($exception->getMessage());
		}
		if ($demand === NULL) {
			$demand = $this->objectManager->get('MONOGON\\TranslationTools\\Domain\\Model\\Dto\\Demand');
		}
		$this->view->assign('translations', $translations);
		$this->view->assign('demand', $demand);

		// $this->sessionService->set('demand', $demand);
	}

	/**
	 * action update
	 *
	 * @param \MONOGON\TranslationTools\Domain\Model\Translation $translation
	 * @return void
	 */
	public function updateAction(\MONOGON\TranslationTools\Domain\Model\Translation $translation) {
		$this->translationRepository->update($translation);

		$this->addFlashMessage('Successfully updated constant ' . $translation->getId());
	}

	/**
	 * action import
	 *
	 * @param array $file
	 * @return void
	 */
	public function importAction($file) {


		$this->addFlashMessage(print_r($file, TRUE));
		$this->redirect('list');
	}
}