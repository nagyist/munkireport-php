<?php

//===============================================
// Include config
//===============================================
define( 'KISS', 1 );
require( 'config.php' );

//===============================================
// Debug
//===============================================
ini_set('display_errors', _DEBUG ? 'On' : 'Off' );
error_reporting( _DEBUG ? E_ALL : 0 );

//===============================================
// Includes
//===============================================
require( SYS_PATH.'kissmvc.php' );
require(APP_PATH.'helpers/site_helper'.EXT);
//===============================================
// Session
//===============================================
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
session_start();
date_default_timezone_set( 'Europe/Brussels' );	

//===============================================
// Uncaught Exception Handling
//===============================================s
function uncaught_exception_handler($e)
{
  ob_end_clean(); //dump out remaining buffered text
  $vars['message']=$e;
  die(View::do_fetch(APP_PATH.'errors/exception_uncaught.php',$vars));
}

set_exception_handler('uncaught_exception_handler');

function custom_error($msg='') 
{
	$vars['msg']=$msg;
	die(View::do_fetch(APP_PATH.'errors/custom_error.php',$vars));
}

//===============================================
// Database
//===============================================
function getdbh()
{
	if ( ! isset($GLOBALS['dbh']))
		try {
		$GLOBALS['dbh'] = new PDO('sqlite:'.APP_PATH.'db/db.sqlite');
		$GLOBALS['dbh']->exec("PRAGMA foreign_keys = ON"); // Turn on FK support
		//$GLOBALS['dbh'] = new PDO('mysql:host=localhost;dbname=dbname', 'username', 'password');
		} catch (PDOException $e) {
		die('Connection failed: '.$e->getMessage());
		}
	return $GLOBALS['dbh'];
}

//===============================================
// Autoloading for Business Classes
//===============================================
// Assumes Model Classes start with capital letters and Libraries start with lower case letters
function __autoload( $classname )
{
	$a=$classname[0];
	if ( $a >= 'A' && $a <='Z' ) require_once( APP_PATH.'models/'.$classname.'.php' );
	else require_once( APP_PATH.'libraries/'.$classname.'.php' );  
}

//===============================================
// Start the controller
//===============================================
$GLOBALS[ 'engine' ] = new Engine( $routes, 'show', 'index' );



//echo 'peak memory usage: '.number_format(memory_get_peak_usage()/1024/1024, 3).' MB';