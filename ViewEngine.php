<?php

namespace Pure\Template;

abstract class ViewEngine {

	abstract function map( $text );

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
	protected function findRules( $text, $begin, $end )
	{
		$rules = array();

		$temp = $text;
		$pieces = explode( $begin, $temp );
		while( count( $pieces ) > 1 ){
			$pieces = explode( $end, $pieces[1] );

			$rule = $begin . $pieces[0] . $end;
			array_push( $rules, $rule );

			if( count( $pieces ) == 1 )
				break;

			$temp = str_replace( $rule, '', $temp );
			$pieces = explode( $begin, $temp );
		}

		return $rules;
	}

	// explode a string and returns all the arguments found
	// Example: 'foo', 1, 2, 'test'
	// output = ['foo', 1, 2, 'test']
	protected function mapArguments( $text )
	{
		$argv = [];
		if(!empty($text))
		{
			$args = explode( ',', $text );
			foreach ($args as $a) {
				// trim the argument
				$a = trim( $a );
				$a = trim( $a, "'" );
				$a = trim( $a, '"' );
				$a = trim( $a );
				// parse null
				if( strtolower($a) == "null" )
					$a = null;
				array_push( $argv, $a );
			}
		}

		return $argv;
	}

}

?>
