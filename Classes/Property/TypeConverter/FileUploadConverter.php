<?php
namespace MONOGON\TranslationTools\Property\TypeConverter;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Helmut Hummel
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

use TYPO3\CMS\Core\Resource\Exception\ExistingTargetFileNameException;
use TYPO3\CMS\Core\Resource\File as FalFile;
use TYPO3\CMS\Core\Resource\FileReference as FalFileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Property\Exception\TypeConverterException;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;
use TYPO3\Flow\Utility\Files;

/**
 * Class FileUploadConverter
 */
class FileUploadConverter extends AbstractTypeConverter {

	/**
	 * Folder where the file upload should go to (including storage).
	 */
	const CONFIGURATION_UPLOAD_FOLDER = 1;

	/**
	 * How to handle a upload when the name of the uploaded file conflicts.
	 */
	const CONFIGURATION_UPLOAD_CONFLICT_MODE = 2;

	/**
	 * Wheter to replace an already present resource.
	 * Useful "for maxitems = 1" fields / propeties
	 * with no ObjectStorage annotation.
	 */
	const CONFIGURATION_ALLOWED_FILE_EXTENSIONS = 4;

	/**
	 * @var string
	 */
	protected $defaultUploadFolder = '1:/user_upload/';

	/**
	 * One of 'cancel', 'replace', 'changeName'
	 *
	 * @var string
	 */
	protected $defaultConflictMode = 'changeName';

	/**
	 * @var array<string>
	 */
	protected $sourceTypes = array('array');

	/**
	 * @var string
	 */
	protected $targetType = 'string';

	/**
	 * Needs to take precedence over the available FileReferenceConverter
	 *
	 * @var integer
	 */
	protected $priority = 2;

	/**
	 * Actually convert from $source to $targetType, taking into account the fully
	 * built $convertedChildProperties and $configuration.
	 *
	 * @param string|integer $source
	 * @param string $targetType
	 * @param array $convertedChildProperties
	 * @param \TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface $configuration
	 * @throws \TYPO3\CMS\Extbase\Property\Exception
	 * @return \TYPO3\CMS\Extbase\Domain\Model\AbstractFileFolder
	 * @api
	 */
	public function convertFrom($source, $targetType, array $convertedChildProperties = array(), PropertyMappingConfigurationInterface $configuration = NULL) {

		if ($source['error'] !== \UPLOAD_ERR_OK) {
			switch ($source['error']) {
				case \UPLOAD_ERR_INI_SIZE:
				case \UPLOAD_ERR_FORM_SIZE:
				case \UPLOAD_ERR_PARTIAL:
					return new Error(Files::getUploadErrorMessage($source['error']), 1264440823);
				default:
					return new Error('An error occurred while uploading. Please try again or contact the administrator if the problem remains', 1340193849);
			}
		}

		try {
			$resource = $this->importUploadedResource($source, $configuration);
		} catch (\Exception $e) {
			return new Error($e->getMessage(), $e->getCode());
		}

		return $resource;
	}

	/**
	 * Imports a resource and respects configuration given for properties
	 *
	 * @param array $uploadInfo
	 * @param PropertyMappingConfigurationInterface $configuration
	 * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 * @throws TypeConverterException
	 * @throws ExistingTargetFileNameException
	 */
	protected function importUploadedResource(array $uploadInfo, PropertyMappingConfigurationInterface $configuration) {
		if (!GeneralUtility::verifyFilenameAgainstDenyPattern($uploadInfo['name'])) {
			throw new TypeConverterException('Uploading files with PHP file extensions is not allowed!', 1399312430);
		}

		$allowedFileExtensions = $configuration->getConfigurationValue('Helhum\\UploadExample\\Property\\TypeConverter\\UploadedFileReferenceConverter', self::CONFIGURATION_ALLOWED_FILE_EXTENSIONS);

		if ($allowedFileExtensions !== NULL) {
			$filePathInfo = PathUtility::pathinfo($uploadInfo['name']);
			if (!GeneralUtility::inList($allowedFileExtensions, strtolower($filePathInfo['extension']))) {
				throw new TypeConverterException('File extension is not allowed!', 1399312430);
			}
		}

		$uploadFolderId = $configuration->getConfigurationValue('Helhum\\UploadExample\\Property\\TypeConverter\\UploadedFileReferenceConverter', self::CONFIGURATION_UPLOAD_FOLDER) ?: $this->defaultUploadFolder;
		$conflictMode = $configuration->getConfigurationValue('Helhum\\UploadExample\\Property\\TypeConverter\\UploadedFileReferenceConverter', self::CONFIGURATION_UPLOAD_CONFLICT_MODE) ?: $this->defaultConflictMode;

		$uploadFolder = $this->resourceFactory->retrieveFileOrFolderObject($uploadFolderId);
		$uploadedFile =  $uploadFolder->addUploadedFile($uploadInfo, $conflictMode);

		$resourcePointer = isset($uploadInfo['submittedFile']['resourcePointer']) && strpos($uploadInfo['submittedFile']['resourcePointer'], 'file:') === FALSE
				? $this->hashService->validateAndStripHmac($uploadInfo['submittedFile']['resourcePointer'])
				: NULL;

		$fileReferenceModel = $this->createFileReferenceFromFalFileObject($uploadedFile, $resourcePointer);

		return $fileReferenceModel;
	}

	// public static function configureProperty ($argument, $configuration){
	// 	$uploadConfiguration = array(
	// 		UploadedFileReferenceConverter::CONFIGURATION_ALLOWED_FILE_EXTENSIONS => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
	// 		UploadedFileReferenceConverter::CONFIGURATION_UPLOAD_FOLDER => '1:/content/',
	// 	);
	// 	/** @var PropertyMappingConfiguration $newExampleConfiguration */
	// 	$newExampleConfiguration = $this->arguments[$argumentName]->getPropertyMappingConfiguration();
	// 	$newExampleConfiguration->forProperty('image')
	// 		->setTypeConverterOptions(
	// 			'Helhum\\UploadExample\\Property\\TypeConverter\\UploadedFileReferenceConverter',
	// 			$uploadConfiguration
	// 		);
	// 	$newExampleConfiguration->forProperty('imageCollection.0')
	// 		->setTypeConverterOptions(
	// 			'Helhum\\UploadExample\\Property\\TypeConverter\\UploadedFileReferenceConverter',
	// 			$uploadConfiguration
	// 		);
	// }

}