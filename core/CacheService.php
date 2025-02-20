<?php

namespace Core;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class CacheService implements IteratorAggregate {

	protected Password|null $password;

	protected readonly Password $mail;

	protected string|null $file_path_cache = null;

	protected string|null $directory_path_cache = null;

	protected array|null $old = null;

	protected array|null $current = null;

	public function __construct(Password|null $password = null)
	{
		$this->password = $password;
		$this->initContent();
	} 

	public function formatMesage() : string
	{
		$date = time();
		return '';
	}

	public function initContent() : void {
		$this->initPath();

		if(!is_file($this->file_path_cache))
			$this->reinitContent();

		if(is_null($this->old))
			$this->setOld(require $this->file_path_cache);

		if(is_null($this->current))
			$this->current = $this->old;
	}

	public function applyCurrent() : bool {
		return $this->applyContent($this->current);
	}

	public function applyContent($val) : bool {
		try {
			file_put_contents($this->file_path_cache, "<?php\nreturn " . var_export($val, true) . ";");
			return true;
		} catch (\Throwable $th) {}
		return false;
	}

	public function initPath() : void {
		if(is_null($this->file_path_cache))
			$this->setFilePathCache(config('cache_file', null) ?? base_path('storage/pass-cache.php'));
	}

	public function setFilePathCache(string $path) : void {
		if(strtolower(pathinfo($path)['extension']) != 'php'){
			trigger_error("[W001] Le fichier cahce `$path` doit être d'extension `.php`, alors elle sera renomé en `$path.php`");
			$path .= '.php';
		}

		$dir = dirname($path);

		if(!empty($dir) && !in_array($dir, ['/', '\\']) && !is_dir($dir))
			mkdir(directory: $dir, recursive: true);			 

		$this->directory_path_cache = $dir;

		$this->file_path_cache = $path;
	}
	
	public static function datas() : array {
		return (new CacheService())->getData();
	}

	public function reinitContent() : void {
		$this->initPath();
		file_put_contents($this->file_path_cache, "<?php\nreturn array(\n\t'pass' => null,);\n");
		$this->setOld(['path' => !empty($this->old) ? ($this->old['path'] ?? null) : null]);
	}

	private function setOld($val) : void {
		$this->old = $val;
		$this->initSecurity();
	}

	private function initSecurity() {
		$val = [...($this->old ?? []), ...($this->current ?? [])];

		if(!isset($val['pass'])) $val['pass'] = null;

		if(empty($val['pass'])){
			$pass = crypt_(env('cache-password', null) ?? readline('Create the secure cache password : '));
			$this->old['pass'] = $pass;
			$this->current['pass'] = $pass;
			$this->applyContent($this->old);
		}
	}

	public function add(array $val, string|null $key = null) {
		if(!is_null($key))
			$this->current[$key] = $val;
		else
			$this->current[] = $val;
	}

	public function getOld() : array|null {
		return $this->old;
		
	}
	public function getCurrent() : array|null {
		return $this->current;
		
	}
	public function getData() : array{
		
		$decrypt = false;
		$res = $this->current;
		$old_pass = $this->old['pass'];
		if(!is_null($old_pass)){
			$pass = crypt_(readline('Cache password : '));
			if($pass == $old_pass && !empty($res)){
				foreach ($res as $key => $value) {
					if($key == 'pass')
						$res[$key] = decrypt($value);
					elseif(is_array($value)){
						$res[$key]['pass'] = decrypt($value['pass']);
						$res[$key]['base-password'] = decrypt($value['base-password']);
					}
				}
			}
		}
		return $res;
	}
	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->getData());
	}
}
