<?php

class SmartGPT
{
	private array $usage = ['promptTokens' => 0, 'completionTokens' => 0, 'totalTokens' => 0];

	function __construct(string $prompt)
	{
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
		for ($i = 0; $i < 8; $i++) $messagess[] = $messages;
		echo ("Generating Guides...(0/8)...");
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
							$messages[] = ["role" => "user", "content" => "Without asking for any additional User Input;\n" .
								"To the best of your abilities I would like you to:\n" .
								$choice['message']['content'] .
								"\nProvide just the response without any cognitive distortions"];
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
		echo ("Generating Responses...(0/8)...");
		$responses = $bunnyai->get($bunnyai->build_prompts($messagess));
		$response_count = 0;
		$response_total = count($responses);
		$messages = [];
		$messages[] = $base_input;
		foreach ($responses as $response) {
			$response_count++;
			if (isset($response['response'])) {
				if (isset($response['response']['choices'])) {
					foreach ($response['response']['choices'] as $choice) {
						if (isset($choice['message']) && isset($choice['message']['content'])) {
							$messages[] = ["role" => "user", "content" => "Possible Response $response_count of $response_total: " . $choice['message']['content']];
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
			echo ("\rGenerating Responses...($response_count/$response_total)...");
		}
		echo ("done.\n");
		$base_messages = $messages;
		$messages[] = ["role" => "user", "content" => "Imagine you are a devil's advocate who is tasked with critisizing these Possible Responses,\n" .
			"Identify any errors, inconsistencies, nuances, caveats, edge cases not included, and/or cognitive distortions.\n" .
			"Use your outside-the-box Critical Thinking skills.  Keep your response as short and concise as possible while still hitting all the important details."];
		$messagess = [];
		for ($i = 0; $i < 4; $i++) $messagess[] = $messages;
		echo ("Playing Devil's Advocate...(0/4)...");
		$responses = $bunnyai->get($bunnyai->build_prompts($messagess));
		$response_count = 0;
		$response_total = count($responses);
		$messages = $base_messages;
		foreach ($responses as $response) {
			$response_count++;
			if (isset($response['response'])) {
				if (isset($response['response']['choices'])) {
					foreach ($response['response']['choices'] as $choice) {
						if (isset($choice['message']) && isset($choice['message']['content'])) {
							$messages[] = ["role" => "user", "content" => "Devil's Advocate $response_count of $response_total: " . $choice['message']['content']];
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
			echo ("\rPlaying Devil's Advocate...($response_count/$response_total)...");
		}
		echo ("done.\n");
		print_r($responses);
		print_r($messages);
		print_r($this->usage);
	}
}
