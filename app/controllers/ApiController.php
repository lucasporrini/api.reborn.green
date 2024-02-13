<?php

// Inclure les modèles
require_once RELATIVE_PATH_MODELS . '/ApiModel.php';

class ApiController
{
    private $apiModel;

    public function __construct() {
        $this->apiModel = new ApiModel();
    }

    public function github_webhook()
    {
        // Enregistrement du payload dans un fichier
        $payload = file_get_contents('php://input'); 
        $githubSignature = isset($_SERVER['HTTP_X_HUB_SIGNATURE']) ? $_SERVER['HTTP_X_HUB_SIGNATURE'] : '';

        // On vérifie que le secret est présent dans le fichier .env
        $secret = $_ENV['GITHUB_SECRET'];
        if(!$secret) { 
            echo "Le secret n'est pas présent dans le fichier .env";
            return;
        }

        $hash = hash_hmac('sha1', $payload, $secret); // On génère le hash du payload
        
        if (hash_equals('sha1=' . $hash, $githubSignature)) { 
            // On integre un message de validation avec la date et l'heure
            $date = date('d/m/Y H:i:s');
            $data = $date . ' - ' . $payload;

            // On regarde si le script est présent dans le dossier "app/auto"
            if(!file_exists('./app/auto/autodeploy.sh')) {
                file_put_contents('./logs/auto/tracking_deploy.log', "existe pas\n", FILE_APPEND);
                return;
            }

            // On enregistre le payload dans un fichier
            file_put_contents('./logs/auto/payload.log', 'Valid payload:' . $data . ";\n", FILE_APPEND);

            // On verifie que le script est présent dans le dossier "automatic"
            if(!file_exists('./app/auto/autodeploy.sh')) {
                echo "Le script n'est pas présent dans le dossier 'auto'";
                return;
            }

            // On execute le script shell
            shell_exec('./app/auto/autodeploy.sh');

            // On récupère les données du dernier commit pour les enregistrer dans un fichier
            $payload = json_decode($payload, true);
            $lastcommit = $payload['head_commit']['id'] . ' - ' . $payload['head_commit']['message'];

            // On ajoute les données dans tracking_deploy.log 
            file_put_contents('./logs/auto/tracking_deploy.log', 'Success (' .  $date . '): ' . $lastcommit . ";\n", FILE_APPEND);

            // On envoie un mail pour confirmer le déploiement
            $to = DEV_MAIL;
            $subject = "Valid - Déploiement du site";
            $message = "Le site a été déployé avec succès\n\nDernier commit: " . $lastcommit;
            $headers = "From: api.deploy@" . SITE_URL_NAME . "\r\n";
            
            mail($to, $subject, $message, $headers) ? file_put_contents('./logs/auto/mail.log', 'Mail sent (' .  $date . '): ' . $lastcommit . ";\n", FILE_APPEND) : file_put_contents('./logs/auto/mail.log', 'Mail not sent (' .  $date . '): ' . $lastcommit . ";\n", FILE_APPEND);
        } else {
            // La signature n'est pas valide, rejeter la requête
            $date = date('d/m/Y H:i:s');
            $data = $date . ' - ' . $payload;
            file_put_contents('./logs/auto/payload.log', 'Unvalid payload: ' . $data . ";\n", FILE_APPEND);

            // On récupère les données du dernier commit pour les enregistrer dans un fichier
            $payload = json_decode($payload, true);
            $lastcommit = $payload['head_commit']['id'] . ' - ' . $payload['head_commit']['message'];

            // On ajoute les données dans tracking_deploy.log
            file_put_contents('./logs/auto/tracking_deploy.log', 'Error (' .  $date . '): ' . $lastcommit . ";\n", FILE_APPEND);

            // On envoie un mail d'echec
            $to = DEV_MAIL;
            $subject = "Echec - Déploiement du site"; 
            $message = "Le site n'a pu être déployé\n\nDernier commit: " . $lastcommit;
            $headers = "From: api.deploy@" . SITE_URL_NAME . "\r\n";
            
            mail($to, $subject, $message, $headers) ? file_put_contents('./logs/auto/mail.log', 'Mail sent (' .  $date . '): ' . $lastcommit . ";\n", FILE_APPEND) : file_put_contents('./logs/auto/mail.log', 'Mail not sent (' .  $date . '): ' . $lastcommit . ";\n", FILE_APPEND);
        }
    }

    public function check_page($pageName)
    {
        // Récupérer les données
        $page = $this->apiModel->check_page($pageName);

        // Retourner les données en json
        header('Content-Type: application/json');
        echo json_encode($page, JSON_UNESCAPED_UNICODE);
    }

    public function home()
    {
        echo 'Welcome to the API !';
    }

    public function get_header()
    {
        // Récupérer les données
        $menu = $this->apiModel->get_menu();

        // Tri des données
        usort($menu, function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });

