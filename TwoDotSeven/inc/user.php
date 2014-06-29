<?php
namespace TwoDot7\User;
use \TwoDot7\Validate as Validate;
use \TwoDot7\Util as Util;
#  _____                      _____ 
# /__   \__      _____       |___  |
#   / /\/\ \ /\ / / _ \         / / 
#  / /    \ V  V / (_) |  _    / /  
#  \/      \_/\_/ \___/  (_)  /_/   

/**
 * Builds a User Handler object. Requires Configuration NameSpace, Database Namespace to Work.
 * Handler is Dynamic and can add Any Attibute dynamically.
 * @author	Prashant Sinha <firstname,lastname>@outlook.com
 * @since	v0.0 20072014
 * @version	0.0
 */
class Handler {
	private $UserCredentials;
	private $UserData;

	function __construct() {
		$DatabaseHandle = new TwoDot7\Database\Handler;
		$UserQuery = "SELECT * FROM 'TwoDot_User'";
		$DatabaseHandle->Query($UserQuery);
		$UserData = pass;
	}
}
/**
 * Wrapper for the User Session Related functions.
 * @author	Prashant Sinha <firstname,lastname>@outlook.com
 * @since	v0.0 23072014
 * @version	0.0
 */
class Session {
	/**
	 * This function Authenticates and Handles Sign In process.
	 * @param	$Data -array- UserName and Password are sent to it.
	 * @return	-array- Contains Success status, Tokens and Status on successful authentication.
	 * @author	Prashant Sinha <firstname,lastname>@outlook.com
	 * @throws	IncompleteArgument Exception.
	 * @since	v0.0 29072014
	 * @version	0.0
	 */
	public static function Login($Data) {
		if(!Validate\UserName($Data['UserName'], False)) {
			return array(
				'Success' => False,
				'Messages' => array(
					array(
						'Message' => 'Invalid UserName or Password.',
						'Class' => 'ERROR')));
		}
		$DatabaseHandle = new \TwoDot7\Database\Handler;
		$DBResponse = $DatabaseHandle->Query("SELECT * FROM _user WHERE UserName=:UserName", array(
			'UserName' => $Data['UserName']))->fetch();
		if($DBResponse) {
			/**
			 * @internal	This Block means that the UserName is valid and Existing.
			 */
			if(\TwoDot7\Util\PBKDF2::ValidatePassword($Data['Password'], $DBResponse['Password'])) {
				/**
				 * @internal	Valid User. Execute Login.
				 */
				$HashGen = Util\Crypt::RandHash();
				$Hash = self::AddToken(array(
					'JSON' => $DBResponse['Hash'],
					'Token' => $HashGen));
				$DatabaseHandle->Query("UPDATE _user SET Hash=:Hash WHERE UserName=:UserName;", array(
					'Hash' => $Hash,
					'UserName' => $Data['UserName']));
				$Expire=time()+(300*24*60*60);
				return array(
					'Success' => True,
					'Hash' => $HashGen,
					'UserName' => $Data['UserName']);
			}
			else {
				return array(
					'Success' => False,
					'Messages' => array(
						array(
							'Message' => 'Invalid UserName or Password.',
							'Class' => 'ERROR')));
			}
		}
		else {
			return array(
				'Success' => False,
				'Messages' => array(
					array(
						'Message' => 'Invalid UserName or Password.',
						'Class' => 'ERROR')));
		}
	}
	/**
	 * This function Authenticates and Check the session status of user.
	 * @param	$Data -array- UserName and Hash are sent to it.
	 * @return	-array- Contains Success status, Tokens and Status on successful authentication.
	 * @author	Prashant Sinha <firstname,lastname>@outlook.com
	 * @throws	IncompleteArgument Exception.
	 * @since	v0.0 29072014
	 * @version	0.0
	 */
	public static function Status($Data) {
		if( isset($Data['UserName']) &&
			isset($Data['Hash'])) {
			//
			$Query = "SELECT * FROM _user WHERE UserName=:UserName";
			$DBResponse = \TwoDot7\Database\Handler::Exec($Query, array('UserName' => $Data['UserName']))->fetch();
			if(self::IsToken(array(
				'JSON' => isset($DBResponse['Hash']) ? $DBResponse['Hash'] : False,
				'Token' => $Data['Hash']))) {

				return array (
					'Success' => True,
					'LoggedIn' => True,
					'UserName' => $Data['UserName'],
					'Hash' => $DBResponse['Hash'],
					'Tokens' => $DBResponse['Tokens'],
					'Status' => $DBResponse['Status']);
			}
			else {
				Util\Log("Failed to Verify Session. Data: ".json_encode($Data), "TRACK");
				return array(
					'Success' => False,
					'LoggedIn' => False,
					'UserName' => False);
			}
		}
		else {
			throw new \TwoDot7\Exception\IncompleteArgument("Invalid Argument in Function \\User\\Login::UserStatus");
		}
	}
	
