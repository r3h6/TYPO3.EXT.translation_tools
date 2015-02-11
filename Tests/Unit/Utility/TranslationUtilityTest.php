<?php

namespace MONOGON\TranslationTools\Tests\Unit\Utility;

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

use \MONOGON\TranslationTools\Utility\TranslationUtility;

/**
 * Test case for class \MONOGON\TranslationTools\Utility\TranslationUtility.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author R3 H6 <r3h6@outlook.com>
 */
class TranslationUtilityTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	protected function setUp() {
	}

	protected function tearDown() {
	}

	/**
	 * @test
	 * @dataProvider getIdFromTranslationProvider
	 */
	public function getIdFromTranslation ($translationKey, $expected){
		$this->assertEquals(
			$expected,
			TranslationUtility::getIdFromTranslationKey($translationKey)
		);
	}

	public function getIdFromTranslationProvider (){
		return array(
			array('foo.bar', 'foo.bar'),
			array('LLL:EXT:test/locallang.xml:foo.bar', 'foo.bar'),
			array('LLL:EXT:test/locallang.xlf:foo.bar', 'foo.bar'),
			array('LLL:fileadmin/locallang.xlf:foo.bar', 'foo.bar'),
			array('LLL:FILE:fileadmin/locallang.xlf:foo.bar', 'foo.bar'),
		);
	}
	/**
	 * @test
	 * @dataProvider getLocallangFromTranslationKeyProvider
	 */
	public function getLocallangFromTranslationKey ($translationKey, $expected, $default = NULL){
		$this->assertEquals(
			$expected,
			TranslationUtility::getLocallangFromTranslationKey($translationKey, $default)
		);
	}

	public function getLocallangFromTranslationKeyProvider (){
		return array(
			array('foo.bar', NULL),
			array('foo.bar', 'default', 'default'),
			array('LLL:EXT:test/locallang.xml:foo.bar', 'EXT:test/locallang.xml'),
			array('LLL:EXT:test/locallang.xlf:foo.bar', 'EXT:test/locallang.xlf'),
			array('LLL:fileadmin/locallang.xlf:foo.bar', 'fileadmin/locallang.xlf'),
			array('LLL:FILE:fileadmin/locallang.xlf:foo.bar', 'fileadmin/locallang.xlf'),
		);
	}


	/**
	 * @test
	 */
	public function extractFromFile (){
		$translations = TranslationUtility::extractFromFile('EXT:translation_tools/Tests/Resources/Private/Templates/Index.html');
		// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($translations); exit;
		$this->assertContainsOnly('MONOGON\TranslationTools\Domain\Model\Translation', $translations);
	}
}
