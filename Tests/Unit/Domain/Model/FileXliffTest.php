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
 * Test case for class \MONOGON\TranslationTools\Domain\Model\FileXliff.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author R3 H6 <r3h6@outlook.com>
 */
class FileXliffTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	protected $locallang = 'EXT:translation_tools/Tests/Resources/Private/Language/locallang.xlf';

	/**
	 * @var \MONOGON\TranslationTools\Domain\Model\FileXliff
	 */
	protected $subject = NULL;

	/**
	 * @var TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager = NULL;


	protected function setUp() {
		// $this->subject = $this->getMock('MONOGON\\TranslationTools\\Domain\\Model\\FileXliff', array(), array(), '', FALSE);
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function constructExisting (){
		$this->subject = new \MONOGON\TranslationTools\Domain\Model\FileXliff($this->locallang);
		$this->assertInstanceOf(
			'MONOGON\\TranslationTools\\Domain\\Model\\FileXliff',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function constructBlank (){
		$this->subject = new \MONOGON\TranslationTools\Domain\Model\FileXliff();
		$this->assertInstanceOf(
			'MONOGON\\TranslationTools\\Domain\\Model\\FileXliff',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function constructBlankDe (){
		$this->subject = new \MONOGON\TranslationTools\Domain\Model\FileXliff('de.locallang.xlf');
		$this->assertInstanceOf(
			'MONOGON\\TranslationTools\\Domain\\Model\\FileXliff',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function addTranslation (){
		$translationMock = $this->getTranslationMock();

		$this->subject = new \MONOGON\TranslationTools\Domain\Model\FileXliff($this->locallang);

		$this->subject->addTranslation($translationMock);
		$this->subject->addTranslation($translationMock);

		echo "<pre>" . htmlspecialchars($this->subject); exit;
	}

	protected function getTranslationMock ($id = 'hello', $target = 'Hallo', $source = 'Hello', $language = 'de'){
		// $translationMock = $this->getMock('MONOGON\\TranslationTools\\Domain\\Model\\Translation', array(), array(), '', TRUE);
		$translationMock = new \MONOGON\TranslationTools\Domain\Model\Translation();
		$translationMock->setId($id);
		$translationMock->setSource($source);
		$translationMock->setTarget($target);
		$translationMock->setTargetLanguage($language);

		return $translationMock;
	}
}