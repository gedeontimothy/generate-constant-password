<?php

namespace Core\Mail;

class Foundation
{
	public readonly string $type;

	protected $mail_object;

	public function __construct(string $type = null)
	{
		$this->type = is_null($type) ? env('mailer-default') : ucfirst($type);

		$class_ = '\\Core\\Mail\\' . $this->type;

		$settings = env('mailer', [])[$this->type];

		if(class_exists($class_)){
			$this->mail_object = new $class_($settings);
		}
	}

	public function __set($method, $value)
	{
		if(method_exists($this->mail_object, $method)){
			$this->mail_object->$method(...$value);
		}
		else {
			throw new \Exception("La méthode `$method` n'existe pas dans la classe `" . get_class($this->mail_object) . "`", 1);
		}
	}
}
