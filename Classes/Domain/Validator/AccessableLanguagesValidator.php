<?php
namespace MONOGON\TranslationTools\Domain\Validator;

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
 * The repository for AccessableLanguagesValidator
 */
class AccessableLanguagesValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator {
	/**
	 * @var MONOGON\TranslationTools\Domain\Repository\SystemLanguageRepository
	 * @inject
	 */
	protected $systemLanguageRepository = NULL;

	public function isValid ($languages){

		if (is_array($languages)){
			$systemLanguages = $this->systemLanguageRepository->findAllAccessableSystemLanguages();
			$flags = array();
			foreach ($systemLanguages as $systemLanguage){
				$flags[] = $systemLanguage->getFlag();
			}

			foreach ($languages as $language){
				if (!in_array($language, $flags)){
						$this->addError('Language is not allowed!', 1415391144);
				}
			}

		}

		// if (!is_string($value) || !$this->validPhoneNumber($value)) {
		// 	$this->addError(
		// 		$this->translateErrorMessage(
		// 			'validator.phonenumber.notvalid',
		// 			'addressCollection'
		// 		), 1415391144);
		// }
	}
}