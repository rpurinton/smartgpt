<?php

class SmartGPT
{
	private array $usage = ['promptTokens' => 0, 'completionTokens' => 0, 'totalTokens' => 0];

	function __construct(string $prompt)
	{
		echo ("Prompt: $prompt\n");
		require_once(__DIR__ . "/BunnyAI.php");
		$bunnyai = new BunnyAI;
		$messages[] = ["role" => "user", "content" => "User Input: \"$prompt\""];
		$messages[] = ["role" => "user", "content" => "Generate a step-by-step guide to formulating a response to the User input, without being able to request any additional User Input. Give just the final steps, without any examples, explanations, or cognitive distortions."];
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
