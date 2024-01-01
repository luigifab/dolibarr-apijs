<?php
/**
 * Created V/17/11/2023
 * Updated D/24/12/2023
 *
 * Copyright 2008-2024 | Fabrice Creuzot (luigifab) <code~luigifab~fr>
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

class ActionsApijs {

	public function addHtmlHeader($parameters, &$object, &$action, $hookmanager) {

		global $conf, $langs;
		if (!empty($conf->apijs->enabled)) {

			$file  = ($langs->trans('DIRECTION') == 'rtl') ? 'css/apijs-screen-rtl.min.css' : 'css/apijs-screen.min.css';
			$html  = '<link rel="stylesheet" media="screen" type="text/css" href="'.$this->getUrl($conf, $file).'">'."\n";
			$html .= '<link rel="stylesheet" media="print"  type="text/css" href="'.$this->getUrl($conf, 'css/apijs-print.min.css').'">'."\n";
			$html .= '<link rel="prefetch" type="font/woff2" as="font" href="'.$this->getLru($conf, 'fonts/apijs/fontello.woff2').'">'."\n";
			$html .= '<script nonce="'.getNonce().'" src="'.$this->getUrl($conf, 'js/apijs.min.js').'"></script>'."\n";

			if (empty($hookmanager->resPrint))
				$hookmanager->resPrint = $html;
			else
				$hookmanager->resPrint .= $html;
		}

		return 0;
	}

	public function printMainArea($parameters, &$object, &$action, $hookmanager) {

		global $conf, $langs;
		if (!empty($conf->apijs->enabled)) {

			try {
				ob_start();
				require_once(__DIR__.'/browser.phtml');
				$html = ob_get_clean();
			}
			catch (Throwable $t) {
				$html = '<script nonce="'.getNonce().'">alert("Apijs: '.addslashes($t->getMessage()).'");</script>'."\n";
			}

			if (empty($hookmanager->resPrint))
				$hookmanager->resPrint = $html;
			else
				$hookmanager->resPrint .= $html;
		}

		return 0;
	}

	protected function getUrl($conf, $file) {
		return dol_buildpath('custom/apijs/'.$file, 1).'?v=696';
	}

	protected function getLru($conf, $file) {
		return dol_buildpath('custom/apijs/'.$file, 1).'?a3ab5acff3';
	}
}