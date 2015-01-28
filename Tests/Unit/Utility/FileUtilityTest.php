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

use \MONOGON\TranslationTools\Utility\FileUtility;

/**
 * Test case for class \MONOGON\TranslationTools\Utility\FileUtility.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author R3 H6 <r3h6@outlook.com>
 */
class FileUtilityTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	// protected $dummyLocalExtensionPath = 'typo3conf/ext/test/Resources/Private/Language/locallang.xlf';
	// protected $dummyLocalExtensionPathBackslashes = 'typo3conf\\ext\\test\\Resources\\Private\\Language\\locallang.xlf';
	// protected $dummyWeirdLocalExtensionPath = 'typo3conf/ext/test/typo3conf/ext/foobar/locallang.xlf';
	// protected $dummyFileadminPath = 'fileadmin/templates/Private/Language/locallang.xlf';
	// protected $dummyWeirdFileadminPath = 'fileadmin/templates/typo3conf/ext/test/locallang.xlf';
	// protected $dummyBrokenPath = 'typo3conf/ext/locallang.xlf';
	const EXT_KEY = 'translation_tools';
	protected $l10nDir = 'typo3conf/l10n/';
	protected $l10nOverwriteDir = 'EXT:l10n_overwrite/Resources/Private/l10n/';

	protected $extConf;

	protected function setUp() {
		$this->extConf = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::EXT_KEY];
		$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::EXT_KEY] = serialize(array(
			'allowWriteToExtension' => '',
			'getAllowWriteToL10nDir' => '',
			'allowWriteToExtension' => '0',
		));
	}

	protected function tearDown() {
		$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::EXT_KEY] = $this->extConf;
	}


	/**
	 * @test
	 */
	public function addLanguageToPathGerman (){
		$language = 'de';
		$identifier = 'typo3conf/ext/news/Resources/Private/Language/locallang.xlf';
		$expected = 'typo3conf/ext/news/Resources/Private/Language/de.locallang.xlf';
		$this->assertEquals($expected, FileUtility::addLanguageToPath($identifier, $language));


		$identifier = 'EXT:news/Resources/Private/Language/locallang.xlf';
		$expected = 'EXT:news/Resources/Private/Language/de.locallang.xlf';
		$this->assertEquals($expected, FileUtility::addLanguageToPath($identifier, $language));
	}

	/**
	 * @test
	 */
	public function determineLanguageFileForNewsGerman (){
		$language = 'de';
		$identifier = 'typo3conf/ext/news/Resources/Private/Language/locallang.xml';
		$expected = $this->l10nOverwriteDir . "news/Resources/Private/Language/$language.locallang.xml";
		$this->assertEquals($expected, FileUtility::determineLanguageFile($identifier, $language));


		$identifier = 'EXT:news/Resources/Private/Language/locallang.xml';
		$expected = $this->l10nOverwriteDir . "news/Resources/Private/Language/$language.locallang.xml";
		$this->assertEquals($expected, FileUtility::determineLanguageFile($identifier, $language));
	}

	/**
	 * @test
	 */
	public function determineLanguageFileForTestExtensionFrench (){
		$language = 'fr';
		$identifier = 'typo3conf/ext/test_extension/Resources/Private/Language/locallang.xlf';
		$expected = $this->l10nDir . "$language/test_extension/Resources/Private/Language/$language.locallang.xlf";
		$this->assertEquals($expected, FileUtility::determineLanguageFile($identifier, $language));

		$identifier = 'EXT:test_extension/Resources/Private/Language/locallang.xlf';
		$expected = $this->l10nDir . "$language/test_extension/Resources/Private/Language/$language.locallang.xlf";
		$this->assertEquals($expected, FileUtility::determineLanguageFile($identifier, $language));
	}

	/**
	 * @test
	 */
	public function determineLanguageFileForTestsGerman (){
		$language = 'de';
		$expected = $this->l10nOverwriteDir . "translation_tools/Tests/Resources/Private/Language/$language.locallang.xlf";

		$identifier = 'typo3conf/ext/translation_tools/Tests/Resources/Private/Language/locallang.xlf';
		$this->assertEquals($expected, FileUtility::determineLanguageFile($identifier, $language));


		$identifier = 'EXT:translation_tools/Tests/Resources/Private/Language/locallang.xlf';
		$this->assertEquals($expected, FileUtility::determineLanguageFile($identifier, $language));
	}

	/**
	 * @test
	 */
	public function extractExtKeyFromLocalExtensionPath() {
		$this->assertEquals('test', FileUtility::extractExtKey('typo3conf/ext/test/Resources/Private/Language/locallang.xlf'));
		$this->assertEquals('test', FileUtility::extractExtKey('EXT:test/Resources/Private/Language/locallang.xlf'));
	}

	/**
	 * @test
	 */
	public function extractExtKeyFromFileadminPath() {
		$this->assertEquals(NULL, FileUtility::extractExtKey('fileadmin/templates/Private/Language/locallang.xlf'));
	}

	/**
	 * @test
	 */
	public function extractExtKeyFromWeirdLocalExtensionPath() {
		$this->assertEquals('test', FileUtility::extractExtKey('typo3conf/ext/test/typo3conf/ext/foobar/locallang.xlf'));
		$this->assertEquals('test', FileUtility::extractExtKey('EXT:test/typo3conf/EXT:foobar/locallang.xlf'));
	}

	/**
	 * @test
	 */
	public function extractExtKeyFromLocalExtensionPathBackslashes() {
		$this->assertEquals('test', FileUtility::extractExtKey('typo3conf\\ext\\test\\Resources\\Private\\Language\\locallang.xlf'));
		$this->assertEquals('test', FileUtility::extractExtKey('EXT:test\\Resources\\Private\\Language\\locallang.xlf'));
	}

	/**
	 * @test
	 */
	public function extractExtKeyFromWeirdFileadminPath() {
		$this->assertEquals(NULL, FileUtility::extractExtKey('fileadmin/templates/typo3conf/ext/test/locallang.xlf'));
	}

	/**
	 * @test
	 */
	public function extractExtKeyFromBrokenPath() {
		$this->assertEquals(NULL, FileUtility::extractExtKey('typo3conf/ext/locallang.xlf'));
	}


	/**
	 * @test
	 */
	public function makeBackupPath() {
		$this->assertEquals(
			'uploads/tx_translationtools/test/locallang.xlf',
			FileUtility::makeBackupPath('typo3conf/ext/test/locallang.xlf')
		);
	}

	/**
	 * @test
	 */
	public function makeOverwritePath (){
		$this->assertEquals(
			$this->l10nOverwriteDir . 'test/de.locallang.xml',
			FileUtility::makeOverwritePath('typo3conf/ext/test/locallang.xml', 'de')
		);
		$this->assertEquals(
			$this->l10nOverwriteDir . 'test/de.locallang.xml',
			FileUtility::makeOverwritePath('EXT:test/locallang.xml', 'de')
		);
	}
}
