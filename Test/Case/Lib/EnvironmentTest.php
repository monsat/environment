<?php

App::import('Lib', 'Environment.Environment');

class EnvironmentTest extends CakeTestCase {

	protected $_settings;
	protected $_constant;

	public function setUp() {
		$this->_settings = Environment::$settings;
		Environment::initialize(array(
			'Develop' => array(
				'www.dev.example.jp',
				'monsat.dev.example.jp',
				'dev.example.jp',
			),
			'Staging' => array(
				'www.example.jp',
				'dev.www.example.jp',
				'monsat.example.jp',
				'example.jp',
			),
			'Production' => array(
				'www.example.com',
				'example.com',
			),
		));
		$this->_constant = Environment::$constant;
	}

	public function tearDown() {
		Environment::$settings = $this->_settings;
		Environment::$constant = $this->_constant;
	}

	public function testGetHostName() {
		$this->assertSame(Environment::getHostName(), basename(ROOT));
		$this->assertSame(Environment::getHostName('/virtual/username.example.com'), 'username.example.com');
	}

	public function testIs() {
		// Develop
		$this->assertTrue(Environment::is('Develop', '/virtual/www.dev.example.jp'));
		$this->assertTrue(Environment::is('Develop', '/virtual/monsat.dev.example.jp'));
		$this->assertTrue(Environment::is('Develop', '/virtual/dev.example.jp'));
		$this->assertFalse(Environment::is('Develop', '/virtual/monsat.example.jp'));
		// Staging
		$this->assertTrue(Environment::is('Staging', '/virtual/www.example.jp'));
		$this->assertTrue(Environment::is('Staging', '/virtual/www.example.jp'));
		$this->assertTrue(Environment::is('Staging', '/virtual/dev.www.example.jp'));
		$this->assertTrue(Environment::is('Staging', '/virtual/monsat.example.jp'));
		$this->assertTrue(Environment::is('Staging', '/virtual/example.jp'));
		$this->assertFalse(Environment::is('Staging', '/virtual/dev.monsat.example.jp'));
		$this->assertFalse(Environment::is('Staging', '/virtual/www.example.com'));
		// Production
		$this->assertTrue(Environment::is('Production', '/virtual/www.example.com'));
		$this->assertTrue(Environment::is('Production', '/virtual/example.com'));
		$this->assertFalse(Environment::is('Production', '/virtual/monsat.example.com'));
		$this->assertFalse(Environment::is('Production', '/virtual/www.example.jp'));
	}

	public function testGetEnvName() {
		$this->assertSame(Environment::getEnvName('/virtual/www.example.com'), 'www');
		$this->assertSame(Environment::getEnvName('/virtual/www.example.jp'), 'www');
		$this->assertSame(Environment::getEnvName('/virtual/example.jp'), '');
		$this->assertSame(Environment::getEnvName('/virtual/monsat.example.jp'), 'monsat');
		$this->assertSame(Environment::getEnvName('/virtual/monsat.dev.example.jp'), 'monsat');
	}

	public function testSettings() {
		$settings = array(
			'Develop' => array(
				'www.dev.example.jp',
				'monsat.dev.example.jp',
				'dev.example.jp',
			),
			'Staging' => array(
				'www.example.jp',
				'dev.www.example.jp',
				'monsat.example.jp',
				'example.jp',
			),
			'Production' => array(
				'www.example.com',
				'example.com',
			),
		);
		Environment::initialize($settings);

		$this->assertEquals(Environment::$settings, $settings);

		define('DEVELOP_CONSTANT_FOR_ENVIRONMENT', 'monsat.dev.example.jp');
		Environment::$constant = 'DEVELOP_CONSTANT_FOR_ENVIRONMENT';

		$this->assertTrue(Environment::is('Develop'));
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

