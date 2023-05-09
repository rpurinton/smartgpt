<?php

require_once(__DIR__ . '/ConfigLoader.php');

class BunnyAI extends ConfigLoader
{
	private $bunny = null;
	private $channel = null;
	function __construct()
	{
		parent::__construct();
		$this->bunny = new \Bunny\Client($this->config['bunny']);
		$this->bunny->connect();
		$this->channel = $this->bunny->channel();
	}
	function __destruct()
	{
		$this->bunny->disconnect();
	}

	public function get(array $messages): array
	{
		$this->channel->qos(0, sizeof($messages));
		$callbackQueue = $this->generate_random_queue_name();
		$this->channel->queueDeclare($callbackQueue);
		foreach ($messages as $key => $message) $this->channel->publish(json_encode(['key' => $key, 'reply-to' => $callbackQueue, 'prompt' => $message], JSON_PRETTY_PRINT), [], '', 'openai_inbox');
		$replies = array();
		$retry = 0;
		while (sizeof($replies) < sizeof($messages) && $retry < 60) {
			$retry++;
			$message = $this->channel->get($callbackQueue);
			if ($message != null) {
				$content = json_decode($message->content, true);
				$replies[$content['key']] = $content;
				$this->channel->ack($message);
			} else sleep(1);
		}
		$this->channel->queueDelete($callbackQueue);
		return $replies;
	}

	public function generate_random_queue_name(): string
	{
		return 'openai_' . bin2hex(random_bytes(16));
	}

	public function build_prompts(array $messages, float $temperature = 0.9, int $max_tokens = 2048, int $top_p = 1, int $n = 1, int $frequency_penalty = 0, int $presence_penalty = 0): array
	{
		$prompts = array();
		$number = 0;
		$total = sizeof($messages);
		foreach ($messages as $key => $message) {
			$number++;
			$prompts[$key] = [
				'model' => 'gpt-3.5-turbo',
				'messages' => $message,
				'temperature' => $this->get_tempreture($number, $total),
				'top_p' => $top_p,
				'n' => $n,
				'stream' => false,
				'frequency_penalty' => $frequency_penalty,
				'presence_penalty' => $presence_penalty,
			];
		}
		return $prompts;
	}

	private function get_tempreture(int $number, int $total): float
	{
		if ($number === 1 && $total === 1) return 0.5;
		if ($number === 1 && $total === 2) return 0;
		if ($number === 2 && $total === 2) return 1;
		return round((1 / ($total - 1)) * ($number - 1), 3);
	}
}
