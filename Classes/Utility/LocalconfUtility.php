<?php
namespace MONOGON\TranslationTools\Utility;


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
use MONOGON\TranslationTools\Configuration\ExtConf;

/**
 * LocalconfUtility
 */
class LocalconfUtility {

	const OVERWRITE_TOKEN_START = '## OVERWRITE LANGUAGE TOKEN START';
	const OVERWRITE_TOKEN_END = '## OVERWRITE LANGUAGE TOKEN END';

	// $GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride'][’fr’][’path/to/originalTranslationFile.xlf’][] = ’other/path/to/fr.otherTranslationFile.xlf’;


	public static function update (){
		$l10nDir = GeneralUtility::getFileAbsFileName(ExtConf::get('storageFolder'));

		if (is_dir($l10nDir)) {
			$files = GeneralUtility::getAllFilesAndFoldersInPath(array(), $l10nDir, 'xml,xlf', FALSE, 99, '');

			$overwrite = self::OVERWRITE_TOKEN_START . "\n";

			foreach ($files as $file){

				$language = FileUtility::extractLanguageFromPath($file);

				$path = FileUtility::convertToOriginalPath($file);
				$overridePath = FileUtility::makeExtPath($file);
				if ($language){
					$overwrite .= "\$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['$language']['$path'][] = '$overridePath';\n";
				} else {
					$overwrite .= "\$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['$path'][] = '$overridePath';\n";
				}


			}
			$overwrite .= self::OVERWRITE_TOKEN_END . "\n";

			$localconfPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('l10n_overwrite') . 'ext_localconf.php';

			if (is_file($localconfPath)){

				$content = file_get_contents($localconfPath);

				$pattern = '/^' . self::OVERWRITE_TOKEN_START . '$.*^' . self::OVERWRITE_TOKEN_END .'$/sim';

				$pattern = '/' . self::OVERWRITE_TOKEN_START . '.*' . self::OVERWRITE_TOKEN_END . '/sim';
				$content = preg_replace($pattern, $overwrite, $content);

				// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($pattern);
				// // ^## OVERWRITE LANGUAGE TOKEN START$.*^## OVERWRITE LANGUAGE TOKEN END
				// \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($content);

				GeneralUtility::writeFile($localconfPath, $content);

			}
		}
	}
}