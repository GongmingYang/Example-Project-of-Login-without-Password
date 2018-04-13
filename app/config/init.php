<?php
/**
 * Created by PhpStorm.
 * User: ygm
 * Date: 6/11/2016
 * Time: 6:05 PM
 */
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Session\Adapter\Files as Session;

use Phalcon\Cache\Backend\Apc as ApcCache;
use Phalcon\Cache\Frontend\Data as FrontendData;
use Phalcon\Cache\Backend\Libmemcached as Memcache;

/*
 *
 */
require APP_PATH ."app/controllers/ControllerBase.php";
// Register an autoloader
$loader = new Loader();
$loader->registerDirs(array(
    '../app/controllers/',
    '../app/models/',
    '../app/library/'
))->register();

// Create a DI
$di = new FactoryDefault();

// Setup the view component
//    $di->set('view', function () {
//        $view = new View();
//        $view->setViewsDir('../app/views/');
//        return $view;
//    });

// Registering Volt as template engine
$di->set(
  'view',
  function () {
    $view = new View();
    $view->setViewsDir('../app/views/');
    $view->registerEngines(
      array(
        ".phtml" => 'Phalcon\Mvc\View\Engine\Volt'
//                    ".volt" => 'Phalcon\Mvc\View\Engine\Volt'
      )
    );
    return $view;
  }
);

/**
 * Registering prsa as the basic encryption toolkit
 */
require_once APP_PATH ."app/library/prsa.php";
$di->set(
  'prsa',
  function () {
    $prsa = new prsa();
    return $prsa;
  }
);


/**
 * Start the session the first time some component request the session service
 */
//use Phalcon\Session\Adapter\Files as Session;
$di->set('session', function () {
  # session_start();
  if(!isset($session)) {
      $session = new Session();
      $session->start();
      return $session;
  }
});

// Setup the database service
// aws5e7e
$di->set('db', function () {
  return new DbAdapter(array(
    "host"     => "localhost",
    "username" => "root",//@TODO this is too simple,please modify it for your sake.
    "password" => "root",
    "dbname"   => "recorder",
    'charset'  => 'utf8'
  ));
});


// Setup a base URI so that all generated URIs include the "tutorial" folder
$di->set('url', function () {
  $url = new UrlProvider();
  $url->setBaseUri('/');
  return $url;
});

// Handle the request
$application = new Application($di);
echo $application->handle()->getContent();

//    echo "My First Web Design from scratch";//exit;
