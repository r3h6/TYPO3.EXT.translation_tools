<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}


## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder

if (TYPO3_MODE === 'BE') {

	/**
	 * Registers a Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'MONOGON.' . $_EXTKEY,
		'user',	 // Make module a submodule of 'user'
		'tools',	// Submodule key
		'',						// Position
		array(
			'Translation' => 'list, update',

		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_tools.xlf',
		)
	);

	/**
	 * Registers a Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'MONOGON.' . $_EXTKEY,
		'tools',	 // Make module a submodule of 'tools'
		'admin',	// Submodule key
		'',						// Position
		array(
			'Translation' => 'list',

		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_admin.xlf',
		)
	);

}
