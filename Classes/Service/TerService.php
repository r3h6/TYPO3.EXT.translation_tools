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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use MONOGON\TranslationTools\Configuration\ExtConf;

/**
 * TerService
 */
class TerService implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * [$ter description]
	 * @var TYPO3\CMS\Lang\Utility\Connection\Ter
	 * @inject
	 */
	protected $ter;

	/**
	 * [$repositoryHelper description]
	 * @var TYPO3\CMS\Extensionmanager\Utility\Repository\Helper
	 * @inject
	 */
	protected $repositoryHelper;

	/**
	 * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
	 * @inject
	 */
	protected $signalSlotDispatcher;

	public function fetchTranslationStatus ($extensionKey){
		$mirrorUrl = $this->getMirrorUrl($extensionKey);
		return $this->ter->fetchTranslationStatus($extensionKey, $mirrorUrl);
	}

	protected function getMirrorUrl($extensionKey) {
		$mirrorUrl = $this->repositoryHelper->getMirrors()->getMirrorUrl();
		$mirrorUrl = $this->emitPostProcessMirrorUrlSignal($extensionKey, $mirrorUrl);
		return $mirrorUrl;
	}

	/**
	 * Emits a signal after the mirror URL of an extension was fetched
	 *
	 * @param string $extensionKey
	 * @param string $mirrorUrl
	 * @return string Modified mirror url
	 */
	protected function emitPostProcessMirrorUrlSignal($extensionKey, $mirrorUrl) {
		$this->signalSlotDispatcher->dispatch(
			'TYPO3\\CMS\\Lang\\Service\\UpdateTranslationService',
			'postProcessMirrorUrl',
			array(
				$extensionKey,
				&$mirrorUrl,
			)
		);
		return $mirrorUrl;
	}
}