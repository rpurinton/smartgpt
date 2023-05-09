<?php

class SmartGPT
{
	function __construct(string $prompt)
	{
		echo("Prompt: $prompt\n");
		require_once(__DIR__."/BunnyAI.php");
		$bunnyai = new BunnyAI;
		print_r($bunnyai);
	}
}
