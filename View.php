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

    private function get_filename($filename){
        $parts = explode('::', $filename);
        if(count($parts) > 1)
        {
            if(array_key_exists($parts[0], self::$namespaces))
                $filename = self::$namespaces[$parts[0]] . '/' . $parts[1];
        }
        else
        {
            if(array_key_exists('::', self::$namespaces))
                $filename = self::$namespaces['::'] . '/' . $filename;
        }
        return $filename;
    }

    public function render( $filename, $direct_output = true, $dont_compute = false ){
        $filename = $this->get_filename($filename);
        if( file_exists( $filename ) == false ) {
            return false;
        }

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

    // view namespaces, used to map path to alias
    protected static $namespaces = array();

    public static function namespace($path, $namespace = null){
        if($namespace == null)
            $namespace = '::';
        self::$namespaces[$namespace] = $path;
    }

    public static function make( $filename, $params = array(), $direct_output = true, $dont_compute = false ){
        $view = new View( $params );
        return $view->render( $filename, $direct_output, $dont_compute );

    }

    // end
    public function __destruct(){}
}

?>
