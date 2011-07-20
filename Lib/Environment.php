<?php

class Environment {

	static $servers = array(
		'develop_server' => "example.net",
		'production_server' => "example.com",
		'develop_domains' => array("www", "monsat"),
		'production_domains' => array("www"),
	);

	static $constant = 'ROOT';

	static function initialize($settings = array()) {
		self::$servers = array_merge(self::$servers, $settings);
	}

	// Return True if Production Environment
	static function isProduction($host = false) {
		return self::_isProductionServer(self::getHostName($host)) && in_array(self::getEnvName($host), self::$servers['production_domains']);
	}

	// Return True if Test Environment
	static function isTest($host = false) {
		if (self::_isDevelopServer(self::getHostName($host)) && in_array(self::getEnvName($host), self::$servers['develop_domains'])) {
			return true;
		}
		return false;
	}
	// Return True if Develop Environment
	static function isDevelop($host = false) {
		if (!self::isProduction($host) && !self::isTest($host)) {
			return true;
		}
		return false;
	}

	// Return Environment Name (subdomains)
	public static function getEnvName($host = false) {
		$server = self::getServer($host);
		$hostName = self::getHostName($host);
		$name = str_replace($server, '', $hostName);
		return rtrim($name, '.');
	}

	// Return True if using ssl
	static function isSSL() {
		return env('HTTPS');
	}

	static function isCLI() {
		return env('argc') > 1;
	}

	static function isWeb() {
		return !self::isCLI();
	}

	static function getHostName($host = false) {
		if (!$host) {
			$host = constant(self::$constant);
		}

		return basename($host);
	}

	public static function getServer($host = false) {
		return self::_determineServer($host) ? self::$servers['production_server'] : self::$servers['develop_server'];
	}

	protected static function _determineServer($host) {
		$host = self::getHostName($host);
		if (self::_isProductionServer($host) && self::_isDevelopServer($host)) {
			throw new Exception('The host names for production server and develop server are ambiguous.');
		} elseif (self::_isProductionServer($host)) {
			return true;
		} elseif (self::_isDevelopServer($host)) {
			return false;
		}

		throw new Exception('No server can be detected. check your directory name.');
	}

	protected static function _isProductionServer($host) {
		return !!preg_match(sprintf('/%s$/', preg_quote(self::$servers['production_server'], '/')), $host);
	}

	protected static function _isDevelopServer($host) {
		return !!preg_match(sprintf('/%s$/', preg_quote(self::$servers['develop_server'], '/')), $host);
	}

}
