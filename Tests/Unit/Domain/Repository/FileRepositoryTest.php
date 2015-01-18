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
class FileRepositoryTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \MONOGON\TranslationTools\Domain\Repository\FileRepository
	 */
	protected $subject = NULL;

	/**
	 * @var TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager = NULL;

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
	public function findByIdentifierXliff() {
		$identifier = 'path/to/somewhere/locallang.xlf';
		$this->assertInstanceOf(
			'MONOGON\\TranslationTools\\Domain\\Model\\File\\Xliff',
			$this->subject->findByIdentifier($identifier)
		);
	}

	/**
	 * @test
	 */
	public function findByIdentifierXml() {
		$identifier = 'path/to/somewhere/locallang.xml';
		$this->assertInstanceOf(
			'MONOGON\\TranslationTools\\Domain\\Model\\File\\Xml',
			$this->subject->findByIdentifier($identifier)
		);
	}

	/**
	 * @test
	 */
	public function findByIdentifierTypoScript() {
		$identifier = 'path/to/somewhere/setup.txt';
		$this->assertInstanceOf(
			'MONOGON\\TranslationTools\\Domain\\Model\\File\\TypoScript',
			$this->subject->findByIdentifier($identifier)
		);
	}

	/**
	 * @test
	 * @expectedException \InvalidArgumentException
	 */
	public function findByIdentifierException() {
		$identifier = 'path/to/somewhere/setup.foo';
		$this->subject->findByIdentifier($identifier);
	}

	/**
	 * @test
	 */
	public function findByIdentifierCheckConstructor() {
		$identifier = 'path/to/somewhere/locallang.xlf';
		$file = $this->subject->findByIdentifier($identifier);
		$this->assertSame(
			$identifier,
			$file->getIdentifier()
		);
	}
}