	/**
	 * This function Adds Token, Rolling over to a Max Size MAXIMUM_CONCURRENT_LOGINS in Config file.
	 * @param	$Data -array- JSON initial string and Token to be added.
	 * @return	-string- JSON string containing Tokens.
	 * @author	Prashant Sinha <firstname,lastname>@outlook.com
	 * @throws	IncompleteArgument Exception.
	 * @since	v0.0 29072014
	 * @version	0.0
	 */
	private static function AddToken($Data) {
		if( isset($Data['JSON']) &&
			isset($Data['Token'])) {
			$Tokens = json_decode($Data['JSON']);
			if(is_array($Tokens)) {
				if(count($Tokens) >= \TwoDot7\Config\MAXIMUM_CONCURRENT_LOGINS) {
					$Tokens = array_diff($Tokens, array($Tokens[0]));
					$Tokens = array_merge($Tokens, array($Data['Token']));
					return json_encode($Tokens);
				}
				else {
					return json_encode(array_merge($Tokens, array($Data['Token'])));
				}
			}
			else {
				return json_encode(array(
					$Data['Token']));
			}
		}
		else {
			throw new \TwoDot7\Exception\IncompleteArgument("Invalid Argument in Function \\User\\Login::AddToken");
		}
	}

	/**
	 * This function Removes a token from the JSON string.
	 * @param	$Data -array- JSON initial string and Token to be removed.
	 * @return	-string- JSON string containing Tokens.
	 * @author	Prashant Sinha <firstname,lastname>@outlook.com
	 * @throws	IncompleteArgument Exception.
	 * @since	v0.0 29072014
	 * @version	0.0
	 */
	private static function RemoveToken($Data) {
		if( isset($Data['JSON']) &&
			isset($Data['Token'])) {
			$Tokens = json_decode($Data['JSON']);
			if(is_array($Tokens)) {
				return json_encode(array_diff($Tokens, array($Data['Token'])));
			}
			else {
				return json_encode(array());
			}
		}
		else {
			throw new \TwoDot7\Exception\IncompleteArgument("Invalid Argument in Function \\User\\Login::RemoveToken");
		}
	}

	/**
	 * This function Checks if a Key exists in the JSON string.
	 * @param	$Data -array- JSON initial string and Token to be checked.
	 * @return	-boolean- Self Explanatory.
	 * @author	Prashant Sinha <firstname,lastname>@outlook.com
	 * @throws	IncompleteArgument Exception.
	 * @since	v0.0 29072014
	 * @version	0.0
	 */
	private static function IsToken($Data) {
		if( isset($Data['JSON']) &&
			isset($Data['Token'])) {
			$Tokens = json_decode($Data['JSON']);
			if(is_array($Tokens)) {
				return in_array($Data['Token'], $Tokens);
			}
			else {
				return False;
			}
		}
		else {
			throw new \TwoDot7\Exception\IncompleteArgument("Invalid Argument in Function \\User\\Login::IsToken");
		}
	}
}

/**
 * Class wrapper for Account Management functions.
 * @author	Prashant Sinha <firstname,lastname>@outlook.com
 * @since	v0.0 26072014
 * @version	0.0
 */
