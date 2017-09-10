<?php

namespace Pure\View;

/*
	implements the view's extension capability
	Example:
		@extends('filename')
		- filename represents a view to extend

		[NOTE] extends must be the first statement

	In parent View can be defined sections to be override
	with expression: @section('name')

	To override a section, do like follow:
	@begin('name')
	...code...
	@end
*/

class ViewExtendEngine extends ViewEngine {

	private $extended = false;
	private $content = null;
	private $canClear = true;

	function __construct($canClear = true){
		$this->canClear = $canClear;
	}

	// if view contains extends rule, do extension
	private function extendView( $text, $params = array() ){
		if( strpos($text, '@extends(') !== false ){
			// extract the template name
			$pieces = explode( '@extends(', $text );
			$pieces = explode( ')', $pieces[1] );
			if( count( $pieces ) <= 0 )
				return $text;

			$template = trim($pieces[0]);
			$template = trim($template, "'");
			$template = trim($template, '"');

			ob_start();
			View::make($template, $params, true, true);
			$result = ob_get_contents();
			ob_end_clean();

			// remove all the extends
			foreach ($this->findRules( $result, '@extends', ')' ) as $extend) {
				$result = str_replace( $extend, '', $result );
			}

			$this->extended = true;
			return $result;
		}
		return $text;
	}

	// clear the view from the unprocessed rules
	private function clear( $text ){
		$result = $text;
		foreach ($this->findRules($text, '@begin', '@end') as $rule) {
			$result = str_replace($rule, '', $result);
		}
		foreach ($this->findRules($text, '@section', ')') as $rule) {
			$result = str_replace($rule, '', $result);
		}
		return $result;
	}

	function map( $text, $params = array() ){

		// inherit template
		$this->content = $this->extendView( $text, $params );

		if( $this->extended == false && $this->canClear )
			return $this->clear( $text );

		// Map sections
		foreach ($this->findRules($text, '@begin', '@end') as $rule) {
			$section_name = null;
			$begin_rule = null;
			foreach ($this->findRules($rule, '(', ')') as $s) {
				$begin_rule = "@begin$s";
				$section_name = ltrim($s, '(');
				$section_name = rtrim($section_name, ')');
				$section_name = trim($section_name);
				$section_name = trim($section_name, "'");
				$section_name = trim($section_name, '"');
				// find only the first occurrence
				break;
			}

			if( $section_name == null || $begin_rule == null )
				continue;

			$pieces = explode( $begin_rule, $rule );
			$pieces = explode( '@end', $pieces[1] );

			// Override the section in parent template
			$this->content = str_replace("@section($section_name)", $pieces[0], $this->content);
			$this->content = str_replace("@section('$section_name')", $pieces[0], $this->content);
			$this->content = str_replace("@section(\"$section_name\")", $pieces[0], $this->content);
		}

		if( $this->canClear )
			$this->content = $this->clear( $this->content );

		return $this->content;
	}

	function __destruct(){}
}

?>
