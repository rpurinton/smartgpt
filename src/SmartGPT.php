<?php

class SmartGPT
{
	function __construct(string $prompt)
	{
		echo ("Prompt: $prompt\n");
		require_once(__DIR__ . "/BunnyAI.php");
		$bunnyai = new BunnyAI;
		$messages[] = ["role" => "user", "content" => $prompt];
		$messagess[] = $messages;
		$messagess[] = $messages;
		$prompts = $bunnyai->build_prompts($messagess);
		$responses = $bunnyai->get($prompts);
		print_r($responses);
	}
}