class Account {
	/**
	 * This function Validates and adds the User.
	 * @internal Requires Validation/.
	 * @param	$SignupData -array- Self Explanatory
	 * @param	$Method -bool- Not Implemented. From __future__
	 * @return	-array- Contains Success status, and Corresponding messages.
	 * @author	Prashant Sinha <firstname,lastname>@outlook.com
	 * @throws	IncompleteArgument Exception.
	 * @since	v0.0 26072014
	 * @version	0.0
	 */
	public static function Add($SignupData, $Method=False) {
		if (isset($SignupData['UserName']) &&
			isset($SignupData['EMail'])	&& 
			isset($SignupData['Password']) &&
			isset($SignupData['ConfPass'])) {

			$Validate = array( 'Success' => True, 'Messages' => array());

			/**
			 * @internal	These checks the validity of the input data.
			 */
			if(!Validate\UserName($SignupData['UserName'], False)) {
				$Validate['Success'] = False;
				array_push($Validate['Messages'], array(
					'Message' => 'The entry for UserName field is not correct. Please try again.', 
					'MessageMode' => 'Error'));
			}
			if(!Validate\EMail($SignupData['EMail'], False)) {
				$Validate['Success'] = False;
				array_push($Validate['Messages'], array(
					'Message' => 'The entry for EMail field is not correct. Please try again.', 
					'MessageMode' => 'Error'));
			}
			if (!Validate\Password($SignupData['Password']) ||
				!Validate\Password($SignupData['ConfPass']) ||
				!($SignupData['Password'] === $SignupData['ConfPass'])) {
				$Validate['Success'] = False;
				array_push($Validate['Messages'], array(
					'Message' => 'The entry for Password fields are not correct. Please try again.', 
					'MessageMode' => 'Error'));
			}

			/**
			 * @internal	These checks the redundancy of the input data.
			 */
			if (Util\Redundant::EMail($SignupData['EMail'])) {
				$Validate['Success']=FALSE;
				array_push($Validate['Messages'], array(
					'Message' => 'The EMail ID is already there in the DataBase, please enter a different one.', 
					'MessageMode' => 'Error'));
			}
			if (Util\Redundant::UserName($SignupData['UserName'])) {
				$Validate['Success']=FALSE;
				array_push($Validate['Messages'], array(
					'Message' => 'The UserName you supplied is taken, please choose a different one.', 
					'MessageMode' => 'Error'));
			}

			if (!$Validate['Success']){
				/**
				 * @internal	Can't add the User Account, Validation error. Stop here.
				 */
				Util\Log("Failed to Add the Account. POST data: ".json_encode($SignupData), "TRACK");
				return $Validate;
			}
			else {
				/**
				 * @internal	Status Cheat: {
				 * 					(-2, Banned Permanently),
				 * 					(-1, Banned Temporarily and Flagged),
				 * 					(0, Never Reviewed),
				 * 					(1, Currently Flagged for Review),
				 * 					(2, Verified But some crutial changes have been made),
				 * 					(4, Verified),
				 * 					(9, Verified + All area access)}
				 * @internal	This adds the User in the Database.
				 */

				$DatabaseHandle = new \TwoDot7\Database\Handler;

				$Query1 = "INSERT INTO _user (UserName, Password, EMail, Hash, Tokens, Status) VALUES (:UserName, :Password, :EMail, :Hash, :Tokens, :Status)";
				$Query2 = "INSERT INTO _usermeta (UserName, Name, Clearance, Meta, MetaAlerts, MetaInfo) VALUES (:UserName, :Name, :Clearance, :Meta, :MetaAlerts, :MetaInfo)";

				$Response = $DatabaseHandle->Query($Query1, array(
					'UserName' => $SignupData['UserName'],
					'Password' => \TwoDot7\Util\PBKDF2::CreateHash($SignupData['Password']),
					'EMail' => $SignupData['EMail'],
					'Hash' => 'NULL',
					'Tokens' => json_encode(array()),
					'Status' => 0))->rowCount();

				$Response += $DatabaseHandle->Query($Query2, array(
					'UserName' => $SignupData['UserName'],
					'Name' => '#',
					'Clearance' => 0,
					'Meta' => json_encode(array()),
					'MetaAlerts' => json_encode(array(
						array(
							'AlertType'	=> 'Info',
							'ID' => md5(strtolower('This is your Notification Panel. It contains everything important, that you need to know.')),
							'Header' => 'Welcome',
							'Content' => 'This is your Notification Panel. It contains everything important, that you need to know.',
							'Dismissed' => False))),
					'MetaInfo' => json_encode(array(
						'Hubs' => array(),
						'Groups' => array()))))->rowCount();

				if ($Response) {
					Util\Log("User Account: ".$SignupData['UserName']." added.");
					// Adding temporary sign-up tracking in Encrypted Log.
					Util\Log("User Account: ".json_encode($SignupData)." added", "TRACK");
					return array(
						'Success' => True, 
						'Messages' => array(
							array(
								'Message' => 'Successfully Completed Sign Up. Please Check your EMail to proceed.',
								'MessageMode' => 'Success')));
				}
				else {
					Util\Log("Failed to Add the Account. 500. POST data: ".json_encode($SignupData), "TRACK");
					return array(
						'Success' => False, 
						'Messages' => array(
							array(
								'Message' => 'Internal Error 500.', 
								'MessageMode' => 'Error')));
				}
			}
		}
		else {
			throw new \TwoDot7\Exception\IncompleteArgument("Invalid Argument in Function \\User\\Account::Add");
		}
	}

	public static function Meta($Username, $Mode) {
	}

	public static function Escalate($UserName) {
	}

	public static function RecoverPassword($Data) {
	}
}
?>