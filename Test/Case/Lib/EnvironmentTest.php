<?php

App::import('Lib', 'Environment.Environment');

class EnvironmentTest extends CakeTestCase {

	protected $_envs;
	protected $_constant;

	public function setUp() {
		$this->_envs = Environment::$envs;
		Environment::initialize(array(
			'Production' => array(
				'www.example.com',
				'example.com',
			),
			'Staging' => array(
				'www.example.jp',
				'dev.www.example.jp',
				'monsat.example.jp',
				'example.jp',
			),
			'Develop' => array(
				'www.dev.example.jp',
				'monsat.dev.example.jp',
				'dev.example.jp',
			),
			'Empty' => array(),
		));
		$this->_constant = Environment::$constant;
	}

	public function tearDown() {
		Environment::$envs = $this->_envs;
		Environment::$constant = $this->_constant;
	}

	public function testGetHostName() {
		$this->assertSame(Environment::getHostName(), basename(ROOT));
		$this->assertSame(Environment::getHostName('/virtual/username.example.com'), 'username.example.com');
	}

	public function testIs() {
		// Production
		$this->assertTrue(Environment::is('Production', '/virtual/www.example.com'));
		$this->assertTrue(Environment::is('Production', '/virtual/example.com'));
		$this->assertFalse(Environment::is('Production', '/virtual/monsat.example.com'));
		$this->assertFalse(Environment::is('Production', '/virtual/www.example.jp'));
		// Staging
		$this->assertTrue(Environment::is('Staging', '/virtual/www.example.jp'));
		$this->assertTrue(Environment::is('Staging', '/virtual/www.example.jp'));
		$this->assertTrue(Environment::is('Staging', '/virtual/dev.www.example.jp'));
		$this->assertTrue(Environment::is('Staging', '/virtual/monsat.example.jp'));
		$this->assertTrue(Environment::is('Staging', '/virtual/example.jp'));
		$this->assertFalse(Environment::is('Staging', '/virtual/dev.monsat.example.jp'));
		$this->assertFalse(Environment::is('Staging', '/virtual/www.example.com'));
		// Develop
		$this->assertTrue(Environment::is('Develop', '/virtual/www.dev.example.jp'));
		$this->assertTrue(Environment::is('Develop', '/virtual/monsat.dev.example.jp'));
		$this->assertTrue(Environment::is('Develop', '/virtual/dev.example.jp'));
		$this->assertFalse(Environment::is('Develop', '/virtual/monsat.example.jp'));
	}

    /**
     * @expectedException BadMethodCallException
     */
	public function testCallStatic() {
		$result = Environment::isProduction('/virtual/www.example.com');
		$this->assertTrue($result);

		Environment::$envs['production'] = Environment::$envs['Production'];
		unset(Environment::$envs['Production']);
		$result = Environment::isProduction('/virtual/www.example.com');
		$this->assertTrue($result);

		Environment::isNotDefinedEnvName();
	}

	public function testEnvs() {
		$envs = array(
			'Production' => array(
				'www.example.com',
				'example.com',
			),
			'Staging' => array(
				'www.example.jp',
				'dev.www.example.jp',
				'monsat.example.jp',
				'example.jp',
			),
			'Develop' => array(
				'www.dev.example.jp',
				'monsat.dev.example.jp',
				'dev.example.jp',
			),
		);
		Environment::initialize($envs);

		$this->assertEquals(Environment::$envs, $envs);

		define('DEVELOP_CONSTANT_FOR_ENVIRONMENT', 'monsat.dev.example.jp');
		Environment::$constant = 'DEVELOP_CONSTANT_FOR_ENVIRONMENT';

		$this->assertTrue(Environment::is('Develop'));
	}

	public function testIsCLIAndIsWeb() {
		$_env = $_ENV;
		$_server = $_SERVER;

		$_ENV['argc'] = 2;
		$this->assertTrue(Environment::isCLI());
		$this->assertFalse(Environment::isWeb());

		$_ENV['argc'] = $_SERVER['argc'] = 1;
		$this->assertFalse(Environment::isCLI());
		$this->assertTrue(Environment::isWeb());

		unset($_ENV['argc']);
		$this->assertFalse(Environment::isCLI());
		$this->assertTrue(Environment::isWeb());

		$_ENV = $_env;
		$_SERVER = $_server;
	}

}

