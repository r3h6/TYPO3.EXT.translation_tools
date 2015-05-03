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

/**
 * ImportCsvService
 */
class ImportCsvService {

	public function __construct(){
		$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('translation_tools');
		$pearPath = $extensionPath . 'Resources/Private/Vendors/PEAR/';
		set_include_path(get_include_path() . PATH_SEPARATOR . $pearPath);
		require 'File/CSV.php';
	}

	public function importFile ($file, $autoEncode = TRUE){
		// $csv = new CsvFile($file);
		// $header = $csv->getHeader();



		$csv = new \File_CSV();
		$config = $csv->discoverFormat($file);

		$line = 0;

		while(($row = $csv->read($file, $config)) !== FALSE){
			if (!empty($row)){
				$header = $row;
				break;
			}
		}
		while(($row = $csv->read($file, $config)) !== FALSE){
			if (!empty($row)){
				$check = $row;
				break;
			}
		}

		if ($check[1] !== 'UTF-8 ÄäÖöÜüÊêÇç'){
			if ($autoEncode){
				$tmp = "$file.tmp";
				file_put_contents($tmp, mb_convert_encoding(file_get_contents($file), 'UTF-8'));
				$success = $this->importFile($tmp, FALSE);
				@unlink($tmp);
				return $success;
			}
			throw new \Exception("Invalid encoding!", 1);
		}


		//mb_detect_order('ASCII, ISO-8859-1, Windows-1251, Windows-1252, UTF-8');

		$encoding = mb_detect_encoding($check[1]);

		\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($header);
		\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($check);
		// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(mb_detect_order());
		// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($encoding);

		if ($check[1] !== 'UTF-8 ÄäÖöÜüÊêÇç'){
			\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(
				"**"
			);
		}

	}
}