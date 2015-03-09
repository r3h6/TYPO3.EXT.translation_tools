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

/**
 * Test case for class \MONOGON\TranslationTools\Domain\Model\Page.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author R3 H6 <r3h6@outlook.com>
 */
class PageTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \MONOGON\TranslationTools\Domain\Model\Page
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = new \MONOGON\TranslationTools\Domain\Model\Page();
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getTitleReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getTitle()
		);
	}

	/**
	 * @test
	 */
	public function setTitleForStringSetsTitle() {
		$this->subject->setTitle('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'title',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getTsConfigReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getTsConfig()
		);
	}

	/**
	 * @test
	 */
	public function setTsConfigForStringSetsTsConfig() {
		$this->subject->setTsConfig('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'tsConfig',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getSiteRootReturnsInitialValueForBoolean() {
		$this->assertSame(
			FALSE,
			$this->subject->getSiteRoot()
		);
	}

	/**
	 * @test
	 */
	public function setSiteRootForBooleanSetsSiteRoot() {
		$this->subject->setSiteRoot(TRUE);

		$this->assertAttributeEquals(
			TRUE,
			'siteRoot',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getDoktypeReturnsInitialValueForInteger() {
		$this->assertSame(
			0,
			$this->subject->getDoktype()
		);
	}

	/**
	 * @test
	 */
	public function setDoktypeForIntegerSetsDoktype() {
		$this->subject->setDoktype(12);

		$this->assertAttributeEquals(
			12,
			'doktype',
			$this->subject
		);
	}
}
