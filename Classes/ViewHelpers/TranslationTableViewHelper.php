<?php
namespace MONOGON\TranslationTools\ViewHelpers;

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

/**
 * TranslationController
 */
class TranslationTableViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 *
	 * @param array $translations
	 * @param \MONOGON\TranslationTools\Domain\Model\Dto\Demand $data
	 * @return type
	 */
	public function render ($translations = NULL, $demand = NULL){
		if (!$translations){
			return 'foobar';
		}
		$output = '';

		$output .= '<table class="translations table table-striped">';

		$languages = $demand->getLanguages();

		$output .= '<thead>';
		$output .= '<tr>';
		if (count($languages)){
			$output .= '<th>ID</th>';
			$output .= '<th>Source</th>';
		}
		foreach ($languages as $language){
			$output .= '<th>' . $language . '</th>';
		}

		$output .= '</tr>';
		$output .= '</thead>';



		$output .= '<tbody>';

		foreach ($translations as $translation){
			$output .= '<tr>';

			if (count($languages)){
				$firstTranslation = reset($translation);
				if ($firstTranslation){
					$output .= '<td title="' . $firstTranslation->getFile() . '">' . $firstTranslation->getId() . '</td>';
					$output .= '<td>' . $firstTranslation->getSource() . '</td>';
				} else {
					$output .= '<td></td>';
					$output .= '<td></td>';
				}
			}
			foreach ($languages as $language){
				if (isset($translation[$language])){
					$output .= '<td>' . $translation[$language]->getTarget() . '</td>';
				} else {
					$output .= '<td></td>';
				}
			}

			$output .= '</tr>';
		}

		$output .= '</tbody>';

		$output .= '</table>';

		return $output;
	}

}