<?php
/**
 * Class written by Svenskunganka
 *
 * Website: http://tjservers.org
 * Facepunch: http://facepunch.com/member.php?u=445369
 */

class SQLiteToMySQL {

	/**
	 * Points to the file to convert
	 *
	 * @access private
	 * @var string
	 */
	private $file = "";

	/**
	 * The DarkRP MySQL/SQlite Tables
	 *
	 * @access private
	 * @var array
	 */
	private $darkrp_tables = array(
		"FADMIN_GROUPS",
		"FADMIN_MOTD",
		"FADMIN_PRIVILEGES",
		"FADMIN_RESTRICTEDENTS",
		"FAdminBans",
		"FAdmin_PlayerGroup",
		"FAdmin_ServerSettings",
		"FAdmin_Immunity",
		"FPP_ANTISPAM1",
		"FPP_BLOCKED1",
		"FPP_BLOCKEDMODELS1",
		"FPP_BLOCKEDMODELSETTINGS1",
		"FPP_ENTITYDAMAGE1",
		"FPP_GLOBALSETTINGS1",
		"FPP_GRAVGUN1",
		"FPP_GROUPMEMBERS1",
		"FPP_GROUPS3",
		"FPP_GROUPTOOL",
		"FPP_PHYSGUN1",
		"FPP_TOOLADMINONLY",
		"FPP_TOOLGUN1",
		"FPP_TOOLRESTRICTPERSON1",
		"FPP_TOOLTEAMRESTRICT",
		"darkrp_cvar",
		"darkrp_door",
		"darkrp_doorgroups",
		"darkrp_jobown",
		"darkrp_jobspawn",
		"darkrp_player",
		"darkrp_position",
		"playerinformation",
	);

	/**
	 * Checks if file is of the correct MIME-type
	 *
	 * @return boolean
	 */
	public function checkMime () {
		$mime = explode(".", $this->file);
		if($mime[1] == "db") {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Checks if the sqlite3 package is installed.
	 * 
	 * @return boolean
	 */
	public function checkSQLite3 () {
		if(exec("sqlite3 --version")) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Extracts the sv.db to a .sql file to the directory of the script
	 * 
	 * @return boolean
	 */
	public function extractSQL () {
		if($this->checkMime() && $this->checkSQLite3()) {
			$newpath = __DIR__ . "/db.sql";
			passthru("sqlite3 " . $this->file . " .dump > " . $newpath ."");
			$this->file = $newpath;
			return true;
		}
		else {
			return false;
		}
	}
	

	/**
	 * Pastes the converted SQL to Pastebin and send the user to the paste.
	 * 
	 * @param string
	 */
	public function pastebin ($code) {
		$url = "http://pastebin.com/api/api_post.php";
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'api_option=paste&api_user_key=&api_paste_private='.PB_PRIVATE.'&api_paste_name='.PB_NAME.'&api_paste_expire_date='.PB_EXPIRE_DATE.'&api_paste_format=SQL&api_dev_key='.PB_DEV_KEY.'&api_paste_code='.$code.'');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_NOBODY, 0);

		$response = curl_exec($ch);

		if(!strpos($response, "Bad API")) {
			header("Location: ".$response);
		}
		else {
			die($response);
		}
		
	}

	/**
	 * Puts the converted content into a SQL file.
	 *
	 * @param string $code
	 */

	public function download ($code) {
		file_put_contents("converted.sql", $code);
		echo "<script>window.location='converted.sql'</script>";
	}

	/**
	 * Converts SQLite INSERT values to MySQL values.
	 *
	 * @param string $type
	 * @param string $method
	 * @param string $file
	 * @return boolean
	 */
	public function convert ($type, $method, $file) {
		$this->file = __DIR__ . "/" . $file;
		if ($this->extractSQL()) {
			$content = file_get_contents($this->file);
			$eachline = explode(";", $content);
			foreach($eachline as $key => &$value) {
				if($type == "pointshop") {
					if(strpos($value, 'INSERT INTO "playerpdata"') !== false && strpos($value, '[PS_Points]') !== false) {
						$templine = explode(",", $eachline[$key+1]);
						$var = ",";
						foreach($templine as $key2 => $value2) {
							if ($key2 > 0) {
								$var = $var.$value2;
							}
						}
						unset($eachline[$key+1]);
						$value = str_replace('"playerpdata"', "`pointshop_data`", $value);
						$value = str_replace("[PS_Points]", "", $value);
						$value = str_replace(")",$var,$value);
					}
					else {
						unset($eachline[$key]);
					}
				}
				elseif($type == "darkrp") {
					foreach($this->darkrp_tables as $table) {
						if(strpos($value, 'INSERT INTO "'.$table.'"') !== false) {
							$value = str_replace('"'.$table.'"', '`'.$table.'`', $value);
						}
						else {
							$templine = explode("(", $value);
							$blacklist = array (
								'INSERT',
								'INTO',
								'VALUES',
								'"',
								'`'
							);
							$templine[0] = trim(str_replace($blacklist, "", $templine[0]));
							if(!in_array($templine[0], $this->darkrp_tables)) {
								unset($eachline[$key]);
							}
						}
					}
				}
				elseif($type == "utime") {
					if(strpos($value, 'INSERT INTO "utime"') === false) {
						unset($eachline[$key]);
					}
				}
			}
			$code = implode(";", $eachline);
			$code .= ";";
			unlink($this->file);
			if ($type == "pastebin" && ENABLE_PASTEBIN) {$this->pastebin($code);} else {$this->download($code);}
		}
		else {
			return false;
		}
	}
}
?>