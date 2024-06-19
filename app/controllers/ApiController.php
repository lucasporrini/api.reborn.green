<?php

// Inclure les modèles
require_once RELATIVE_PATH_MODELS . '/ApiModel.php';

class ApiController
{
    private $apiModel;

    public function __construct() {
        $this->apiModel = new ApiModel();
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
        echo 'Welcome to the Reborn API ! 😊';
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

    public function get_category_by_id($id)
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];

        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $category = $this->apiModel->get_category_by_id($id);

            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($category, JSON_UNESCAPED_UNICODE);
        }
    }

    public function get_category_by_slug($slug)
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];
        
        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $category = $this->apiModel->get_category_by_slug($slug);

            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($category, JSON_UNESCAPED_UNICODE);
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

    public function get_clients()
    {
        // On récupère les données
        $headers = apache_request_headers();
        $token = $headers['Authorization'];

        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $clients = $this->apiModel->get_clients();

            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($clients, JSON_UNESCAPED_UNICODE);
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

    public function add_product()
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];
        
        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $data = json_decode(file_get_contents('php://input'), true);
            $data['created_at'] = date('Y-m-d');

            // On fait l'ajout en base de données
            $addedProduct = $this->apiModel->add_product($data);
            
            if($addedProduct == null) {
                header('Content-Type: application/json');
                http_response_code(500);
                $json = ['error' => 'Erreur interne'];
                echo json_encode($json, JSON_UNESCAPED_UNICODE);
                exit;
            } else {
                // Retourner les données en json
                header('Content-Type: application/json');
                http_response_code(200);
                echo json_encode(['success' => 'Produit ajouté avec succès', 'id' => $addedProduct], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
    }

    public function add_product_from_app()
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];
        
        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $data = json_decode(file_get_contents('php://input'), true);
            
            dd($data);

            // On fait l'ajout en base de données
            $addedProduct = $this->apiModel->add_product($data);
            
            if($addedProduct == null) {
                header('Content-Type: application/json');
                http_response_code(500);
                $json = ['error' => 'Erreur interne'];
                echo json_encode($json, JSON_UNESCAPED_UNICODE);
                exit;
            } else {
                // Retourner les données en json
                header('Content-Type: application/json');
                http_response_code(200);
                echo json_encode(['success' => 'Produit ajouté avec succès', 'id' => $addedProduct], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
    }

    public function sales()
    {
        // Afficher les données
        $headers = apache_request_headers();
        $token = $headers['Authorization'];

        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $data = json_decode(file_get_contents('php://input'), true);

            foreach($data['items'] as $item) {
                $product = $this->apiModel->get_products_with_conditions(['slug' => $item['slug']])[0];

                if($product['quantity'] < $item['quantity']) {
                    write_log('sales', 'Error', 'Quantité insuffisante', 'red');
                    header('Content-Type: application/json');
                    http_response_code(500);
                    echo json_encode(['error' => 'Quantité insuffisante'], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                $soldProduct = $this->apiModel->sold_products(['client_id' => $data['clientId'], 'product_id' => $product['id'], 'quantity' => $item['quantity'], 'sold_at' => date('Y-m-d')]);
                $decreaseQuantityProduct = $this->apiModel->decrease_product_quantity($product['id'], $item['quantity']);
                write_log('sales', 'Success', 'Vente effectuée avec succès', 'green');
            }

            // Retourner les données en json
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode(['success' => 'Vente effectuée avec succès'], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    public function get_users()
    {
        // On récupère les données
        $headers = apache_request_headers();
        $token = $headers['Authorization'];

        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $users = $this->apiModel->get_users();

            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($users, JSON_UNESCAPED_UNICODE);
        }
    }

    public function get_sales()
    {
        // Afficher les données
        $headers = apache_request_headers();
        $token = $headers['Authorization'];

        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $data = json_decode(file_get_contents('php://input'), true);

            $sales = $this->apiModel->get_sales();

            // Pour chaque vente on récupère le produit et l'acheteur
            foreach($sales as $key => $sale) {
                $product = $this->apiModel->get_products_with_conditions(['id' => $sale['product_id']])[0];
                $client = $this->apiModel->get_clients($sale['client_id'])[0];

                $sales[$key]['product'] = $product;
                $sales[$key]['client'] = $client;
            }

            // Retourner les données en json
            header('Content-Type: application/json');
            echo json_encode($sales, JSON_UNESCAPED_UNICODE);
        }
    }

    public function edit_product($slug)
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];
        
        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $data = json_decode(file_get_contents('php://input'), true);
            
            // On fait la modification en base de données
            $editedProduct = $this->apiModel->edit_product($slug, $data);

            if($editedProduct !== null) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Erreur interne']);
                exit;
            } else {
                // Retourner les données en json
                header('Content-Type: application/json');
                http_response_code(200);
                echo json_encode(['success' => 'Produit modifié avec succès'], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
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
                exit;
            }
        }
    }
}
