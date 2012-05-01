<?php

class Environment {

	public static $envs = array(
		'Production' => array(),
		'Staging' => array(),
		'Develop' => array(),
	);

	public static $constant = 'ROOT';

	public static function initialize(array $envs) {
		self::$envs = $envs;
	}

	// Return True if Current Environment
	public static function is($envKey = '', $host = false) {
		$host = self::getHostName($host);
		$hosts = (empty($envKey) || empty(self::$envs[$envKey])) ? self::$envs : self::$envs[$envKey];
		return in_array($host, $hosts);
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

	// deprecated (old version methods)
	public function isProduction($host = false) {
		return self::is('production', $host) || self::is('Production', $host);
	}
	public function isTest($host = false) {
		return self::is('test', $host) || self::is('Test', $host);
	}
	public function isDevelop($host = false) {
		return self::is('develop', $host) || self::is('Develop', $host);
	}

}
