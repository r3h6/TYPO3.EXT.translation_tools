<?php
namespace MONOGON\TranslationTools\Tests\Unit\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 R3 H6 <r3h6@outlook.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class MONOGON\TranslationTools\Controller\TranslationController.
 *
 * @author R3 H6 <r3h6@outlook.com>
 */
class TranslationControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \MONOGON\TranslationTools\Controller\TranslationController
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = $this->getMock('MONOGON\\TranslationTools\\Controller\\TranslationController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listAction () {

		$translations = array();
		$demand = 'Demand!';

		$translationRepository = $this->getMock('MONOGON\\TranslationTools\\Domain\\Repository\\TranslationRepository', array('findDemanded'), array(), '', FALSE);
		$translationRepository
			->expects($this->once())
			->method('findDemanded')
			->will($this->returnValue($translations));

		$this->inject($this->subject, 'translationRepository', $translationRepository);

		$objectManager = $this->getMock('TYPO3\\CMS\\Extbase\\Object\\ObjectManager', array('get'), array(), '', FALSE);
		$objectManager
			->expects($this->any())
			->method('get')
			->will($this->returnValue($demand));

		$this->inject($this->subject, 'objectManager', $objectManager);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view
			->expects($this->at(0))
			->method('assign')
			->with('translations', $translations);
		$view
			->expects($this->at(1))
			->method('assign')
			->with('demand', $demand);


		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function updateAction () {
		$translation = new \MONOGON\TranslationTools\Domain\Model\Translation();

		$translationRepository = $this->getMock('MONOGON\\TranslationTools\\Domain\\Repository\\TranslationRepository', array('update'), array(), '', FALSE);
		$translationRepository
			->expects($this->once())
			->method('update')
			->with($translation);

		$this->inject($this->subject, 'translationRepository', $translationRepository);

		$this->subject->updateAction($translation);
	}
}
