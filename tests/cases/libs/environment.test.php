<?php

App::import('Lib', "Utilities.Environment");
Environment::initialize(array(
	'develop_server' => "example.jp",
));

class EnvironmentTestCase extends CakeTestCase {

	function startCase() {
	}
	function endCase() {
	}

	function startTest($method) {
	}
	function endTest() {
	}
	// internal
	public function testInternalGetHostName() {
		$this->assertIdentical(Environment::getHostName(), basename(ROOT));
		$this->assertIdentical(Environment::getHostName("/virtual/username.example.com"), "username.example.com");
	}
	// public
	public function testIsProduction() {
		$this->assertIdentical(Environment::isProduction("/virtual/www.example.com"), true);
		$this->assertIdentical(Environment::isProduction("/virtual/monsat.example.com"), false);
		$this->assertIdentical(Environment::isProduction("/virtual/www.example.jp"), false);
	}
	public function testIsTest() {
		$this->assertIdentical(Environment::isTest("/virtual/www.example.jp"), true);
		$this->assertIdentical(Environment::isTest("/virtual/monsat.example.jp"), true);
		$this->assertIdentical(Environment::isTest("/virtual/dev.monsat.example.jp"), false);
		$this->assertIdentical(Environment::isTest("/virtual/www.example.com"), false);
	}
	public function testIsDevelop() {
		$this->assertIdentical(Environment::isDevelop("/virtual/dev.monsat.example.jp"), true);
		$this->assertIdentical(Environment::isDevelop("/virtual/monsat.example.jp"), false);
		$this->assertIdentical(Environment::isDevelop("/virtual/www.example.jp"), false);
		$this->assertIdentical(Environment::isDevelop("/virtual/dev.monsat.example.com"), true);
	}
	public function testGetEnvName() {
		$this->assertIdentical(Environment::getEnvName("/virtual/www.example.com"), "www");
		$this->assertIdentical(Environment::getEnvName("/virtual/www.example.jp"), "www");
		$this->assertIdentical(Environment::getEnvName("/virtual/monsat.example.jp"), "monsat");
		$this->assertIdentical(Environment::getEnvName("/virtual/dev.monsat.example.jp"), "dev.monsat");
	}
	
}

