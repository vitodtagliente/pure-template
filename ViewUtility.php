<?php

namespace Pure\Template;

class ViewUtility
{
	private function __construct(){}

	private function __destruct(){}

	// rimuove spazi e apici di entrambi i tipi
	public static function trim($text)
	{
		return trim(trim(trim($text), "'"), '"');
	}

	/*
		Returns an array of all scripts found.
		Example: findRules( text, '{{', '}}' );
		[
			'{{ script1; }}',
			'{{ script2; }}',
				.......
			'{{ scriptn; }}'
		]
	*/
	public static function find_rules($text, $begin, $end)
	{
		$rules = array();

		$temp = $text;
		$pieces = explode($begin, $temp);
		while(count($pieces) > 1){
			$pieces = explode($end, $pieces[1]);

			$rule = $begin . $pieces[0] . $end;
			array_push($rules, $rule);

			if( count($pieces) == 1 )
				break;

			$temp = str_replace($rule, '', $temp);
			$pieces = explode($begin, $temp);
		}

		return $rules;
	}

	// explode a string and returns all the arguments found
	// Example: 'foo', 1, 2, 'test'
	// output = ['foo', 1, 2, 'test']
	public static function extract_arguments($text, $begin = null, $end = null)
	{
		$argv = [];
		if(!empty($text))
		{
			$args = explode(',', $text);
			foreach ($args as $a) {
				// trim the argument
				$a = trim( $this->trim_string($a) );
				// parse null
				if(strtolower($a) == "null")
					$a = null;
				array_push($argv, $a);
			}
		}
		return $argv;
	}
}

?>