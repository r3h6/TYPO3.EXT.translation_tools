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
			'locallangDirectories' => 'EXT:translation_tools/Tests/',
		));
	}

	protected function tearDown() {
		$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::EXT_KEY] = $this->extConf;
	}

	/**
	 * @test
	 */
	public function getLocallangFilesUncached (){
		$files = FileUtility::getLocallangFiles(FALSE);
	}

	/**
	 * @test
	 */
	public function getExtensionDirectories (){
		$files = FileUtility::getExtensionDirectories();

		\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($files);
		exit;
	}


	/**
	 * @test
	 * @dataProvider addLanguageToPathProvider
	 */
	public function addLanguageToPath ($language, $identifier, $expected){
		$this->assertEquals($expected, FileUtility::addLanguageToPath($identifier, $language));
	}

	public function addLanguageToPathProvider (){
		return array(
			array(
				'de',
				'typo3conf/ext/news/Resources/Private/Language/locallang.xlf',
				'typo3conf/ext/news/Resources/Private/Language/de.locallang.xlf',
			),
			array(
				'de',
				'EXT:news/Resources/Private/Language/locallang.xlf',
				'EXT:news/Resources/Private/Language/de.locallang.xlf',
			),
			array(
				'default',
				'EXT:news/Resources/Private/Language/locallang.xlf',
				'EXT:news/Resources/Private/Language/locallang.xlf',
			),
		);
	}

	/**
	 * @test
	 * @dataProvider determineLanguageFileProvider
	 */
	public function determineLanguageFile ($language, $identifier, $expected){
		$this->assertEquals($expected, FileUtility::determineLanguageFile($identifier, $language));
	}

	public function determineLanguageFileProvider (){
		return array(
			array(
				'de',
				'typo3conf/ext/news/Resources/Private/Language/locallang.xml',
				$this->l10nOverwriteDir . 'news/Resources/Private/Language/de.locallang.xml',
			),
			array(
				'de',
				'EXT:news/Resources/Private/Language/locallang.xml',
				$this->l10nOverwriteDir . 'news/Resources/Private/Language/de.locallang.xml',
			),
			array(
				'fr',
				'typo3conf/ext/test_extension/Resources/Private/Language/locallang.xlf',
				$this->l10nDir . "fr/test_extension/Resources/Private/Language/fr.locallang.xlf",
			),
			array(
				'fr',
				'EXT:test_extension/Resources/Private/Language/locallang.xlf',
				$this->l10nDir . "fr/test_extension/Resources/Private/Language/fr.locallang.xlf",
			),
			array(
				'de',
				'typo3conf/ext/translation_tools/Tests/Resources/Private/Language/locallang.xlf',
				$this->l10nOverwriteDir . "translation_tools/Tests/Resources/Private/Language/de.locallang.xlf",
			),
			array(
				'de',
				'EXT:translation_tools/Tests/Resources/Private/Language/locallang.xlf',
				$this->l10nOverwriteDir . "translation_tools/Tests/Resources/Private/Language/de.locallang.xlf",
			),
			array(
				'default',
				'typo3conf/ext/translation_tools/Tests/Resources/Private/Language/locallang.xlf',
				$this->l10nOverwriteDir . "translation_tools/Tests/Resources/Private/Language/locallang.xlf",
			),
		);
	}


	/**
	 * @test
	 * @dataProvider extractExtKeyProvider
	 */
	public function extractExtKey ($expected, $path){
		$this->assertEquals($expected, FileUtility::extractExtKey($path));
	}

	public function extractExtKeyProvider (){
		return array(
			array('test', 'typo3conf/ext/test/Resources/Private/Language/locallang.xlf'),
			array('test', 'EXT:test/Resources/Private/Language/locallang.xlf'),
			array(NULL, 'fileadmin/templates/Private/Language/locallang.xlf'),
			array('test', 'typo3conf/ext/test/typo3conf/ext/foobar/locallang.xlf'),
			array('test', 'EXT:test/typo3conf/EXT:foobar/locallang.xlf'),
			array('test', 'typo3conf\\ext\\test\\Resources\\Private\\Language\\locallang.xlf'),
			array('test', 'EXT:test\\Resources\\Private\\Language\\locallang.xlf'),
			array(NULL, 'fileadmin/templates/typo3conf/ext/test/locallang.xlf'),
			array(NULL, 'typo3conf/ext/locallang.xlf'),
			array('test', PATH_site . 'typo3conf/ext/test/Resources/Private/Language/locallang.xlf'),
		);
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
