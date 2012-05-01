# Environment

This is a plugin / module of CakePHP2.x

## Usage

### Path

```bash
$ cd /full/path/to
$ ls
www.example.com
$ cd www.example.com
$ ls
lib app
```

0. cd app/Plugin
1. git clone git://github.com/monsat/environment.git Environment
2. Edit like this on your APP/Config/bootstrap.php

```php
# APP/Config/bootstrap.php

// load plugin
CakePlugin::load('Environment');

// load Environment class
App::import('Lib', 'Environment.Environment');

// setting example
Environment::initialize(array(
  'Production' => array('example.info', 'www.example.info'),
  'Develop' => array('dev.www.example.info'),
));
```

```php
if (Environment::is('Production')) {
  Configure::write('debug', 0);
}
```

Return True if your basename(ROOT) is 'example.info' or 'www.example.info'

### Optional

check your basename(APP) instead of basename(ROOT)

```php
Environment::$constant = 'APP';
```

