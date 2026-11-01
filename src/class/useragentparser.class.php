<?php
/**
 * Copyright 2013-2026 | Jesse G. Donat <donatj~gmail~com>
 * https://github.com/donatj/PhpUserAgent
 *
 * Copyright 2019-2026 | Fabrice Creuzot (luigifab) <code~luigifab~fr>
 * https://gist.github.com/luigifab/4cb373e75f3cd2f342ca6bc25504b149 (1.12.0-fork1)
 *
 * Parses a user agent string into its important parts
 * Fork checked by Claude from the original
 * Licensed under the MIT License
 */

class Useragentparser {

	public function parse($userAgent = null) {

		if ($userAgent === null) $userAgent = getenv('HTTP_USER_AGENT');

		$platform = null;
		$browser  = null;
		$version  = null;
		$empty    = ['platform' => $platform, 'browser' => $browser, 'version' => $version];
		$priority = ['Xbox One','Xbox','Windows Phone','Tizen','Android','FreeBSD','NetBSD','OpenBSD','CrOS','Fuchsia','X11','Sailfish'];

		if (empty($userAgent)) return $empty;

		if (preg_match('/\((.*?)\)/m', $userAgent, $parentMatches)) {

			preg_match_all('/(?P<platform>BB\d+;|Android|Adr|Symbian|Sailfish|CrOS|Fuchsia|Tizen|iPhone|iPad|iPod|Linux|(?:Open|Net|Free)BSD|Macintosh|Windows(?:\ Phone)?|Silk|linux-gnu|BlackBerry|PlayBook|X11|(?:New\ )?Nintendo\ (?:WiiU?|3?DS|Switch)|Xbox(?:\ One)?) (?:\ [^;]*)? (?:;|$)/imx',
				$parentMatches[1], $result);
			$result['platform'] = array_unique($result['platform']);
			if (count($result['platform']) > 1) {
				if (!empty($keys = array_intersect($priority, $result['platform'])))
					$platform = reset($keys);
				else
					$platform = $result['platform'][0];
			}
			else if (isset($result['platform'][0])) {
				$platform = $result['platform'][0];
			}
		}

		if ($platform == 'linux-gnu' || $platform == 'X11')
			$platform = 'Linux';
		else if ($platform == 'CrOS')
			$platform = 'Chrome OS';
		else if ($platform == 'Adr')
			$platform = 'Android';
		else if (($platform === null) && preg_match_all('%(?P<platform>Android)[:/ ]%ix', $userAgent, $result))
			$platform = $result['platform'][0];








		preg_match_all( // ["browser" => ["Firefox"...], "version" => ["45.0"...]]
			'%(?P<prev>.)?(?P<browser>Camino|Kindle(\ Fire)?|Firefox|Thunderbird|Iceweasel|IceCat|Safari|MSIE|Trident|AppleWebKit|TizenBrowser|(?:Headless)?Chrome|YaBrowser|Vivaldi|IEMobile|Opera|OPR|Silk|Midori|(?-i:Edge)|EdgA?|CriOS|UCBrowser|Puffin|OculusBrowser|SamsungBrowser|SailfishBrowser|XiaoMi/MiuiBrowser|YaApp_Android|Whale|Applebot|Facebot|Googlebot|YandexBot|bingbot|Lynx|Version|Valve\ Steam\ Tenfoot|Mastodon|NintendoBrowser|PLAYSTATION\ (?:\d|Vita)+)\)?;?(?:[:/ ](?P<version>[\dA-Z.]+)|/[A-Z]*)%ix',
			$userAgent, $result);


		// If nothing matched, check for simple bot patterns
		if (!isset($result['browser'][0], $result['version'][0])) {
			// Try: bot/version with contact info "site.com botname/1.0 (+contact)"
			if (preg_match('%(?P<browser>[A-Z\d\-]+)[/ ](?P<version>[\dA-Z.]+)\s*\(\+(?:https?:|[a-z\d._-]+@[a-z\d._-]+)%ix', $userAgent, $gr)) {
				return ['platform' => $platform, 'browser' => $gr['browser'], 'version' => $gr['version']];
			}
			// Try: simple bot at start "BotName/version" (not Mozilla)
			if (preg_match('%^(?!Mozilla)(?P<browser>[A-Z\d\-]+)([/ :](?P<version>[\dA-Z.]+))?%ix', $userAgent, $gr)) {
				return ['platform' => $platform, 'browser' => $gr['browser'], 'version' => empty($gr['version']) ? null : $gr['version']];
			}
		}

		if ((empty($result['browser'][0]) || ($result['prev'][0] !== '')) &&
		    preg_match('%[(;]\s*(?P<browser>[^(/;]+)(?:[:/ ]v?(?P<version>[\dA-Z.]+)[^;)\s]*)?;?(?:\s*robot;)?\s*\+(?:https?:|[a-z\d._-]+@[a-z\d._-]+)%ix', $userAgent, $bot_result)) {
			return ['platform' => $platform, 'browser' => trim($bot_result['browser']), 'version' => empty($bot_result['version']) ? null : $bot_result['version']];
		}

		if (!isset($result['browser'][0], $result['version'][0])) {
			return ['platform' => $platform, 'browser' => null, 'version' => null];
		}


		if (preg_match('/rv:(?P<version>[\dA-Z.]+)/i', $userAgent, $rv_result)) {
			$rv_result = $rv_result['version'];
		}

		$browser = $result['browser'][0];
		$version = $result['version'][0];

		$lowerBrowser = array_map('strtolower', $result['browser']);
		$key = 0;
		$val = '';

		if ($this->findT($lowerBrowser, ['OPR' => 'Opera', 'Facebot' => 'iMessageBot', 'UCBrowser' => 'UC Browser', 'YaBrowser' => 'Yandex', 'YaApp_Android' => 'Yandex', 'Iceweasel' => 'Firefox', 'Icecat' => 'Firefox', 'CriOS' => 'Chrome', 'Edg' => 'Edge', 'EdgA' => 'Edge', 'XiaoMi/MiuiBrowser' => 'MiuiBrowser'], $key, $browser)) {
			$version = is_numeric(substr($result['version'][$key], 0, 1)) ? $result['version'][$key] : null;
		}
		else if ($this->find($lowerBrowser, 'Playstation Vita', $key, $platform)) {
			$platform = 'PlayStation Vita';
			$browser  = 'Browser';
		}
		else if ($this->find($lowerBrowser, ['Kindle Fire', 'Silk'], $key, $val)) {
			$browser  = $val == 'Silk' ? 'Silk' : 'Kindle';
			$platform = 'Kindle Fire';
			if (!($version = $result['version'][$key]) || !is_numeric($version[0])) {
				$version = $result['version'][array_search('Version', $result['browser'])];
			}
		}
		else if ($this->find($lowerBrowser, 'NintendoBrowser', $key) || $platform == 'Nintendo 3DS') {
			$browser = 'NintendoBrowser';
			$version = $result['version'][$key];
		}
		else if ($this->find($lowerBrowser, 'Kindle', $key, $platform)) {
			$browser = $result['browser'][$key];
			$version = $result['version'][$key];
		}
		else if ($this->find($lowerBrowser, 'Opera', $key, $browser)) {
			$this->find($lowerBrowser, 'Version', $key);
			$version = $result['version'][$key];
		}
		else if ($this->find($lowerBrowser, 'Puffin', $key, $browser)) {
			$version = $result['version'][$key];
			if (strlen($version) > 3) {
				$part = substr($version, -2);
				if (ctype_upper($part)) {
					$version = substr($version, 0, -2);
					$flags   = ['IP' => 'iPhone','IT' => 'iPad','AP' => 'Android','AT' => 'Android','WP' => 'Windows Phone','WT' => 'Windows'];
					if (is_string($part) && array_key_exists($part, $flags)) {
						$platform = $flags[$part];
					}
				}
			}
		}
		else if ($this->find($lowerBrowser, ['Googlebot', 'Applebot', 'IEMobile', 'Edge', 'Midori', 'Whale', 'Vivaldi', 'OculusBrowser', 'SamsungBrowser', 'Valve Steam Tenfoot', 'Chrome', 'HeadlessChrome', 'SailfishBrowser'], $key, $browser)) {
			$version = $result['version'][$key];
		}
		else if ($rv_result && $this->find($lowerBrowser, 'Trident')) {
			$browser = 'MSIE';
			$version = $rv_result;
		}
		else if ($browser == 'AppleWebKit') {
			if ($platform == 'Android') {
				$browser = 'Android Browser';
			}
			else if (strncmp((string) $platform, 'BB', 2) === 0) {
				$browser  = 'BlackBerry Browser';
				$platform = 'BlackBerry';
			}
			else if ($platform == 'BlackBerry' || $platform == 'PlayBook') {
				$browser = 'BlackBerry Browser';
			}
			else if ($this->find($lowerBrowser, 'Safari', $key, $browser) || $this->find($lowerBrowser, 'TizenBrowser', $key, $browser)) {
				$version = $result['version'][$key];
			}
			else if (count($result['browser'])) {
				$key     = count($result['browser']) - 1;
				$browser = $result['browser'][$key];
				$version = $result['version'][$key];
			}
			if ($this->find($lowerBrowser, 'Version', $key)) {
				$version = $result['version'][$key];
			}
		}
		else if (!empty($pKey = preg_grep('/playstation \d/i', $result['browser']))) {
			$pKey     = reset($pKey);
			$platform = 'PlayStation '.preg_replace('/\D/', '', $pKey);
			$browser  = 'NetFront';
		}

		return ['platform' => $platform, 'browser' => $browser, 'version' => $version];
	}

	private function find($lowerBrowser, $search, &$key = null, &$value = null) {

		$search = (array) $search;

		foreach ($search as $val) {
			$xkey = array_search(strtolower($val), $lowerBrowser);
			if ($xkey !== false) {
				$value = $val;
				$key   = $xkey;
				return true;
			}
		}

		return false;
	}

	private function findT($lowerBrowser, array $search, &$key = null, &$value = null) {

		$value2 = null;

		if ($this->find($lowerBrowser, array_keys($search), $key, $value2)) {
			$value = $search[$value2];
			return true;
		}

		return false;
	}
}