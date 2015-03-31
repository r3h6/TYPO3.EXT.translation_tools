<?php

namespace MONOGON\TranslationTools\Tests\Unit\Persistence;

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
 * Test case for class \MONOGON\TranslationTools\Persistence\TranslationResultTest.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author R3 H6 <r3h6@outlook.com>
 */
class TranslationResultTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	private $subject;

	protected function setUp() {
		$this->subject = new \MONOGON\TranslationTools\Persistence\TranslationResult();
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function isCountable (){
		$this->assertTrue($this->subject instanceof \Countable);
	}

	/**
	 * @test
	 */
	public function isIterator (){
		$this->assertTrue($this->subject instanceof \Iterator);
	}

	/**
	 * @test
	 */
	public function isArrayAccess (){
		$this->assertTrue($this->subject instanceof \ArrayAccess);
	}

	/**
	 * @test
	 */
	public function addTranslation (){

		$this->subject->addTranslation(
			$this->getMockTranslation('a')
		);

		$this->subject->addTranslation(
			$this->getMockTranslation('b')
		);

		$this->subject->addTranslation(
			$this->getMockTranslation('b', 'de')
		);

		$this->assertCount(2, $this->subject);

		foreach ($this->subject as $key => $translation){
			// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($translation);
			// exit;
			$this->assertInternalType('array', $translation);

			$this->assertArrayHasKey('units', $translation);
		}

	}

	protected function getMockTranslation ($id, $language = 'en', $file = 'locallang.xlf'){
		$translationMock = $this->getMock('MONOGON\\TranslationTools\\Domain\\Model\\Translation', array('getId', 'getFile', 'getHashKey', 'getTargetLanguage'), array(), '', FALSE);

		$translationMock->method('getId')->will($this->returnValue($id));
		$translationMock->method('getFile')->will($this->returnValue($file));
		$translationMock->method('getTargetLanguage')->will($this->returnValue($language));
		$translationMock->method('getHashKey')->will($this->returnValue(md5("$file:$id")));

		return $translationMock;
	}
}
