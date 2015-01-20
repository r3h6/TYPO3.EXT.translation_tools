<?php

namespace MONOGON\TranslationTools\Tests\Unit\Domain\Model\File;

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
 * Test case for class \MONOGON\TranslationTools\Domain\Model\File\Xliff.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author R3 H6 <r3h6@outlook.com>
 */
class XliffTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \MONOGON\TranslationTools\Domain\Model\File\Xliff
	 */
	protected $subject = NULL;

	/**
	 * @var TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager = NULL;

	protected function setUp() {
		$this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$this->subject = $this->objectManager->get('MONOGON\\TranslationTools\\Domain\\Model\\File\\Xliff');

		//$this->inject($this->subject, 'view', new \TYPO3\CMS\Fluid\View\StandaloneView());
	}

	protected function tearDown() {
		unset($this->subject);
		unset($this->objectManager);
	}

	/**
	 * @test
	 */
	public function renderEmpty () {
		//$expected =  \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('translation_tools') . 'Resources/Private/Backend/Templates/File/File.xlf';

		$expected = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<xliff version="1.0">
	<file source-language="" datatype="plaintext" original="messages" date="' . date('c') . '" product-name="translation_tools">
		<header>
			<generator>TranslationTools</generator>
		</header>
		<body>
		</body>
	</file>
</xliff>';

		$this->assertXmlStringEqualsXmlString($expected, $this->subject->render()->getContent());
	}

	/**
	 * @test
	 */
	public function saveAs (){
		$path = GeneralUtility::tempnam('test_', '.xlf');
		$this->subject->saveAs($path);
		$this->assertFileExists($path);
		GeneralUtility::unlink_tempfile($path);
		$this->assertFileNotExists($path);
	}
}
