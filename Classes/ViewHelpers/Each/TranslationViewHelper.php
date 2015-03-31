<?php
namespace MONOGON\TranslationTools\ViewHelpers\Each;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2014 R3 H6 <r3h6@outlook.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * TranslationViewHelper
 */
class TranslationViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * [render description]
	 * @param  array $translations [description]
	 * @param  array $languages    [description]
	 * @param  string $as           [description]
	 * @return string               [description]
	 */
	public function render ($translations, $languages, $as = 'unit'){
		foreach ($languages as $language){
			if (isset($translations['unit'][$language])){
				$unit = $translations['unit'][$language];
			} else {
				$unit = $this->objectManager->get('MONOGON\\TranslationTools\\Domain\\Model\\Translation');
				$unit
					->setTargetLanguage($language)
					->setId($translations['id'])
					->setFile($translations['File']);
			}

			if ($language == 'default'){
				$unit->setTargetLanguage('');
			}
		}
	}

}