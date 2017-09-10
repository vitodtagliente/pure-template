<?php

namespace Pure\Template;

class View
{
    // The view's parameters
    protected $vars = array();

    public function __construct( $args = array() ) {
        $this->set( $args );
    }

    public function __get ( $index ) {
        return $this->vars[$index];
    }

    public function __set( $index, $value ){
		$this->vars[$index] = $value;
	}

    public function set( $args = array() ){
        if( is_array( $args ) )
            foreach ($args as $key => $value) {
                $this->vars[$key] = $value;
            }
    }

    public function clear(){
        unset( $this->vars );
        $this->vars = array();
    }

    public function render( $filename, $direct_output = true, $dont_compute = false ){
        if( file_exists( self::$path . "/$filename" ) == false ) {
            return false;
        }
        $filename = self::$path . "/$filename";

        // This allow to evaluate params in sections of view
        // where the php tag is used
        extract( $this->vars );
        ob_start();
        include( $filename );
        $content = ob_get_contents();
		ob_end_clean();

        // Map view's inheritance
        $extend_engine = new ViewExtendEngine(!$dont_compute);
        $content = $extend_engine->map( $content, $this->vars );
        if( $dont_compute == false ){
            // Map functions and variables contained between {{ ... }}
            $script_engine = new ViewScriptEngine();
            $content = $script_engine->map( $content, $this->vars );
        }

		if($direct_output)
        	echo $content;
		else return $content;
    }

    // begin static section

	private static $path;

    public static function path($path = null){
        if(isset($path))
            self::$path = $path;
        else return self::$path;
    }

    public static function make( $filename, $params = array(), $direct_output = true, $dont_compute = false ){
        $view = new View( $params );
        return $view->render( $filename, $direct_output, $dont_compute );

    }

    // end
    public function __destruct(){}
}

?>
