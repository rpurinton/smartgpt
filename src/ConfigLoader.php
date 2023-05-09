<?php

require_once(__DIR__ . "/vendor/autoload.php");

class ConfigLoader
{
	public $config = null;
	function __construct()
	{
		exec("ls " . __DIR__ . "/conf.d/*.conf", $configfiles);
		foreach ($configfiles as $configfile) {
			$section = substr($configfile, 0, strpos($configfile, ".conf"));
			$section = substr($section, strpos($section, "conf.d/") + 7);
			$this->config[$section] = json_decode(file_get_contents($configfile), true);
		}
	}
}
