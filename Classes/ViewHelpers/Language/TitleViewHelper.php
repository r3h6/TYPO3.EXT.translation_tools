<?php
namespace MONOGON\TranslationTools\ViewHelpers\Language;

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
 * TitleViewHelper
 */
class TitleViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var MONOGON\TranslationTools\Domain\Repository\SystemLanguageRepository
	 * @inject
	 */
	protected $systemLanguageRepository = NULL;

	/**
	 * [render description]
	 * @param  string $key [description]
	 * @return [type]      [description]
	 */
	public function render ($key){
		$language = $this->systemLanguageRepository->findByFlag($key)->getFirst();
		if ($language){
			return $language->getTitle();
		}
		return ucfirst($key);
	}

}