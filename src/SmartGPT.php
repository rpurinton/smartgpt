<?php

class SmartGPT
{
	private array $usage = ['promptTokens' => 0, 'completionTokens' => 0, 'totalTokens' => 0];

	function __construct(string $prompt)
	{
		echo ("Prompt: $prompt\n");
		require_once(__DIR__ . "/BunnyAI.php");
		$bunnyai = new BunnyAI;
		$base_input = ["role" => "user", "content" => "User Input: \"$prompt\""];
		$generate_guides = ["role" => "user", "content" => "
		Generate a step-by-step guide that encourages the assistant to think creatively and 
		come up with an innovative solution to formulating a response to the User input,
		without being able to request any additional User Input.
		The guide should be open-ended and allow for multiple interpretations and approaches.
		Give just the final steps, without any examples, explanations, or cognitive distortions."];
		$messages = [];
		$messages[] = $base_input;
		$messages[] = $generate_guides;
		for ($i = 0; $i < 16; $i++) $messagess[] = $messages;
		echo ("Generating Guides...(0/16)...");
		$responses = $bunnyai->get($bunnyai->build_prompts($messagess));
		$response_count = 0;
		$response_total = count($responses);
		$messagess = [];
		foreach ($responses as $response) {
			$response_count++;
			if (isset($response['response'])) {
				if (isset($response['response']['choices'])) {
					foreach ($response['response']['choices'] as $choice) {
						if (isset($choice['message']) && isset($choice['message']['content'])) {
							$messages = [];
							$messages[] = $base_input;
							$messages[] = ["role" => "user", "content" => $choice['message']['content']];
							$messagess[] = $messages;
						}
					}
				}
				if (isset($response['response']['usage'])) {
					if (isset($response['response']['usage']['promptTokens']))
						$this->usage['promptTokens'] += $response['response']['usage']['promptTokens'];
					if (isset($response['response']['usage']['completionTokens']))
						$this->usage['completionTokens'] += $response['response']['usage']['completionTokens'];
					if (isset($response['response']['usage']['totalTokens']))
						$this->usage['totalTokens'] += $response['response']['usage']['totalTokens'];
				}
			}
			echo ("\rGenerating Guides...($response_count/$response_total)...");
		}
		echo ("done.\n");
		print_r($messagess);
		print_r($this->usage);
	}
}
