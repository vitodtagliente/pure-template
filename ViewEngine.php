<?php

namespace Pure\Template;

abstract class ViewEngine 
{
	abstract function map($text, $params = array());
}

?>
