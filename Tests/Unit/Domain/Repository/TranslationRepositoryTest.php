<?php

namespace MONOGON\TranslationTools\Tests\Unit\Repository;

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

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Test case for class \MONOGON\TranslationTools\Domain\Model\Translation.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author R3 H6 <r3h6@outlook.com>
 */
class TranslationRepositoryTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \MONOGON\TranslationTools\Domain\Repository\TranslationRepository
	 */
	protected $subject = NULL;

	/**
	 * @var TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager = NULL;

	protected static $locallangTranslation = 'typo3conf/ext/translation_tools/Tests/Resources/Private/Language/locallang.xlf';

	protected function setUp() {
		$this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$this->subject = $this->objectManager->get('MONOGON\\TranslationTools\\Domain\\Repository\\TranslationRepository');
	}

	protected function tearDown() {
		unset($this->objectManager);
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function findDemandedDefault (){
		$mockDemand = $this->getMock('MONOGON\\TranslationTools\\Domain\\Model\\Dto\\Demand');
		$mockDemand->method('getFile')->willReturn('EXT:translation_tools/Tests/Resources/Private/Language/locallang.xlf');
		$mockDemand->method('getLanguages')->willReturn(array('default'));
		// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($mockDemand); exit;
		$result = $this->subject->findDemanded($mockDemand);

		$this->assertCount(3, $result);
		foreach ($result as $value){
			$this->assertArrayHasKey('_id', $value);
			$this->assertArrayHasKey('_file', $value);
			$this->assertArrayHasKey('_source', $value);
			$this->assertArrayHasKey('default', $value);

			$this->assertInstanceOf('MONOGON\\TranslationTools\\Domain\\Model\\Translation', $value['default']);
		}

		$this->assertSame(
			'Test A',
			reset($result)['default']->getTarget()
		);
	}

	/**
	 * @test
	 */
	public function findInSourceCode (){
		$translations = $this->subject->findInSourceCode('EXT:translation_tools');
		$this->assertContainsOnly('MONOGON\TranslationTools\Domain\Model\Translation', $translations);
	}

	/**
	 * @test
	 */
	public function findInLocallangFiles (){
		$translations = $this->subject->findInLocallangFile(array('EXT:translation_tools/Tests/Resources/Private/Language/locallang.xlf'));
		$this->assertContainsOnly('MONOGON\TranslationTools\Domain\Model\Translation', $translations);
	}
}
