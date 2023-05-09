<?php

class SmartGPT
{
	function __construct(string $prompt)
	{
		echo ("Prompt: $prompt\n");
		require_once(__DIR__ . "/BunnyAI.php");
		$bunnyai = new BunnyAI;
		$prompts = $bunnyai->build_prompts([["role" => "user", "content" => $prompt]]);
		$responses = $bunnyai->get($prompts);
		print_r($responses);
	}
}
