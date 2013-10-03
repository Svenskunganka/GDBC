<?php
/**
 * GMod SQLiteToMySQL example written by Svenskunganka
 *
 * Website: http://tjservers.org
 * Facepunch: http://facepunch.com/member.php?u=445369
 */

/********************************************************************************************************************************************************************************
 * EDIT THESE SETTINGS IF YOU WANT TO USE PASTEBIN																																*
 * For more information: http://pastebin.com/api 																																*
 *******************************************************************************************************************************************************************************/
 	define ("ENABLE_PASTEBIN", true); 								// Enable or disable Pastebin. If disabled, you can just leave the fields below as they are.				*
 	define ("PB_DEV_KEY", "PastebinAPIDeveloperAuthKey"); 			// Your Pastebin API Developer Auth Key. Can be found here: http://pastebin.com/api#1 						*
	define ("PB_PRIVATE", 0); 										// Public = 0, Unlisted = 1, Private = 2																	*
	define ("PB_NAME", "converted.sql"); 							// Name of the file to be pasted 																			*
	define ("PB_EXPIRE_DATE", "1M"); 								// When the paste should expire. Read more about these values here: http://pastebin.com/api#6				*
/********************************************************************************************************************************************************************************/

ini_set("max_execution_time", 120); // Sets the maximum time this script will execute (2 minutes)
ini_set("upload_max_filesize", "10M"); // Setting the max filesize an uploaded file can be. (10 Megabytes)
include 'class.php'; // Including the class itself.

$type = "pointshop"; // Set to "darkrp" if you want to convert DarkRP values, "pointshop" for Pointshop values.
$method = "download"; // Set to "download" if you want the code to be downloaded. "pastebin" if you want the code to be sent to http://pastebin.com
$file = "sv.db"; // Full path & name of the file that you want to convert. Example: "/files/sv.db"
$class = new SQLiteToMySQL(); // Create new object

// Convert!
if(!$class->convert($type, $method, $file)) {
	die("Could not convert the file. The file may not be present, wrong file name specified in the code, wrong MIME-type or SQLite3 isn't installed/accessible by the web-server.");
}
?>