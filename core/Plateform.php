<?php

namespace Core;

class Plateform
{
	protected string $name = '';
	
	public function __construct(string|array $name)
	{
		$this->setName($name);
	}

	public function inputNameIfEmpty() : bool {

		if($this->isEmptyName()){

			$this->inputName();

			return true;

		}

		return false;

	}

	public function inputName() : Plateform {

		$err = false;

		do{

			if($err) echo "Unvailable Input !";

			$plateform_name = readline("Enter Plateform Name : ");

			$err = true;

		} while(empty($plateform_name));

		return $this->setName($plateform_name);

	}

	public function getName() : string {
		return $this->name;
	}

	public function setName(string|array $name) : Plateform
	{
		$this->name = strtolower(trim(is_string($name) ? $name : implode('', $name)));

		return $this;
	}

	public function isEmptyName() : bool
	{
		return empty($this->name);
	}

	public function getFirstLetter() : string
	{
		return $this->name[0];
	}

	public function getLastLetter() : string
	{
		return substr($this->name, -1);
	}

	public function getFirstLetterCode() : int {
		return $this->getLetterCode($this->getFirstLetter());
	}

	public function getLastLetterCode() : int {
		return $this->getLetterCode($this->getLastLetter());
	}

	public function getLetterCode(string|int $char) : int {
		$chars = config('chars');

		$char_ = array_key_exists($char, $chars) ? $chars[$char] : null;

		if(is_null($char_)){
			$char_ = preg_match('/^[^\w\d]+$/u', $char)
				? -1
				: (preg_match('/^\d+$/', $char)
					? (int) $char
					: -2
				)
			;
		}

		if(is_null($char_))
			throw new \Exception("Les chars configuré sur \"$char\" n'est pas valide.", 1);

		return $char_;
	}
}
