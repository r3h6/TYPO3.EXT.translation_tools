<?php

$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('translation_tools');
$vendorsPath = $extensionPath . 'Resources/Private/Vendors/';

return array(
	'Keboola\\Csv\\CsvFile' => $vendorsPath . 'Keboola/Csv/CsvFile.php',
	'Keboola\\Csv\\Exception' => $vendorsPath . 'Keboola/Csv/Exception.php',
	'Keboola\\Csv\\InvalidArgumentException' => $vendorsPath . 'Keboola/Csv/InvalidArgumentException.php',
);