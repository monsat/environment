<?php

App::import('Lib', 'Environment.Environment');
Environment::initialize(array(
	'develop_server' => 'example.jp',
));

class EnvironmentTestCase extends CakeTestCase {

	protected $_servers;
	protected $_constant;

	public function startTest() {
		$this->_server = Environment::$servers;
		Environment::initialize(array(
			'develop_server' => "example.jp",
			'production_server' => "example.com",
			'develop_domains' => array("www", "monsat", ""),
			'production_domains' => array("www", ""),
		));
		$this->_constant = Environment::$constant;
	}

	public function endTest() {
		Environment::$servers = $this->_server;
		Environment::$constant = $this->_constant;
	}

	public function testGetHostName() {
		$this->assertIdentical(Environment::getHostName(), basename(ROOT));
		$this->assertIdentical(Environment::getHostName('/virtual/username.example.com'), 'username.example.com');
	}

	public function testIsProduction() {
		$this->assertIdentical(Environment::isProduction('/virtual/www.example.com'), true);
		$this->assertIdentical(Environment::isProduction('/virtual/example.jp'), true);
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
		$this->assertIdentical(Environment::isDevelop('/virtual/dev.monsat.example.jp'), true);
		$this->assertIdentical(Environment::isDevelop('/virtual/monsat.example.jp'), false);
		$this->assertIdentical(Environment::isDevelop('/virtual/www.example.jp'), false);
		$this->assertIdentical(Environment::isDevelop('/virtual/dev.monsat.example.com'), true);
	}

	public function testGetEnvName() {
		$this->assertIdentical(Environment::getEnvName('/virtual/www.example.com'), 'www');
		$this->assertIdentical(Environment::getEnvName('/virtual/www.example.jp'), 'www');
		$this->assertIdentical(Environment::getEnvName('/virtual/example.jp'), '');
		$this->assertIdentical(Environment::getEnvName('/virtual/monsat.example.jp'), 'monsat');
		$this->assertIdentical(Environment::getEnvName('/virtual/dev.monsat.example.jp'), 'dev.monsat');
	}

	public function testGetServer() {
		$this->assertIdentical(Environment::getServer('/virtual/www.example.com'), 'example.com');
		$this->assertIdentical(Environment::getServer('/virtual/www.example.jp'), 'example.jp');
		$this->assertIdentical(Environment::getServer('/virtual/monsat.example.jp'), 'example.jp');
		$this->assertIdentical(Environment::getServer('/virtual/dev.monsat.example.jp'), 'example.jp');
	}

	function testSettings() {
		$settings = array(
			'develop_server' => 'example.com',
			'production_server' => 'example.jp',
			'develop_domains' => array('monsat.test'),
			'production_domains' => array('www'),
		);
		Environment::initialize($settings);

		$this->assertEqual(Environment::$servers, $settings);

		define('TEST_CONSTANT_FOR_ENVIRONMENT', 'monsat.test.example.com');
		Environment::$constant = 'TEST_CONSTANT_FOR_ENVIRONMENT';

		$this->assertIdentical(Environment::getEnvName(), 'monsat.test');
		$this->assertIdentical(Environment::getServer(), 'example.com');
	}

	function testIsCLIAndIsWeb() {
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

