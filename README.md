This is a plugin / module of CakePHP.

Usage

0. cd APP/libs
1. git clone git://github.com/monsat/environment.git
2. write like this on your APP/config/bootstrap.php

App::import('Lib', "Environment");
Environment::initialize(array(
  'develop_server' => "example.jp", // your develop server's domain
  'production_server' => "example.info", // your production server's domain
  'develop_domains' => array("www", "dev"), // your sub domains of develop server
  'production_domains' => array("www"), // your sub domains of production server
));

