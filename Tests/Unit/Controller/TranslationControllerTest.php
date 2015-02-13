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

use MONOGON\TranslationTools\Exception\ExecutionTimeException;

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
	public function listAction (){
		// Prepare mock value
		$demand = NULL;
		$translations = array();

		// Prepare mock repository
		$translationRepository = $this->getMock('MONOGON\\TranslationTools\\Domain\\Repository\\TranslationRepository', array('findDemanded'), array(), '', FALSE);

		$translationRepository
			->expects($this->once())
			->method('findDemanded')
			->with($demand)
			->will($this->returnValue($translations));

		$this->inject($this->subject, 'translationRepository', $translationRepository);

		// Prepare mock object manager and demand
		$demand = $this->getMock('MONOGON\\TranslationTools\\Domain\\Model\\Dto\\Demand');
		$objectManager = $this->getMockObjectManager($demand);
		$this->inject($this->subject, 'objectManager', $objectManager);

		// Prepare mock view
		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view
			->expects($this->at(0))
			->method('assign')
			->with('translations', $this->equalTo($translations));

		$view
			->expects($this->at(1))
			->method('assign')
			->with('demand', $this->equalTo($demand));

		$this->inject($this->subject, 'view', $view);

		// Test action
		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function listActionTranslationRepositoryThrowsExecutionTimeException (){
		// Prepare mock value
		$demand = NULL;
		$translations = NULL;

		// Prepare mock repository
		$translationRepository = $this->getMock('MONOGON\\TranslationTools\\Domain\\Repository\\TranslationRepository', array('findDemanded'), array(), '', FALSE);

		$translationRepository
			->expects($this->once())
			->method('findDemanded')
			->with($demand)
			->will($this->throwException(new ExecutionTimeException('Mock it!')));

		$this->inject($this->subject, 'translationRepository', $translationRepository);

		// Prepare flash message
		$this->subject
			->expects($this->once())
			->method('addFlashMessage')
			->with('Mock it!');

		// Prepare mock object manager and demand
		$demand = $this->getMock('MONOGON\\TranslationTools\\Domain\\Model\\Dto\\Demand');
		$objectManager = $this->getMockObjectManager($demand);
		$this->inject($this->subject, 'objectManager', $objectManager);

		// Prepare mock view
		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view
			->expects($this->at(0))
			->method('assign')
			->with('translations', $this->equalTo($translations));

		$view
			->expects($this->at(1))
			->method('assign')
			->with('demand', $this->equalTo($demand));

		$this->inject($this->subject, 'view', $view);

		// Test action
		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function differenceAction (){
		// Prepare mock values
		$ll = 'EXT:mock/locallang.xlf';
		$path = 'EXT:translation_tools/Tests';

		$requiredTranslations = array(
			'a' => $this->getMockTranslation($ll, 'mock.a'),
			'b' => $this->getMockTranslation($ll, 'mock.b'),
			'c' => $this->getMockTranslation($ll, 'mock.c'),
		);

		$availableTranslations = array(
			'a' => $this->getMockTranslation($ll, 'mock.a'),
			'd' => $this->getMockTranslation($ll, 'mock.d'),
		);

		$missingTranslations = array(
			'b' => $this->getMockTranslation($ll, 'mock.b'),
			'c' => $this->getMockTranslation($ll, 'mock.c'),
		);

		$unusedTranslations = array(
			'd' => $this->getMockTranslation($ll, 'mock.d'),
		);

		// Prepare mock repository
		$translationRepository = $this->getMock('MONOGON\\TranslationTools\\Domain\\Repository\\TranslationRepository', array('findInSourceCode', 'findInLocallangFiles'), array(), '', FALSE);

		$translationRepository
			->expects($this->once())
			->method('findInSourceCode')
			->with($this->equalTo($path))
			->will($this->returnValue($requiredTranslations));

		$translationRepository
			->expects($this->once())
			->method('findInLocallangFiles')
			->with($this->equalTo(array($ll)))
			->will($this->returnValue($availableTranslations));

		$this->inject($this->subject, 'translationRepository', $translationRepository);

		// Prepare mock view
		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view
			->expects($this->at(0))
			->method('assign')
			->with('missingTranslations', $this->equalTo($missingTranslations));

		$view
			->expects($this->at(1))
			->method('assign')
			->with('unusedTranslations', $this->equalTo($unusedTranslations));

		$this->inject($this->subject, 'view', $view);

		// Test action
		$this->subject->differenceAction($path);
	}

	protected function getMockTranslation ($file, $id){
		$translation = $this->getMock('MONOGON\\TranslationTools\\Domain\\Repository\\TranslationRepository', array('getFile', 'getId', 'getHashKey'));

		$translation
			->method('getFile')
			->will($this->returnValue($file));
		$translation
			->method('getId')
			->will($this->returnValue($id));
		$translation
			->method('getHashKey')
			->will($this->returnValue(md5($file . ':' . $id)));

		return $translation;
	}

	protected function getMockObjectManager (){
		$mockObjects =func_get_args();
		$objectManager = $this->getMock('TYPO3\\CMS\\Extbase\\Object\\ObjectManager', array('get'));
		$objectManager
			->method('get')
			->will(call_user_func_array(array($this, 'returnValue'), $mockObjects));
		return $objectManager;
	}


}
