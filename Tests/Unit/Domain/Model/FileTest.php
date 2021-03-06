<?php

namespace MONOGON\TranslationTools\Tests\Unit\Domain\Model;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Test case for class \MONOGON\TranslationTools\Domain\Model\File.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author R3 H6 <r3h6@outlook.com>
 */
class FileTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \MONOGON\TranslationTools\Domain\Model\File
	 */
	protected $subject = NULL;

	protected function setUp() {
		// $this->subject = $this->getMock('MONOGON\\TranslationTools\\Domain\\Model\\File', array(), array(), '', FALSE);
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function exists (){
		$this->subject = $this->getSubject('EXT:translation_tools/Tests/Resources/Private/Language/de.locallang.xlf');
		$this->assertSame(
			TRUE,
			$this->subject->exists()
		);
	}

	/**
	 * @test
	 */
	public function getTranslations (){
		$this->subject = $this->getSubject('EXT:translation_tools/Tests/Resources/Private/Language/de.locallang.xlf');
		$this->assertCount(
			3,
			$this->subject->getTranslations()
		);
	}


	protected function getSubject ($path){
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager')->get('MONOGON\\TranslationTools\\Domain\\Model\\File', $path);
	}
}