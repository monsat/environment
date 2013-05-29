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

	public static function __callStatic($method, $arguments) {
		if (preg_match('/^is(.+)$/', $method, $matches)) {
			$env = $matches[1];
			if (!isset(self::$envs[$env])) {
				$env = strtolower($env{0} . substr($env, 1));
				if (!isset(self::$envs[$env])) {
					throw new BadMethodCallException("The $env is not defined for Environment");
				}
			}
			array_unshift($arguments, $env);
			return call_user_func_array(__CLASS__ . '::is', $arguments);
		}
		throw new Exception('Called undefined class method ' . __CLASS__ . "::$method()");
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

}
