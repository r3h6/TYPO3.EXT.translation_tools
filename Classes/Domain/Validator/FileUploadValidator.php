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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\Flow\Utility\Files;
use MONOGON\TranslationTools\Utility\LocalizationUtility;

/**
 * The repository for AccessableLanguagesValidator
 */
class FileUploadValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator {

	/**
	 * @var array
	 */
	protected $supportedOptions = array(
		'allowedExtensions' => array('', 'Allowed file extensions.', 'string'),
	);

	public function isValid ($value){

		// Check if value is a file upload.
		if (!is_array($value) || !array_key_exists('tmp_name', $value) || !array_key_exists('error', $value)){
			return $this->addError('Value is not a file upload!', 1427913855);
		}

		// Check for upload errors.
		if ($value['error'] !== \UPLOAD_ERR_OK) {
			switch ($value['error']) {
				case \UPLOAD_ERR_INI_SIZE:
				case \UPLOAD_ERR_FORM_SIZE:
				case \UPLOAD_ERR_PARTIAL:
					 return $this->addError(Files::getUploadErrorMessage($value['error']), 1264440823);
				default:
					return $this->addError('An error occurred while uploading. Please try again or contact the administrator if the problem remains', 1340193849);
			}
		}

		// Check against deny pattern.
		if (!GeneralUtility::verifyFilenameAgainstDenyPattern($value['name'])) {
			return $this->addError('File extensions is not allowed!', 1399312430);
		}

		// Check against allowed pattern.
		$allowedExtensions = $this->options['allowedExtensions'];
		if (!empty($allowedExtensions)){
			$filePathInfo = PathUtility::pathinfo($value['name']);
			if (!GeneralUtility::inList($allowedExtensions, strtolower($filePathInfo['extension']))) {
				return $this->addError(LocalizationUtility::translate('Only files with extension "%s" are allowed.', array($allowedExtensions)), 1399312430);
			}
		}

	}
}