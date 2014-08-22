<?php
namespace TwoDot7\Broadcast;
use \TwoDot7\Mailer as Mailer;
use \TwoDot7\Database as Database;
#  _____                      _____ 
# /__   \__      _____       |___  |
#   / /\/\ \ /\ / / _ \         / / 
#  / /    \ V  V / (_) |  _    / /  
#  \/      \_/\_/ \___/  (_)  /_/   

/**
 * Broadcast Origin/Target from/to a User.
 * const USER 1
 * @example Can be accessed by other Namespaces as \TwoDot7\Broadcast\USER or, (int)1.
 */
const USER = 1;

/**
 * Broadcast Origin/Target from/to a registered Bit.
 * const BIT 2
 */
const BIT = 2;

/**
 * Broadcast Origin from the System.
 * const BIT 2
 */
const SYSTEM = 3;

/**
 * Broadcast Origin/Target from/to a Group.
 * const GROUP 4
 */
const GROUP = 4;

/**
 * Broadcast Source to Custom Key.
 * const CUSTOM 5
 */
const CUSTOM = 5;

/**
 * Action wrapper class for Broadcasts.
 */
class Action {
	public static function Add($Data = array()) {
		// Atleast the Addition data must be sent in the API call.
		// Fields:
		// OriginType: Required USER / BIT / or SYSTEM
		// Origin: Required. UserName or BitID or System Function, which/who is calling the Addition API
		// TargetType: Optional. USER / Group or Custom.
		// Target: In case of USER: List of UserName(s),
		// Target: In case of Group: List of group(S),
		// Target: In case of Custom: 0.
		// Visible: Default: 0. Public: 1, Target only: 2.
		// Datatype: Index, Generated by the Util::Pack.
		// Data: Packed data.

		// Check the Arguments
		if (!isset($Data['OriginType']) ||
			!isset($Data['Origin']) ||
			!isset($Data['TargetType']) ||
			!isset($Data['Data'])) {
			throw new \TwoDot7\Exception\IncompleteArgument("Incomplete Arguments in \\TwoDot7\\Broadcast\\Action::Add");
		}

		// Check the Validity of Arguments
		// Origin type
		if ($Data['OriginType'] === \TwoDot7\Broadcast\USER ||
			$Data['OriginType'] === \TwoDot7\Broadcast\BIT ||
			$Data['OriginType'] === \TwoDot7\Broadcast\SYSTEM) {

			switch ($Data['OriginType']) {
				case \TwoDot7\Broadcast\USER :
					if (!\TwoDot7\Util\Redundant::UserName($Data['Origin'])) {
						return array(
							'Success' => False,
							'Error' => "Unknown User"
							);
					}
					break;
				case \TwoDot7\Broadcast\BIT :
					if (!\TwoDot7\Util\Redundant::Bit($Data['Origin'])) {
						return array(
							'Success' => False,
							'Error' => "Unknown Bit"
							);
					}
					break;
				case \TwoDot7\Broadcast\SYSTEM :
					if (!\TwoDot7\Validate\Alphanumeric($Data['Origin'], 6, 255)) {
						\TwoDot7\Util\Log("Invalid Broadcast Origin by System: {$Data['Origin']}", "ALERT");
						return array(
							'Success' => False,
							'Error' => "Invalid system process."
							);
					}
					break;
			}
		} else throw new \TwoDot7\Exception\InvalidArgument("OriginType is not a valid type.");
		
		// Target type
		if ($Data['TargetType'] === \TwoDot7\Broadcast\USER ||
			$Data['TargetType'] === \TwoDot7\Broadcast\GROUP ||
			$Data['TargetType'] === \TwoDot7\Broadcast\CUSTOM) {
			
			switch ($Data['Target']) {
				case \TwoDot7\Broadcast\USER:
					if (is_array($Data['Target'])) {
						$Pass = True;
						foreach ($Data['Target'] as $UserName) {
							if (!\TwoDot7\Util\Redundant::UserName($UserName)) $Pass = False;
						}
						if (!$Pass) {
							return array(
								'Success' => False,
								'Error' => "The Target User list is not Valid"
								);
						}
					} elseif (is_string($Data['Target'])) {
						if (!\TwoDot7\Util\Redundant::UserName($Data['Target'])) {
							return array(
								'Success' => False,
								'Error' => "Invalid Target User");
						}
						// Normalize the Data.
						$Data['Target'] = array($Data['Target']);
					} else {
						return array(
							'Success' => False,
							'Error' => "Invalid Target"
							);
					}
					break;
				case \TwoDot7\Broadcast\GROUP:
					if (is_array($Data['Target'])) {
						$Pass = True;
						foreach ($Data['Target'] as $Group) {
							if (!\TwoDot7\Util\Redundant::Group($Group)) $Pass = False;
						}
						if (!$Pass) {
							return array(
								'Success' => False,
								'Error' => "The Target Group list is not Valid"
								);
						}
					} elseif (is_string($Data['Target'])) {
						if (!\TwoDot7\Util\Redundant::Group($Data['Target'])) {
							return array(
								'Success' => False,
								'Error' => "Invalid Target Group");
						}
						// Normalize the Data.
						$Data['Target'] = array($Data['Target']);
					} else {
						return array(
							'Success' => False,
							'Error' => "Invalid Target"
							);
					}
					break;
				case \TwoDot7\Broadcast\CUSTOM:
					if ($Data['Target']) \TwoDot7\Util\Log("WARNING: Target not supported for CUSTOM broadcasts.", "DEBUG");
					$Data['Target'] = 0;
					break;
			}
		} else throw new \TwoDot7\Exception\InvalidArgument("TargetType is not a valid type.");
		
				

		$Query = "INSERT INTO _broadcast (OriginType, Origin, TargetType, Target, Visible, Datatype, Data) VALUES (:OriginType, :Origin, :TargetType, :Target, :Visible, :Datatype, :Data)";
	}

	public static function Remove() {
		// Deletes a broadcast
	}

	public static function Update() {
		// Updates a broadcast
	}

	public static function CreateFeed() {
		// Creates the Feed.
	}
}

class Utils {
	public static function Pack($Data) {
		// Packs the Raw broadcast data. Packs images and stuff as well.
	}

	public static function Unpack() {
		// Unpacks a Packed broadcast data.
	}
}