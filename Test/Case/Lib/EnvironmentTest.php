<?php

App::import('Lib', 'Environment.Environment');

class EnvironmentTestCase extends CakeTestCase {

	protected $_settings;
	protected $_constant;

	public function startTest() {
		$this->_settings = Environment::$settings;
		Environment::initialize(array(
			'develop' => array(
				'server' => 'dev.example.jp',
				'subdomains' => array(
					'www',
					'monsat',
					'',
				),
			),
			'test' => array(
				'server' => 'example.jp',
				'subdomains' => array(
					'www',
					'monsat',
					'',
				),
			),
			'production' => array(
				'server' => 'example.com',
				'subdomains' => array(
					'www',
					'',
				),
			),
		));
		$this->_constant = Environment::$constant;
	}

	public function endTest() {
		Environment::$settings = $this->_settings;
		Environment::$constant = $this->_constant;
	}

	public function testGetHostName() {
		$this->assertIdentical(Environment::getHostName(), basename(ROOT));
		$this->assertIdentical(Environment::getHostName('/virtual/username.example.com'), 'username.example.com');
	}

	public function testIsProduction() {
		$this->assertIdentical(Environment::isProduction('/virtual/www.example.com'), true);
		$this->assertIdentical(Environment::isProduction('/virtual/example.com'), true);
		$this->assertIdentical(Environment::isProduction('/virtual/monsat.example.com'), false);
		$this->assertIdentical(Environment::isProduction('/virtual/www.example.jp'), false);
	}

	public function testIsTest() {
		$this->assertIdentical(Environment::isTest('/virtual/www.example.jp'), true);
		$this->assertIdentical(Environment::isTest('/virtual/example.jp'), true);
		$this->assertIdentical(Environment::isTest('/virtual/monsat.example.jp'), true);
		$this->assertIdentical(Environment::isTest('/virtual/dev.monsat.example.jp'), false);
		$this->assertIdentical(Environment::isTest('/virtual/www.example.com'), false);
	}

	public function testIsDevelop() {
		$this->assertIdentical(Environment::isDevelop('/virtual/monsat.dev.example.jp'), true);
		$this->assertIdentical(Environment::isDevelop('/virtual/monsat.example.jp'), false);
		$this->assertIdentical(Environment::isDevelop('/virtual/www.example.jp'), false);
		$this->assertIdentical(Environment::isDevelop('/virtual/dev.example.jp'), true);
	}

	public function testGetEnvName() {
		$this->assertIdentical(Environment::getEnvName('/virtual/www.example.com'), 'www');
		$this->assertIdentical(Environment::getEnvName('/virtual/www.example.jp'), 'www');
		$this->assertIdentical(Environment::getEnvName('/virtual/example.jp'), '');
		$this->assertIdentical(Environment::getEnvName('/virtual/monsat.example.jp'), 'monsat');
		$this->assertIdentical(Environment::getEnvName('/virtual/monsat.dev.example.jp'), 'monsat');
	}

	public function testGetServer() {
		$this->assertIdentical(Environment::getServer('/virtual/www.example.com'), 'example.com');
		$this->assertIdentical(Environment::getServer('/virtual/www.example.jp'), 'example.jp');
		$this->assertIdentical(Environment::getServer('/virtual/monsat.example.jp'), 'example.jp');
		$this->assertIdentical(Environment::getServer('/virtual/monsat.dev.example.jp'), 'dev.example.jp');
	}

	public function testError() {
		$settings = array(
			'develop' => array(
				'server' => 'example.com',
				'subdomains' => array(
					'www',
				),
			),
			'test' => array(
				'server' => 'example.com',
				'subdomains' => array(
					null,
				),
			),
			'production' => array(
				'server' => 'example.com',
				'subdomains' => array(
					'www',
					null,
				),
			),
		);
		Environment::initialize($settings);
		try {
			Environment::getServer('/virtual/www.example.com');
			$this->fail('ambiguous');
		} catch (Exception $e) {
			$this->pass('ambiguous');
		}
		try {
			Environment::getServer('/virtual/www.example.jp');
			$this->fail('not detected');
		} catch (Exception $e) {
			$this->pass('not detected');
		}
	}

	public function testSettings() {
		$settings = array(
			'develop' => array(
				'server' => 'dev.example.com',
				'subdomains' => array(
					'monsat.test',
				),
			),
			'test' => array(
				'server' => 'example.com',
				'subdomains' => array(
					'monsat.test',
				),
			),
			'production' => array(
				'server' => 'example.jp',
				'subdomains' => array(
					'www',
				),
			),
		);
		Environment::initialize($settings);

		$this->assertEqual(Environment::$settings, $settings);

		define('TEST_CONSTANT_FOR_ENVIRONMENT', 'monsat.test.example.com');
		Environment::$constant = 'TEST_CONSTANT_FOR_ENVIRONMENT';

		$this->assertIdentical(Environment::isTest(), true);
		$this->assertIdentical(Environment::getEnvName(), 'monsat.test');
		$this->assertIdentical(Environment::getServer(), 'example.com');
	}

	public function testIsCLIAndIsWeb() {
		$backup = $_ENV;

		$_ENV['argc'] = 2;
		$this->assertTrue(Environment::isCLI());
		$this->assertFalse(Environment::isWeb());

		$_ENV['argc'] = 1;
		$this->assertFalse(Environment::isCLI());
		$this->assertTrue(Environment::isWeb());

		unset($_ENV['argc']);
		$this->assertFalse(Environment::isCLI());
		$this->assertTrue(Environment::isWeb());

		$_ENV = $backup;
	}

}

