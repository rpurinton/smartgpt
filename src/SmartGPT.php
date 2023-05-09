<?php

class SmartGPT
{
	private array $usage = ['promptTokens' => 0, 'completionTokens' => 0, 'totalTokens' => 0];

	function __construct(string $prompt)
	{
		echo ("Prompt: $prompt\n");
		require_once(__DIR__ . "/BunnyAI.php");
		$bunnyai = new BunnyAI;
		$messages[] = ["role" => "user", "content" => $prompt];
		$messages[] = ["role" => "user", "content" => "Generate a step-by-step guide to address the user input based on the information provided."];
		for ($i = 0; $i < 16; $i++) {
			$messagess[] = $messages;
		}
		$responses = $bunnyai->get($bunnyai->build_prompts($messagess));
		foreach ($responses as $response) {
			$this->usage['promptTokens'] += $response['response']['usage']['promptTokens'];
			$this->usage['completionTokens'] += $response['response']['usage']['completionTokens'];
			$this->usage['totalTokens'] += $response['response']['usage']['totalTokens'];
		}
		print_r($responses);
		print_r($this->usage);
	}
}
