<?php
class Route {
    private $path;
    private $callable;
    private $matches = [];
    private $params = [];
    private $requireAuth = false;
    private $acceptSegments = false;

    public function __construct($path, $callable, $requireAuth = false, $acceptSegments = false){
        $this->path = trim($path, '/');  // On retire les / inutils
        $this->callable = $callable;
        $this->requireAuth = $requireAuth;
        $this->acceptSegments = $acceptSegments;
    }

    public function requireAuth() {
        return $this->requireAuth;
    }

    public function match($url){
        $url = trim($url, '/');
        $path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->path);
        $regex = "#^$path$#i";

        if (!$this->acceptSegments) {
            if (!preg_match($regex, $url, $matches)) {
                return false;
            }
            array_shift($matches);
            $this->matches = $matches;
            return true;
        }

        // Gestion des segments
        $urlSegments = explode('/', $url);
        $routeSegments = explode('/', $this->path);

        if (count($urlSegments) !== count($routeSegments)) {
            return false;
        }

        $segmentCount = count($urlSegments);
        for ($i = 0; $i < $segmentCount; $i++) {
            if ($routeSegments[$i] !== $urlSegments[$i]) {
                return false;
            }
        }

        return true;
    }
    
    private function paramMatch($match){
        if(isset($this->params[$match[1]])){
            return '(' . $this->params[$match[1]] . ')';
        }
        return '([^/]+)';
    }

    public function call(){
        if(is_string($this->callable)){
            $params = explode('#', $this->callable);
            $Controller = "App\\Controller\\" . $params[0] . "Controller";
            $Controller = new $Controller();
            return call_user_func_array([$Controller, $params[1]], $this->matches);
        } else {
            return call_user_func_array($this->callable, $this->matches);
        }
    }

    public function with($param, $regex){
        $this->params[$param] = str_replace('(', '(?:', $regex);
        return $this; // On retourne tjrs l'objet pour enchainer les arguments
    }

    public function getUrl($params){
        $path = $this->path;
        foreach($params as $k => $v){
            $path = str_replace(":$k", $v, $path);
        }
        return $path;
    }
}
?>