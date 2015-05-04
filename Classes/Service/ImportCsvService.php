<?php
namespace MONOGON\TranslationTools\Service;


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

use \Keboola\Csv\CsvFile;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
/**
 * ImportCsvService
 */
class ImportCsvService {

	/**
	 * [$fileRepository description]
	 *
	 * @var \MONOGON\TranslationTools\Domain\Repository\FileRepository
	 * @inject
	 */
	protected $fileRepository = NULL;

	public function __construct(){
		$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('translation_tools');
		$pearPath = $extensionPath . 'Resources/Private/Vendors/PEAR/';
		set_include_path(get_include_path() . PATH_SEPARATOR . $pearPath);
		require 'File/CSV.php';
	}

	public function importFile ($file, $autoEncode = TRUE){
		$csv = new \File_CSV();
		$config = $csv->discoverFormat($file);

		$line = 0;

		while(($row = $csv->read($file, $config)) !== FALSE){
			if (!empty($row)){
				$line++;
				$header = $row;
				break;
			}
		}
		while(($row = $csv->read($file, $config)) !== FALSE){
			if (!empty($row)){
				$line++;
				$check = $row;
				break;
			}
		}

		if ($check[1] !== 'UTF-8 ÄäÖöÜüÊêÇç'){
			if ($autoEncode){
				$tmpFile = GeneralUtility::tempnam('import_', '.csv');
				$error = GeneralUtility::writeFileToTypo3tempDir($tmpFile, mb_convert_encoding(file_get_contents($file), 'UTF-8'));
				if ($error){
					throw new \Exception($error, 1430771681);
				}
				$success = $this->importFile($tmpFile, FALSE);
				GeneralUtility::unlink_tempfile($tmpFile);
				return $success;
			}
			throw new \Exception("Invalid encoding!", 1430771687);
		}

		$locallangFiles = array();

		while(($row = $csv->read($file, $config)) !== FALSE){
			if (!empty($row)){
				$line++;

				$col1 = GeneralUtility::trimExplode(':', $row[0]);
				$id = array_pop($col1);
				$file = join(':', $col1);
				$source = $row[1];



				for ($i = 2; $i < count($header); $i++){
					try {
						$language = $header[$i];
						$target = $row[$i];

						if (empty($target)){
							continue;
						}

						$translation = GeneralUtility::makeInstance('MONOGON\\TranslationTools\\Domain\\Model\\Translation');
						$translation->setId($id)
							->setSource($source)
							->setTarget($target)
							->setSourceFile($file)
							->setTargetLanguage($language);

						$targetFile = $translation->getTargetFile();
						$sha1 = sha1($targetFile);
						if (!isset($locallangFiles[$sha1])){
							$locallangFiles[$sha1] = $this->fileRepository->findByIdentifier($targetFile);
						}
						$locallangFiles[$sha1]->addTranslation($translation);


					} catch (\Exception $exception){

					}
				}
				break;
			}
		}

		\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($locallangFiles);

	}

}