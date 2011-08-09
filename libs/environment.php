<?php

class Environment {

	public static $settings = array(
		'develop' => array(
			'server' => 'dev.example.net',
			'subdomains' => array(
				'monsat',
			),
		),
		'test' => array(
			'server' => 'example.net',
			'subdomains' => array(
				'monsat',
			),
		),
		'production' => array(
			'server' => 'example.com',
			'subdomains' => array(
				null,
				'www',
			),
		),
	);

	public static $constant = 'ROOT';
	protected static $_regexes = array();

	public static function initialize($settings = array()) {
		self::$settings = array_merge(self::$settings, $settings);
		self::$_regexes = array();
	}

	// Return True if Production Environment
	public static function isProduction($host = false) {
		return self::_match($host, 'production');
	}

	// Return True if Test Environment
	public static function isTest($host = false) {
		return self::_match($host, 'test');
	}

	// Return True if Develop Environment
	public static function isDevelop($host = false) {
		return self::_match($host, 'develop');
	}

	protected static function _match($host, $type) {
		$host = self::getHostName($host);

		if (!isset(self::$_regexes[$type])) {
			extract(self::$settings[$type]);

			$hasEmpty = false;
			foreach ($subdomains as $i => $subdomain) {
				if (empty($subdomain)) {
					$hasEmpty = true;
					unset($subdomains[$i]);
				}
			}
			if (!empty($subdomains)) {
				$subdomains = array_map('preg_quote', $subdomains, array_fill(0, count($subdomains), '/'));
				$subdomainsRegex = sprintf("(%s)\\.", implode('|', $subdomains));
				if ($hasEmpty) {
					$subdomainsRegex = "($subdomainsRegex)?";
				}
			} else {
				$subdomainsRegex = '';
			}

			$serverRegex = preg_quote($server, '/');
			self::$_regexes[$type] = sprintf('/^%s%s$/', $subdomainsRegex, $serverRegex);
		}

		return !!preg_match(self::$_regexes[$type], $host);

	}

	// Return Environment Name (subdomains)
	public static function getEnvName($host = false) {
		$server = self::getServer($host);
		$hostName = self::getHostName($host);
		$name = str_replace($server, '', $hostName);
		return rtrim($name, '.');
	}

	// Return True if using ssl
	public static function isSSL() {
		return env('HTTPS');
	}

	public static function isCLI() {
		return env('argc') > 1;
	}

	public static function isWeb() {
		return !self::isCLI();
	}

	public static function getHostName($host = false) {
		if (!$host) {
			$host = constant(self::$constant);
		}

		return basename($host);
	}

	public static function getServer($host = false) {
		$type = self::_determineServer($host);
		return self::$settings[$type]['server'];
	}

	protected static function _determineServer($host) {

		$results = array(
			'production' => (int)self::isProduction($host),
			'test' => (int)self::isTest($host),
			'develop' => (int)self::isDevelop($host),
		);

		$counts = array_count_values($results);
		if (!isset($counts[1])) {
			throw new Exception('No server can be detected. check your directory name.');
		} elseif ($counts[1] == 1) {
			return array_search(1, $results);
		}

		$results = array_filter($results);
		throw new Exception(sprintf('The host names for %s server are ambiguous.', implode(', ', array_keys($results))));

	}

}
