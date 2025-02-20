<?php

define('CONFIG', require_once __DIR__ . '/config.php');
define('ENV', file_exists(__DIR__ . '/env.php') ? (require_once __DIR__ . '/env.php') : []);

if(!function_exists('getPlateformName')){

	function getPlateformName() : string {

		if((config('env-exec') === 'cli' || (config('env-exec', 'auto') == 'auto' && is_cli())) && isset($_SERVER['argv'])){

			array_shift($_SERVER['argv']);

			return implode('', $_SERVER['argv']);

		}

		return '';

	}

}


if(!function_exists('crypt_')){
	function crypt_(string $val) {
		$cleSecrete = "ma_cle_secrete_cachee"; // Clé fixe (à garder confidentielle)
		$iv = substr(hash('sha256', $cleSecrete), 0, 16); // Générer un IV fixe à partir de la clé
		
		// Chiffrement avec AES-256-CBC
		$texteChiffre = openssl_encrypt($val, 'AES-256-CBC', $cleSecrete, 0, $iv);
		
		return base64_encode($texteChiffre);
	}
}
if(!function_exists('encrypt')){
	function decrypt(string $val) {
		$cleSecrete = "ma_cle_secrete_cachee"; // Clé fixe (la même que pour le chiffrement)
		$iv = substr(hash('sha256', $cleSecrete), 0, 16); // Générer le même IV pour déchiffrer
		
		// Décodage et déchiffrement
		$texteChiffre = base64_decode($val);
		$texteDechiffre = openssl_decrypt($texteChiffre, 'AES-256-CBC', $cleSecrete, 0, $iv);
		
		return $texteDechiffre;
	}
}
if(!function_exists('generateCharsPass')){
	function generateCharsPass($print = true) {
		$tab = $result = [];

		for ($i = 65; $i <= 90; $i++) {
			
			
			do{

				$nbr = random_int(0, 99);

			} while(in_array($nbr, $tab));

			if($print)
				echo chr($i) . ' ' . ($nbr < 10 ? '0' : '') . $nbr . "\n";
			
			$tab[] = $nbr;

			$result[chr($i)] = $nbr;

		}
		
		return $result;
	}
}

if(!function_exists('config')){

	function config(string|int|null $key = null, $default = null) {

		return base_key_manage(CONFIG, $key, $default);

	}

}
if(!function_exists('env')){

	function env(string|int|null $key = null, $default = null) {

		return base_key_manage(ENV, $key, $default);

	}

}
if(!function_exists('base_key_manage')){

	function base_key_manage(array $data, string|int|null $key = null, $default = null) {

		return !is_null($key)
			? (array_key_exists($key, $data)
				? $data[$key]
				: $default
			)
			: $data
		;

	}

}

if(!function_exists('path')){
	function base_path(string $path = '') : string {
		return __DIR__ . (!empty($path) ? '/' . $path : '');
	}
}

if(!function_exists('is_cli')){
	function is_cli(): bool {
		return checkExecutionType('cli');
	}
}

if(!function_exists('checkExecutionType')){
	function checkExecutionType(string $type): bool {
		// Vérification du type d'exécution
		switch ($type) {
			case 'cli':
				// Vérifier si le script est exécuté en ligne de commande (CLI)
				return (php_sapi_name() === 'cli');
			
			case 'web':
				// Vérifier si le script est exécuté via un serveur web
				return (php_sapi_name() !== 'cli');
			
			// Ajoutez d'autres types selon vos besoins
			case 'apache':
				// Vérifier si le script est exécuté sous Apache
				return (php_sapi_name() === 'apache2handler');
			
			case 'cgi':
				// Vérifier si le script est exécuté sous CGI
				return (php_sapi_name() === 'cgi-fcgi');
			
			// Si un type inconnu est passé, on retourne false
			default:
				return false;
		}
	}
}
if(!function_exists('dump')){
	function dump(...$args) : void
	{
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
		$file = $backtrace['file'] ?? 'unknown file';
		$line = $backtrace['line'] ?? 'unknown line';
		var_dump(...$args);
		echo("\033[90m... {$file} on line {$line}\033[0m\n\n");
	}
}

if(!function_exists('dd')){
	function dd(...$args) : void
	{
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
		$file = $backtrace['file'] ?? 'unknown file';
		$line = $backtrace['line'] ?? 'unknown line';
		var_dump(...$args);
		echo("\033[90m... {$file} on line {$line}\033[0m\n\n");
		exit(1); // Arrête l'exécution
	}
}

if(!function_exists('digitalRoot')){
	/**
	 * Calcule la racine numérique (digital root) d'un nombre.
	 * La racine numérique est obtenue en sommant les chiffres d'un nombre
	 * jusqu'à obtenir un seul chiffre (entre 0 et 9).
	 *
	 * @param int|string $number Le nombre à traiter (peut être un entier ou une chaîne de chiffres).
	 * @return int|null Retourne la racine numérique du nombre ou null si l'entrée est invalide.
	 */
	function digitalRoot(int|string $number) : int|null
	{
		if (!is_numeric($number)) {
			return null;
		}

		$number = (int) $number;
		$sum = array_sum(str_split((string) abs($number)));

		return ($sum >= 10) ? digitalRoot($sum) : $sum;
	}
}