<?php
/**
 * Created V/17/11/2023
 * Updated D/09/03/2025
 *
 * Copyright 2008-2025 | Fabrice Creuzot (luigifab) <code~luigifab~fr>
 * https://github.com/luigifab/dolibarr-apijs
 *
 * This program is free software, you can redistribute it or modify
 * it under the terms of the GNU General Public License (GPL) as published
 * by the free software foundation, either version 2 of the license, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but without any warranty, without even the implied warranty of
 * merchantability or fitness for a particular purpose. See the
 * GNU General Public License (GPL) for more details.
 */

require_once(DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php');

class modApijs extends DolibarrModules {

	public function __construct($db) {

		parent::__construct($db);

		// @see https://github.com/Dolibarr/dolibarr/blob/develop/htdocs/core/modules/DolibarrModules.class.php
		$this->numero                = 491303;
		$this->editor_name           = 'luigifab';
		$this->editor_url            = 'https://github.com/luigifab/dolibarr-apijs';
		$this->family                = 'base';
		$this->name                  = 'Apijs';
		$this->module_parts          = ['hooks' => ['main']];
		$this->version               = '6.9.7';
		$this->description           = 'JavaScript pop-ups and slideshow for Dolibarr.';
		$this->const_name            = 'MAIN_MODULE_'.strtoupper($this->name); // not mb_strtoupper
		$this->langfiles             = ['apijs@apijs'];
		$this->phpmin                = [7,2];
		$this->need_dolibarr_version = [5,0];
		$this->url_last_version      = 'https://raw.githubusercontent.com/luigifab/dolibarr-apijs/master/version.txt';
		$this->hidden                = false;

		global $conf, $langs;
		if (!isset($conf->apijs->enabled)) {
			$conf->apijs = new stdClass();
			$conf->apijs->enabled = 0;
		}
		else if (!empty($conf->apijs->enabled)) {
			$langs->loadLangs(['apijs@apijs']);
			$this->description = implode(' ', [
				'<button type="button" onclick="apijs.dialog.dialogInformation(\''.$langs->trans('ApijsExampleTitle1').'\',\''.addslashes($langs->trans('ApijsExampleText1')).'\');">'.$langs->trans('ApijsExample', 1).'</button>',
				'<button type="button" onclick="apijs.dialog.dialogInformation(\''.$langs->trans('ApijsExampleTitle2').'\',\'[p]'.addslashes($langs->trans('ApijsExampleText2')).'[/p][p]'.addslashes($langs->trans('ApijsExampleText3', '[a href=\'https://www.luigifab.fr/\' class=\'popup\']www.luigifab.fr[/a]')).'[/p]\');">'.$langs->trans('ApijsExample', 2).'</button>',
			]).' '.$this->description;
		}
	}
}