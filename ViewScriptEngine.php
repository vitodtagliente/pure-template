<?php

namespace Pure\View;

class ViewScriptEngine extends ViewEngine {

	// Map php functions and variables

	function map( $__pure_view_content, $__pure_view_params = array() ){

		$__pure_view_rules = $this->findRules( $__pure_view_content, '{{', '}}' );

        foreach ($__pure_view_rules as $__pure_view_rule) {
        	// trim each rule
            $__pure_view_r = str_replace( '{{', '', $__pure_view_rule );
            $__pure_view_r = str_replace( '}}', '', $__pure_view_r );
            $__pure_view_r = trim( $__pure_view_r );

            $__pure_words_count = str_word_count( $__pure_view_r );

            if( $__pure_words_count == 1 && strpos($__pure_view_r, '$') === 0 ){
            	// it's a single word, one variable
            	// try to replace it using eval
            	$__pure_view_r = rtrim( $__pure_view_r, ';' );

            	$__pure_view_value = eval("return $__pure_view_r;");

            	// if eval fails
				// try to find a match with view's params
				if ($__pure_view_value == null){

					$__pure_view_r = ltrim( $__pure_view_r, '$' );

					foreach ($__pure_view_params as $__pure_key => $__pure_value) {
						if($__pure_key == $__pure_view_r)
							$__pure_view_value = $__pure_value;
					}
				}
            }
            else {
            	// eval the rule
            	// TODO: exception handler
				//$__pure_view_value = eval($__pure_view_r);
            }

            $__pure_view_content = str_replace( $__pure_view_rule, $__pure_view_value, $__pure_view_content );
        }
        return $__pure_view_content;

	}
}

?>
