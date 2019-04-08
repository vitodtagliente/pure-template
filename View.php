<?php

namespace Pure\Template;

class View
{
    // The view's parameters
    private $params = array();
    // gestione dei namespace
    private static $namespaces = array();
    // extend engine
    private $engines = array();
    // nome del file della vista, risolto del relativo namespace
    private $filename = null;
    // se abilitato è possibile finalizzare l'output prodotto
    // ovvero viene ripulito delle sezioni non sovrascritte
    private $can_clear = true;

    public function __construct($filename, $args = array(), $can_clear = true) {
        $this->filename = $this->get_filename($filename);
        $this->set($args);
        $this->can_clear = $can_clear;

        // inizializza i diversi motori di rendering
        $this->engines['extend'] = new ViewExtendEngine();
        $this->engines['script'] = new ViewScriptEngine();
        $this->engines['foreach'] = new ViewForeachEngine();
    }

    public function __destruct(){}

    public function __get ($index) {
        if(array_key_exists($index, $this->params))
            return $this->params[$index];
        return null;        
    }

    public function __set($index, $value){
        if(array_key_exists($index, $this->params) == false)
            $this->params[$index] = $value;
    }

    public function set($args = array()){
        if(is_array($args))
        {
            foreach ($args as $key => $value) 
            {
                $this->params[$key] = $value;
            }
        }
    }

    // specifica di namespace per le path
    public static function namespace($key, $path){
        if(!array_key_exists($key, self::$namespaces))
            self::$namespaces[$key] = $path;
    }

    // check if the view file exists
    public function exists()
    {
        return file_exists($this->filename);
    }

    // I file possono essere definiti per namespace 
    // namespace::filename
    // quindi se il filename contiene '::', occorre
    // identificare il path del namespace e poi 
    // cercare il file li dentro
    // altrimenti il file verrà cercato nella directory
    // specificata
    private function get_filename($filename)
    {
        if (($strpos = strpos($filename, '::')) !== false)
        {
            $parts = explode('::', $filename);
            if(count($parts) > 1)
            {
                if(array_key_exists($parts[0], self::$namespaces))
                    $filename = self::$namespaces[$parts[0]] . '/' . $parts[1];
            }
        }
        else 
        {
            if(isset(self::$namespaces['::']))
                $filename = self::$namespaces['::'] . "/$filename";
        }
        return $filename;
    }

    // genera la vista
    public function render($direct_output = true)
    {
        // controlla che il file esista
        if($this->exists() == false)
            return false;

        // Carica il contenuto e imposta i parametri settati
        // come variabili di contesto
        extract($this->params);
        ob_start();
        include($this->filename);
        $content = ob_get_contents();
        ob_end_clean();    

        // view engines
        foreach($this->engines as $key => $engine)
        {
            $content = $engine->map($content, $this->params);
        }

        // pulisci l'output di eventuali sezioni non sovrascritte
        if($this->can_clear)
        {
            foreach (ViewUtility::find_rules($content, '@section', ')') as $rule) {
                $content = str_replace($rule, '', $content);
            }
        }

        // controlla se occorre produrre a schermo
        // o semplicemente ritornare il contenuto
        if($direct_output)
        {
            echo $content;
            return true;
        }
        else return $content;
    }

    // debug 
    public function debug()
    {
        $content = $this->render(false);
        var_dump($this);
        var_dump($content);
    }

    // funzione statica per la generazione di viste immediate
    public static function make($filename, $params = array(), $direct_output = true){
        $view = new View($filename, $params);
        return $view->render($direct_output);
    }
}

?>
