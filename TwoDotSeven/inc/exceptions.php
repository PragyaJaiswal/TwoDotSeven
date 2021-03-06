<?php
namespace TwoDot7\Exception;
#  _____                      _____ 
# /__   \__      _____       |___  |
#   / /\/\ \ /\ / / _ \         / / 
#  / /    \ V  V / (_) |  _    / /  
#  \/      \_/\_/ \___/  (_)  /_/   

/**
 * Exception Classes.
 * @author	Prashant Sinha <firstname,lastname>@outlook.com
 * @since	v0.0 20062014
 * @version	0.0
 */

class BadUserName extends \Exception {
	protected $message = "The User Name provided is not Valid";
}

class AuthError extends \Exception {
	protected $message = "Authentication Failure.";
}

class InvalidArgument extends \Exception {
	// Empty class.
}

class IncompleteArgument extends \Exception {
	// Empty class.
}

function RenderError($Exception) {
	require_once $_SERVER['DOCUMENT_ROOT'].'/TwoDotSeven/admin/login.signup.errors.php';
	\TwoDot7\Admin\Template\Login_SignUp_Error\_init(array(
		'Call' => 'Error',
		'ErrorMessageHead' => 'Sorry, there was a Server Error',
		'ErrorMessageFoot' => 'Couldn\'t load some or all the required files.',
		'ErrorCode' => 'ImportError: '.$Name,
		'Code' => 500,
		'Mood' => ''));
	die();
	return 0;
}