<?php

namespace MONOGON\TranslationTools\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Remo Häusler <remo.haeusler@hotmail.com>
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
 * Test case for class \MONOGON\TranslationTools\Domain\Model\Translation.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author Remo Häusler <remo.haeusler@hotmail.com>
 */
class TranslationTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \MONOGON\TranslationTools\Domain\Model\Translation
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = new \MONOGON\TranslationTools\Domain\Model\Translation();
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getIdReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getId()
		);
	}

	/**
	 * @test
	 */
	public function setIdForStringSetsId() {
		$this->subject->setId('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'id',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getFileReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getFile()
		);
	}

	/**
	 * @test
	 */
	public function setFileForStringSetsFile() {
		$this->subject->setFile('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'file',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getUnitsReturnsInitialValueForTranslationUnit() {
		$newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->subject->getUnits()
		);
	}

	/**
	 * @test
	 */
	public function setUnitsForObjectStorageContainingTranslationUnitSetsUnits() {
		$unit = new \MONOGON\TranslationTools\Domain\Model\TranslationUnit();
		$objectStorageHoldingExactlyOneUnits = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$objectStorageHoldingExactlyOneUnits->attach($unit);
		$this->subject->setUnits($objectStorageHoldingExactlyOneUnits);

		$this->assertAttributeEquals(
			$objectStorageHoldingExactlyOneUnits,
			'units',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function addUnitToObjectStorageHoldingUnits() {
		$unit = new \MONOGON\TranslationTools\Domain\Model\TranslationUnit();
		$unitsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('attach'), array(), '', FALSE);
		$unitsObjectStorageMock->expects($this->once())->method('attach')->with($this->equalTo($unit));
		$this->inject($this->subject, 'units', $unitsObjectStorageMock);

		$this->subject->addUnit($unit);
	}

	/**
	 * @test
	 */
	public function removeUnitFromObjectStorageHoldingUnits() {
		$unit = new \MONOGON\TranslationTools\Domain\Model\TranslationUnit();
		$unitsObjectStorageMock = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array('detach'), array(), '', FALSE);
		$unitsObjectStorageMock->expects($this->once())->method('detach')->with($this->equalTo($unit));
		$this->inject($this->subject, 'units', $unitsObjectStorageMock);

		$this->subject->removeUnit($unit);

	}
}
