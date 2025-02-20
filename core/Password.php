<?php

namespace Core;


class Password {
	
	public readonly Plateform $plateform;

	protected string $prefix = '';

	protected string $suffix = '';

	protected string $infix = '';

	protected bool $cache_password = false;

	protected array $generated = [];
	
	public function __construct(Plateform $plateform, string $prefix = '', string $suffix = '', string|null $infix = null){
		$this->plateform = $plateform;

		$this->setPrefix($prefix);
		$this->setSuffix($suffix);
		$this->setInfix($infix ?? env('password_infix', ''));
	}

	public function cachePassword() : bool {
		return config('cache-password', null) ?? $this->cache_password;
	}

	public function generate(string $password, bool|null $cache = null) : String {
		if(is_null($cache)){
			$cache = $this->cachePassword();
		}
		$pass = $this->getPrefix() . $password . $this->getInfix() . ucfirst($this->plateform->getName()) . $this->getCode() . $this->getSuffix();
		

		$this->generated[] = [
			'pass' => crypt_($pass),
			'date' => date('Y-m-d H:i:s'),
			'base-password' => crypt_($password),
			'name' => $this->plateform->getName(),
			...(empty($this->getSuffix()) ? [] : ['suffix' => $this->getSuffix()]),
			...(empty($this->getPrefix()) ? [] : ['prefix' => $this->getPrefix()]),
			...(empty($this->getInfix()) ? [] : ['infix' => $this->getInfix()]),
		];

		return $pass;
	}
	
	public function __toString()
	{
		return !empty($this->generated) ? current($this->generated)['pass'] : '';
	}

	public function getCode() : string {
		$flc = $this->plateform->getFirstLetterCode();
		$llc = $this->plateform->getLastLetterCode();
		$code = digitalRoot($flc) * digitalRoot($llc);

		// for($i = 26; $i <= 26; $i++) dump($i . ' -> [' . chr($i) . ']');
		// for($i = 35; $i <= 47; $i++) dump($i . ' -> [' . chr($i) . ']');
		// for($i = 91; $i <= 96; $i++) dump($i . ' -> [' . chr($i) . ']');
		// for($i = 123; $i <= 126; $i++) dump($i . ' -> [' . chr($i) . ']');

		$dr = digitalRoot($code);
		return "$code" . ($code < 26 && $dr % 2 != 0 ? '@' : chr(
			($code >= 27 && $code <= 47) || $code < 26
				? 35 + ($dr % 2 == 0 ? $dr + 3 : $dr)
				: ($code >= 48 && $code <= 90
					? 91 + abs($dr > 5 ? ($dr % 2 == 0 ? ($dr - 4) : ($dr - 5)) : $dr)
					: ($code >= 97 && $code <= 122
						? 123 + ($dr > 3 ? $dr - ($dr - 3) : $dr)
						: ($code > 126
							? 126
							: $code
						)
					)
				)
			)
		);
	}

	public function cache() : bool {
		if(!empty($this->generated)){
			$cache = new CacheService($this);
			$cache->getOld();
			foreach ($this->generated as $pass)
				$cache->add($pass);
			return $cache->applyCurrent();
		}
		return false;
	}

	public function getPrefix() : string {
		return $this->prefix;
	}
	public function getSuffix() : string {
		return $this->suffix;
	}
	public function getInfix() : string {
		return $this->infix;
	}

	public function setPrefix(string $prefix) : Password {
		$this->prefix = $prefix;
		return $this;
	}
	public function setSuffix(string $suffix) : Password {
		$this->suffix = $suffix;
		return $this;
	}
	public function setInfix(string $infix) : Password {
		$this->infix = $infix;
		return $this;
	}
	

}