        // Retourner les données en json
        header('Content-Type: application/json');
        echo json_encode($menu, JSON_UNESCAPED_UNICODE);
    }

    public function get_header_admin()
    {
         // Récupérer les données
         $menu = $this->apiModel->get_menu_admin();

         // Tri des données
         usort($menu, function ($a, $b) {
             return $a['priority'] <=> $b['priority'];
         });
 
         // Retourner les données en json
         header('Content-Type: application/json');
         echo json_encode($menu, JSON_UNESCAPED_UNICODE);
    }

    public function login()
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];

        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $data = json_decode(file_get_contents('php://input'), true);

            $email = $data['email'];
            $password = $data['password'];

            if(!$email || !$password) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Veuillez renseigner tous les champs']);
                exit;
            }

            $user = $this->apiModel->authenticate_user($email, $password);
            
            if($user) {
                echo json_encode(['success' => 'Vous êtes connecté', 'user' => $user]);
            } else {
                echo json_encode(['error' => 'L\'authentification a échoué']);
            }
        }
    }

    public function get_user($user_id)
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];

        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $user = $this->apiModel->get_user($user_id);

            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($user, JSON_UNESCAPED_UNICODE);
        }
    }

    public function get_products()
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];

        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $products = $this->apiModel->get_products_with_conditions(['available' => 1, 'active' => 1, 'booked' => 0, 'sold' => 0]);
            
            // Si la liste des produits est inférieur à 36
            if(count($products) < 36) {
                // On change les conditions
                $products = $this->apiModel->get_products_with_conditions(['available' => 1, 'active' => 1, 'booked' => 1, 'sold' => 0]);
                
                if(count($products) < 36) {
                    $products = $this->apiModel->get_products();
                }
            }

            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($products, JSON_UNESCAPED_UNICODE);
        }
    }

    public function get_last_fiveteen_products()
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];

        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $product = $this->apiModel->get_last_fiveteen_products();

            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($product, JSON_UNESCAPED_UNICODE);
        }
    }

    public function get_product($slug)
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];

        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $product = $this->apiModel->get_products_with_conditions(['slug' => $slug]);

            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($product, JSON_UNESCAPED_UNICODE);
        }
    }

    public function get_products_by_category($cat_id)
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];
        
        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $products = $this->apiModel->get_products_by_category($cat_id);

            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($products, JSON_UNESCAPED_UNICODE);
        }
    }

    public function get_company()
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];

        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $company = $this->apiModel->get_company();
            
            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($company, JSON_UNESCAPED_UNICODE);
        }
    }

    public function get_categories()
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];

        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $categories = $this->apiModel->get_categories();

            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($categories, JSON_UNESCAPED_UNICODE);
        }
    }

    public function get_categorie_by_id($id)
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];

        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $categories = $this->apiModel->get_categorie_by_id($id);

            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($categories, JSON_UNESCAPED_UNICODE);
        }
    }

    public function get_categorie_by_slug($slug)
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];

        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $categories = $this->apiModel->get_categorie_by_slug($slug);

            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($categories, JSON_UNESCAPED_UNICODE);
        }
    }

    public function get_subcategories()
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];

        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $categories = $this->apiModel->get_subcategories();

            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($categories, JSON_UNESCAPED_UNICODE);
        }
    }

    public function get_locations()
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];
        
        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $locations = $this->apiModel->get_locations();

            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($locations, JSON_UNESCAPED_UNICODE);
        }
    }

    public function get_location_by_id($id)
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];

        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $location = $this->apiModel->get_location_by_id($id);

            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($location, JSON_UNESCAPED_UNICODE);
        }
    }

    public function get_company_address()
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];

        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $company = $this->apiModel->get_company();

            // Récupérer l'adresse de la société
            foreach($company as $object) {
                if($object['parameter'] === 'address') {
                    $company_address = $object['value'];
                }
            };

            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($company_address, JSON_UNESCAPED_UNICODE);
        }
    }

    public function delete_product($slug)
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];
        
        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $deletedProduct = $this->apiModel->delete_product($slug);
            
            if($deletedProduct !== null) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Erreur interne']);
                exit;
            } else {
                // Retourner les données en json
                header('Content-Type: application/json');
                http_response_code(200);
                echo json_encode(['success' => 'Produit supprimé avec succès'], JSON_UNESCAPED_UNICODE);
            }
        }
    }

    public function enable_product($slug)
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];
        
        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $enabledProduct = $this->apiModel->enable_product($slug);
            
            if($enabledProduct !== null) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Erreur interne']);
                exit;
            } else {
                // Retourner les données en json
                header('Content-Type: application/json');
                http_response_code(200);
                echo json_encode(['success' => 'Produit activé avec succès'], JSON_UNESCAPED_UNICODE);
            }
        }
    }

    public function get_structure($table)
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];
        
        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $structure = $this->apiModel->get_structure($table);
            
            if($structure !== null) {
                // Retourner les données en json
                header('Content-Type: application/json');
                http_response_code(200);
                echo json_encode(['success' => 'Structure récupérée avec succès', 'structure' => $structure], JSON_UNESCAPED_UNICODE);
                exit;
            } else {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Erreur interne']);
                exit;
            }
        }
    }

    public function edit_product($slug)
    {
        echo "<pre>";
        print_r($slug);
        echo "</pre>";
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
    }

    public function edit_product_photo($slug)
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];
        
        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $photos = json_decode(file_get_contents('php://input'), true);

            // On fait la modification en base de données
            $editedProduct = $this->apiModel->edit_product_photo($slug, $photos['photos']);

            if($editedProduct !== null) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Erreur interne']);
                exit;
            } else {
                // Retourner les données en json
                header('Content-Type: application/json');
                http_response_code(200);
                echo json_encode(['success' => 'Photo modifiée avec succès'], JSON_UNESCAPED_UNICODE);
            }
        }
    }
}
