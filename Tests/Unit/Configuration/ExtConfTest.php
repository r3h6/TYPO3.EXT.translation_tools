<?php

namespace MONOGON\TranslationTools\Tests\Unit\Configuration;

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

use \MONOGON\TranslationTools\Configuration\ExtConf;

/**
 * Test case for class \MONOGON\TranslationTools\Configuration\ExtConf.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author R3 H6 <r3h6@outlook.com>
 */
class ExtConfTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	const EXT_KEY = 'translation_tools';
	protected $extConf;

	protected function setUp (){
		$this->extConf = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::EXT_KEY];
		$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::EXT_KEY] = serialize(array(
				'fooBar' => 'foobar',
				'allowWriteToExtension' => 'ext1,ext2',
				'allowWriteToL10nDir' => 'ext1,ext2',
			));
	}

	protected function tearDown(){
		$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::EXT_KEY] = $this->extConf;
		unset($this->extConf);
	}

	/**
	 * @test
	 */
	public function fooBar (){
		$this->assertSame('foobar', ExtConf::get('fooBar'));
	}

	/**
	 * @test
	 */
	public function getAllowWriteToExtension (){
		$this->assertSame(
			array('ext1', 'ext2'),
			ExtConf::get('getAllowWriteToExtension')
		);
	}
}