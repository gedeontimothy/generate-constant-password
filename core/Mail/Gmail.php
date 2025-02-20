<?php

namespace Core\Mail;

use Core\Mail\Support\MailerInterface;
use Google\Client;
use Google\Service\Gmail as BaseGmail;

class Gmail implements MailerInterface
{

	protected Client $client;

	protected string $email_content;

	protected string $from;

	protected string $to;

	public function __construct(array $settings)
	{
		$this->client = new Client;

		array_map(function($key, $val){
			if(
				!in_array($key, ['CLIENT_ID', 'CLIENT_SECRET', 'REFRESH_TOKEN']) // required
				// ||
				// empty($val)
			){
				throw new \Exception("Clé '$key' Mail Google manquant ou invalide.", 1);
			}
		}, array_keys($settings), $settings);

		$this->initSetting($settings);

		// $this->from = env('mailer-from', (env('mailer', [])['gmail'] ?? [])['from'] ?? null);
		$this->from = (env('mailer', [])['gmail'] ?? [])['from'] ?? env('mailer-from', null);

		if(is_null($this->from))
			throw new \Exception("Veuillez renseigner le 'mailer-from'", 1);
			

	}

	public function addContent(string $content) : Gmail {
		$this->email_content .= $content;
		return $this;
	}

	public function setEmailContent(string $content) : Gmail {
		$this->email_content = $content;
		return $this;
	}

	protected function initSetting(array $settings) : void {
		
		$this->client->setClientId($settings['CLIENT_ID']);

		$this->client->setClientSecret($settings['CLIENT_SECRET']);

		$this->client->setAccessType($settings['ACCESS_TYPE'] ?? 'offline');

		$this->client->setApprovalPrompt($settings['APPROVAL_PROMPT']?? 'force');

		$this->client->refreshToken($settings['REFRESH_TOKEN']);
	}
}
