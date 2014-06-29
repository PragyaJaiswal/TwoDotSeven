<?php
namespace TwoDot7\Config;
#  _____                      _____ 
# /__   \__      _____       |___  |
#   / /\/\ \ /\ / / _ \         / / 
#  / /    \ V  V / (_) |  _    / /  
#  \/      \_/\_/ \___/  (_)  /_/   

# Put the Domain Name, and the Root directory name.
define("BASEURI", "http://localhost:70");
define("ROOTDIR", "--Fill Your Info Here --");

# Default Language files.
define("LANG_EN", "langEN.php");

# SQL Configuration.
const SQL_HOST = "--Fill Your Info Here --";
const SQL_USERNAME = "--Fill Your Info Here --";
const SQL_PASSWORD = "--Fill Your Info Here --";
const SQL_DBNAME = "--Fill Your Info Here --";

# Encryption Keys. Generate them on <todo>
# WARNING: CHANGING THESE VALUES RENDERS ALL THE ENCRYPTED DATA USELESS.
const SHA_PRIVATE = "--Fill Your Info Here --";
const SHA_INITIAL_VECTOR = "--Fill Your Info Here --";

# Optional Offsets.
# Default Offset to GMT +5:30, Indian time zone. Set 0 for GMT.
define("TIME_OFFSET", 9800);
# Maximum number of Concurrent logins.
const MAXIMUM_CONCURRENT_LOGINS = 10;

# Environment Settings.
error_reporting(E_ALL); 	# Comment it out in Production Environment.
#error_reporting(0); # Comment it out in Development Environment.
?>