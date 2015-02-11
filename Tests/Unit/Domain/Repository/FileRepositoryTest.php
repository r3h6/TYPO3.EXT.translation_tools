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
use \MONOGON\TranslationTools\Utility\FileUtility;

/**
 * Test case for class \MONOGON\TranslationTools\Domain\Model\Translation.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author R3 H6 <r3h6@outlook.com>
 */
class FileRepositoryTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \MONOGON\TranslationTools\Domain\Repository\FileRepository
	 */
	protected $subject = NULL;

	/**
	 * @var TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager = NULL;

	protected static $locallangFile = 'typo3conf/ext/translation_tools/Tests/Resources/Private/Language/locallang.xlf';

	protected function setUp() {
		$this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$this->subject = $this->objectManager->get('MONOGON\\TranslationTools\\Domain\\Repository\\FileRepository');
	}

	protected function tearDown() {
		unset($this->objectManager);
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function makeInstanceXliff() {
		$identifier = 'path/to/somewhere/locallang.xlf';
		$this->assertInstanceOf(
			'MONOGON\\TranslationTools\\Domain\\Model\\FileXliff',
			$this->subject->makeInstance($identifier)
		);
	}

	/**
	 * @test
	 */
	public function makeInstanceXml() {
		$identifier = 'path/to/somewhere/locallang.xml';
		$this->assertInstanceOf(
			'MONOGON\\TranslationTools\\Domain\\Model\\FileXml',
			$this->subject->makeInstance($identifier)
		);
	}

	/**
	 * @test
	 * @expectedException \InvalidArgumentException
	 */
	public function makeInstanceException() {
		$identifier = 'path/to/somewhere/setup.foo';
		$this->subject->makeInstance($identifier);
	}

	/**
	 * @test
	 */
	public function makeInstanceCheckConstructor() {
		$identifier = 'path/to/somewhere/locallang.xlf';
		$file = $this->subject->makeInstance($identifier);
		$this->assertSame(
			$identifier,
			$file->getIdentifier()
		);
	}

	/**
	 * @test
	 */
	public function backup (){
		$expected = PATH_site . FileUtility::makeBackupPath(self::$locallangFile);
		$file = $this->subject->makeInstance(self::$locallangFile);
		$this->subject->backup($file);
		$this->assertFileExists($expected);
		unlink($expected);
		$this->assertFileNotExists($expected);
	}

}
