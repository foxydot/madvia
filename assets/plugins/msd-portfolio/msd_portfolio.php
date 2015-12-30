<?php
/*
Plugin Name: MSD Portfolios
Description: Portfolio plugin.
Author: Catherine Sandrick
Version: 0.1
Author URI: http://madsciencedept.com
*/
$msd_portfolio_path = plugin_dir_path(__FILE__);
$msd_portfolio_url = plugin_dir_url(__FILE__);
/*
 * Pull in some stuff from other files
 */
if(!function_exists('requireDir')){
	function requireDir($dir){
		$dh = @opendir($dir);

		if (!$dh) {
			throw new Exception("Cannot open directory $dir");
		} else {
			while($file = readdir($dh)){
				$files[] = $file;
			}
			closedir($dh);
			sort($files);
			foreach($files AS $file){
				if ($file != '.' && $file != '..') {
					$requiredFile = $dir . DIRECTORY_SEPARATOR . $file;
					if ('.php' === substr($file, strlen($file) - 4)) {
						require_once $requiredFile;
					} elseif (is_dir($requiredFile)) {
						requireDir($requiredFile);
					}
				}
			}
		}
		unset($dh, $dir, $file, $requiredFile);
	}
}
requireDir(plugin_dir_path(__FILE__) . '/lib/inc');