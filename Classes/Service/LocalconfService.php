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


use Exception;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use MONOGON\TranslationTools\Utility\FileUtility;

/**
 * LocalconfService
 */
class LocalconfService implements \TYPO3\CMS\Core\SingletonInterface {

	const OVERWRITE_TOKEN = '## OVERWRITE_TOKEN';

	public function update ($extensionKey){
		$l10nDir = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extensionKey);
		if (!is_dir($l10nDir)) {
			throw new Exception("Directory '$l10nDir' doesn't exist.", 1430655975);

		}

		$content = "<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

";

		$files = GeneralUtility::getAllFilesAndFoldersInPath(array(), $l10nDir, 'xml,xlf', FALSE, 99, '');
		foreach ($files as $file){
			$language = FileUtility::extractLanguageFromPath($file);
			$path = FileUtility::convertToOriginalPath($file);
			$overridePath = FileUtility::makeTypo3Path($file);
			if ($language){
				$content .= "\$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['$language']['$path'][] = '$overridePath';\n";
			} else {
				$content .= "\$GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']['$path'][] = '$overridePath';\n";
			}
		}

		$content .= self::OVERWRITE_TOKEN;

		$localconfPath = $l10nDir . 'ext_localconf.php';

		if (is_file($localconfPath)){
			$token = '/^' . self::OVERWRITE_TOKEN . '(.*)/mis';
			if (preg_match_all($token, file_get_contents($localconfPath), $matches)){
				$content .= rtrim($matches[1][0]);
			}
		}

		GeneralUtility::writeFile($localconfPath, $content);
	}
}