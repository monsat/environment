<?php

class Environment {
	static $servers = array(
		'develop_server' => "example.net",
		'production_server' => "example.com",
		'develop_domains' => array("www", "monsat"),
		'production_domains' => array("www"),
	);
	
	static function initialize($settings = array()) {
		self::$servers = array_merge(self::$servers, $settings);
	}
	// Return True if Production Environment
	static function isProduction($host = false) {
		$host = self::getHostName($host);
		if (self::_isProductionServer($host) && in_array(self::getEnvName($host), self::$servers['production_domains'])) {
			return true;
		}
		return false;
	}
	// Return True if Test Environment
	static function isTest($host = false) {
		$host = self::getHostName($host);
		if (self::_isDevelopServer($host) && in_array(self::getEnvName($host), self::$servers['develop_domains'])) {
			return true;
		}
		return false;
	}
	// Return True if Develop Environment
	static function isDevelop($host = false) {
		$host = self::getHostName($host);
		if (!self::isProduction($host) && !self::isTest($host)) {
			return true;
		}
		return false;
	}
	// Return Environment Name (subdomains)
	static function getEnvName($host = false) {
		$host = self::getHostName($host);
		if (self::_isProductionServer($host)) {
			$len = self::_strposServer($host) - 1;
		} else if (self::_isDevelopServer($host)) {
			$len = self::_strposServer($host, false) - 1;
		} else {
			return false;
		}
		return substr($host, 0, $len);
	}
	// Return True if using ssl
	static function isSSL() {
		return env('HTTPS');
	}
	function getHostName($host = ROOT) {
		return basename($host);
	}
	// Internal functions
	function _isProductionServer($host) {
		return substr($host, self::_strposServer($host)) === self::$servers['production_server'];
	}
	function _isDevelopServer($host) {
		return substr($host, self::_strposServer($host, false)) === self::$servers['develop_server'];
	}
	function _strposServer($host, $is_production = true) {
		$srv = $is_production ? self::$servers['production_server'] : self::$servers['develop_server'];
		return strlen($host) - strlen($srv);
	}
}
