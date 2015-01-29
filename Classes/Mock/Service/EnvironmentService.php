<?php
namespace MONOGON\TranslationTools\Mock\Service;

class EnvironmentService implements \TYPO3\CMS\Core\SingletonInterface {

	public function isEnvironmentInFrontendMode (){
		return TRUE;
	}
}