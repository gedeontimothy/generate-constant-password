<?php

namespace Core\Mail\Support;

interface MailerInterface
{
	public function addContent(string $content) : mixed;

	public function setEmailContent(string $content) : mixed;
}
