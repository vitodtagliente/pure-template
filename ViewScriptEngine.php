<?php

namespace Pure\Template;

class ViewScriptEngine extends ViewEngine {

	// Map php functions and variables
	function map($view_content, $view_params = array())
	{
		// trova tutte le regole racchiuse tra doppie graffe
		$rules = ViewUtility::find_rules($view_content, '{{', '}}');

        foreach ($rules as $rule) 
        {
        	// estrapola la parte di codice racchiusa
            $rule_code = str_replace('{{', '', $rule);
            $rule_code = str_replace('}}', '', $rule_code);
            $rule_code = trim($rule_code);
			$rule_code = trim($rule_code, ';');

			// memorizza qui l'esito dell'elaborazione
			$rule_code_value = null;

			// se è una sola parola
			// presumibilmente è una variabile
			// in tal caso, esegui la stampa del valore
			if(strpos($rule_code, '$') === 0)
			{
				// cerca tra i parametri della vista
				if ($rule_code_value == null)
				{
					$rule_code_variable = ltrim($rule_code, '$');
					foreach ($view_params as $param_key => $param_value) 
					{
						if($param_key == $rule_code_variable)
						{
							$rule_code_value = $param_value;
							break;
						}
					}
				}
			}
			else
			{
				// si tratta di una funzione presumibilmente
				$rule_code_value = eval("$rule_code;");
				if($rule_code_value == null)
				{
					// it is a void function
					$rule_code_value = eval("return $rule_code;");
				}
			}

            $view_content = str_replace($rule, $rule_code_value, $view_content);
        }
        return $view_content;

	}
}

?>
