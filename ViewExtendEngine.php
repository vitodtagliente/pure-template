<?php

namespace Pure\Template;

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

class ViewExtendEngine extends ViewEngine 
{
	// la seguente vista estende un template
	private $extended = false;
	// sezioni sovrascritte
	private $overrides = array();
	// sezioni definite
	private $sections = array();

	function __construct(){

	}

	function map($text, $params = array())
	{
		// debug purposes
		$this->overrides = ViewUtility::find_rules($text, '@begin', '@end');
		$this->sections = ViewUtility::find_rules($text, '@section', ')');

		// inherit template
		$content = $this->extend_view($text, $params);

		// Map sections
		foreach (ViewUtility::find_rules($text, '@begin', '@end') as $rule) 
		{
			$section_name = null;
			$begin_rule = null;
			foreach (ViewUtility::find_rules($rule, '(', ')') as $s) 
			{
				$begin_rule = "@begin$s";
				$section_name = ltrim($s, '(');
				$section_name = rtrim($section_name, ')');
				$section_name = ViewUtility::trim($section_name);
				// find only the first occurrence
				break;
			}

			if($section_name == null || $begin_rule == null)
				continue;

			$pieces = explode($begin_rule, $rule);
			$pieces = explode('@end', $pieces[1]);

			// Override the section in parent template
			$content = str_replace("@section($section_name)", $pieces[0], $content);
			$content = str_replace("@section('$section_name')", $pieces[0], $content);
			$content = str_replace("@section(\"$section_name\")", $pieces[0], $content);
		}
		return $content;
	}

	// se presente la macro @extends
	// estendi questo template
	private function extend_view($text, $params = array())
	{
		// se presente, procedi con l'estensione
		if(strpos($text, '@extends(') !== false)
		{
			// extract the template name
			$pieces = explode('@extends(', $text);
			$pieces = explode(')', $pieces[1]);
			if(count($pieces) <= 0)
				return $text;
			
			$parent_view = new View(ViewUtility::trim($pieces[0]), $params, false);
			$result = $parent_view->render(false);

			// remove all the extends
			foreach (ViewUtility::find_rules($result, '@extends', ')') as $extend) {
				$result = str_replace($extend, '', $result );
			}

			$this->extended = true;
			return $result;
		}
		return $text;
	}

	function __destruct(){}
}

?>
