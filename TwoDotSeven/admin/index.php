<?php
namespace TwoDot7\Admin;
#  _____                      _____                         
# /__   \__      _____       |___  |     ___  ______________
#   / /\/\ \ /\ / / _ \         / /     / _ \/ __/ __/_  __/
#  / /    \ V  V / (_) |  _    / /     / , _/ _/_\ \  / /   
#  \/      \_/\_/ \___/  (_)  /_/     /_/|_/___/___/ /_/    

/**
 * This Invokes and processes the Admin Views.
 * @author Prashant Sinha <prashantsinha@outlook.com>
 * @since 0.0
 */
require "../../import.php";
_Import('config.php');
_Import('database.php');
_Import('exceptions.php');
_Import('validator.php');
_Import('utility.php');
_Import('install.php');
_Import('user.php');
_Import('cron.php');
require "login.php";

# Parse incoming URI and then process it.
$URI = explode('/', preg_replace("/[\/]+/", "/", $_SERVER['REQUEST_URI']));

const BASE = 2;

switch(strtolower(isset($URI[BASE]) ? $URI[BASE] : False)) {
	case 'login':
		Login\init();
		break;
}