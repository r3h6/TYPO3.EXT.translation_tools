<?php

namespace MONOGON\TranslationTools\Tests\Unit\Service;

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
 * Test case for class \MONOGON\TranslationTools\Service\TerService.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author R3 H6 <r3h6@outlook.com>
 */
class TerServiceTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	protected $subject;

	protected function setUp() {
		$this->subject = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager')->get('MONOGON\\TranslationTools\\Service\\TerService');
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 * @dataProvider fetchTranslationStatusProvider
	 */
	public function fetchTranslationStatus ($extensionKey, $expected){
		$this->assertEquals(
			$expected,
			is_array($this->subject->fetchTranslationStatus($extensionKey))
		);

	}

	public function fetchTranslationStatusProvider (){
		return array(
			array('news', TRUE),
			array('not_existing_extension', FALSE),
		);
	}


}
