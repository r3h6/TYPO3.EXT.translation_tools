<?php
namespace MONOGON\TranslationTools\View\Translation;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 R3 H6 <r3h6@outlook.com>
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

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 *
 */
class ListCsv extends \TYPO3\CMS\Extbase\Mvc\View\AbstractView {

	protected $delimeter = ';';

	public function initializeView (){

	}

	public function render (){
		$translations = $this->variables['translations'];
		$demand = $this->variables['demand'];
		$languages = $demand->getLanguages();

		// $tempFile = GeneralUtility::tempnam('translations', '.csv');
		// $fp = fopen($tempFile, 'w');
		$fp = fopen('php://output', 'w');

		// BOM (http://www.skoumal.net/cs/node/24)
		fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

		// Header
		$fields = array();
		$fields[] ='ID';
		foreach ($languages as $language) {
			$fields[] =$language;
		}
		fputcsv($fp, $fields, $this->delimeter);


		fputcsv($fp, array(
			'DO NOT DELETE THIS LINE!',
			'UTF-8 ÄäÖöÜüÊêÇç'
		), $this->delimeter);



		// Body
		foreach ($translations as $translationGroup){
			$fields = array();
			$fields[] = $translationGroup['file'] . ':' . $translationGroup['id'];
			// $fields[] = $translationGroup['id'];
			foreach ($languages as $language){
				$fields[] =$translationGroup[$language]->getTarget();
			}
			fputcsv($fp, $fields, $this->delimeter);
		}

		fclose($fp);

		// Response
		$fileName = 'Translation_' . date('d-m-Y-H-i') . '.csv';
		$headers = array(
			'Pragma' => 'public',
			'Expires' => '0',
			'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
			'Content-Description' => 'File Transfer',
			'Content-type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
			'Content-Transfer-Encoding' => 'binary',
		);

		$response = $this->controllerContext->getResponse();

		foreach ($headers as $name => $value){
			$response->setHeader($name, $value);
		}

		// $content = file_get_contents($tempFile);
		// // GeneralUtility::unlink_tempfile($tempFile);
		// return $content;
	}

	// protected function convertCharset ($value){
	// 	return $value;
	// 	return iconv('UTF-8', 'Windows-1252', $value);
	// }
}