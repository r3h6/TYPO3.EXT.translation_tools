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

/**
 * The repository for AccessableLanguagesValidator
 */
class FileUploadValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator {

	const CONFLICT_MODE_OVERWRITE = 'overwrite';
	const CONFLICT_MODE_RENAME = 'rename';
	const CONFLICT_MODE_ERROR = 'error';

	/**
	 * [$sessionService description]
	 * @var \MONOGON\TranslationTools\Service\SessionService
	 * @inject
	 */
	protected $sessionService = NULL;

	/**
	 * @var array
	 */
	protected $supportedOptions = array(
		'uploadDirectory' => array('typo3temp/', 'File upload directory', 'string'),
		'allowedExtensions' => array('', 'Allowed file extensions.', 'string'),
		'conflictMode' => array(FileUploadValidator::CONFLICT_MODE_RENAME, 'How to handle if file already exists.', 'string'),
		'sessionKey' => array('fileUpload', 'Session key for file data.', 'string'),
	);

	public function isValid ($value){

		// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this); exit;

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
				return $this->addError('File extension is not allowed!', 1399312430);
			}
		}

		// Build upload directory path.
		$uploadDirectory = $this->options['uploadDirectory'];
		$uploadDirectory = GeneralUtility::getFileAbsFileName($uploadDirectory);
		$uploadDirectory = PathUtility::sanitizeTrailingSeparator($uploadDirectory);

		// Build file path.
		$basicFileFunctions = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Utility\\File\\BasicFileUtility');
		$fileName = $value['name'];
		$fileName = $basicFileFunctions->cleanFileName($fileName, 'utf-8');
		$filePath = $uploadDirectory . $fileName;

		$conflictMode = $this->options['conflictMode'];
		if ($conflictMode === self::CONFLICT_MODE_ERROR && file_exists($filePath)){
			return $this->addError('File already exists!', 1427918043);
		}

		if ($conflictMode === self::CONFLICT_MODE_RENAME){
			$filePath = $basicFileFunctions->getUniqueName($fileName, $uploadDirectory);
		}

		// Move file.
		try {
			if (!GeneralUtility::upload_copy_move($value['tmp_name'], $filePath)){
				throw new \Exception('File could not be moved!', 1427918324);
			}
		} catch (\Exception $exception){
			return $this->addError($exception->getMessage(), $exception->getCode());
		}

		// Store in session.
		$value['filePath'] = $filePath;
		$this->sessionService->set($this->options['sessionKey'], $value);

		// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($value);
	}
}