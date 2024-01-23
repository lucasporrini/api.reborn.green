<?php
class Router {
    private $url;
    private $routes = [];
    private $namedRoutes = [];

    public function __construct($url){
        $this->url = $url;
    }

    public function get($path, $callable, $requireAuth=false, $name = null){
        return $this->add($path, $callable,  $name, 'GET', $requireAuth);
    }

    public function post($path, $callable, $requireAuth=false, $name = null){
        return $this->add($path, $callable, $name, 'POST', $requireAuth);
    }

    private function add($path, $callable, $name, $method, $requireAuth){
        $route = new Route($path, $callable, $requireAuth);
        $this->routes[$method][] = $route;
        if(is_string($callable) && $name === null){
            $name = $callable;
        }
        if($name){
            $this->namedRoutes[$name] = $route;
        }
        return $route;
    }

    private function isUserAuthenticated() {
        // Vérifier si l'utilisateur est connecté en vérifiant les informations d'identification dans la session
        if (!isset($_SESSION['user']['logged_in']) || $_SESSION['user']['logged_in'] === false) {
            return false;
        }

        return true;
    }

    public function run(){
        if(!isset($this->routes[$_SERVER['REQUEST_METHOD']])){
            throw new Exception('REQUEST_METHOD does not exist');
        }
        $routeFound = false;
        foreach($this->routes[$_SERVER['REQUEST_METHOD']] as $route){
            if($route->match($this->url)){
                $routeFound = true;

                // Vérifier si la route à besoin du middleware
                $requireAuth = $route->requireAuth();

                // Vérifier l'authentification uniquement pour les routes nécessitant une authentification
                if ($requireAuth && !$this->isUserAuthenticated()) {
                    // L'utilisateur n'est pas authentifié, vous pouvez gérer le cas ici
                    // Par exemple, rediriger vers la page de connexion ou renvoyer une réponse JSON d'erreur
                    header('Location: /admin/login');
                    exit;
                }

                return $route->call();
            }
        }
        // throw new Exception('No matching routes');
        if(!$routeFound){
            $this->notFound();
        }
    }

    public function url($name, $params = []){
        if(!isset($this->namedRoutes[$name])){
            throw new Exception('No route matches this name');
        }
        return $this->namedRoutes[$name]->getUrl($params);
    }

    public function notFound()
    {
        header("HTTP/1.0 404 Not Found");
        header('Location: /404');
    } 
}