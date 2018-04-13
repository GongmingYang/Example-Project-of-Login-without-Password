<?php
try
{
  define('APP_PATH', realpath('..') . '/');
  require APP_PATH . 'app/config/config.php';
  require APP_PATH . 'app/config/init.php';

} catch (Exception $e)
{
     echo "Exception: ", $e->getMessage();
}