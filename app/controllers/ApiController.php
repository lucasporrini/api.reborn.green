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

    public function sold_products()
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];
        
        if($this->apiModel->middleware_auth($token)) {
            // Récupérer les données
            $data = json_decode(file_get_contents('php://input'), true);
            dd($data);
            // Récupérer l'id du produit à partir du slug
            $product = $this->apiModel->get_products_with_conditions(['slug' => $data['slug']])[0];

            $data['product_id'] = $product['id'];
            $data['sold_at'] = date('Y-m-d');

            // On fait la modification en base de données
            $editedProduct = $this->apiModel->sold_products($data);

            if($editedProduct !== null) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => 'Erreur interne']);
                exit;
            } else {
                // Retourner les données en json
                header('Content-Type: application/json');
                http_response_code(200);
                echo json_encode(['success' => 'Produit vendu avec succès'], JSON_UNESCAPED_UNICODE);
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

    public function add_product_from_json()
    {
        // On récupère le token dans le header
        $headers = apache_request_headers();
        $token = $headers['Authorization'];
        
        if($this->apiModel->middleware_auth($token)) {
            $temp = '{
                "title": "Chaise haute ",
                "height": "25",
                "width": "12",
                "depth": "15",
                "weight": "7",
                "brand": "Bébé ",
                "reference": " ",
                "material": "bois",
                "assembly": "",
                "code_article": " ",
                "trust": "4",
                "quantity": "7",
                "description": " ",
                "caption": " ",
                "price_new": " ",
                "price_unite": " ",
                "unite": "",
                "packaging": "1",
                "state": "très bon",
                "carbon_footprint": " ",
                "slug": " ",
                "storage_location": "Chantier",
                "available": "True",
                "location_name": "SG555",
                "location_place": "Croissy beaubourg ",
                "location_adresse": "19 Boulevard Georges bidault"
              }
              ';
            
            $data = json_decode($temp, true);
        
            if(json_last_error() !== JSON_ERROR_NONE) {
                dd('Erreur dans le JSON:' . json_last_error_msg());
            }

            // Récupérer les données
            // $data = json_decode(file_get_contents('php://input'), true);
            dd($data);
            // On stocke les données dans un tableau qui sera envoyé à la base de données
            $item_data = [];
            $data['title'] ? $item_data['title'] = $data['title'] : $item_data['title'] = null;
            $item_data['title'] ? $item_data['slug'] = slugify($data['title']) : $item_data['slug'] = null;
            $data['height'] ? $item_data['height'] = $data['height'] : $item_data['height'] = null;
            $data['width'] ? $item_data['width'] = $data['width'] : $item_data['width'] = null;
            $data['depth'] ? $item_data['depth'] = $data['depth'] : $item_data['depth'] = null;
            $data['weight'] ? $item_data['weight'] = $data['weight'] : $item_data['weight'] = null;
            $data['brand'] ? $item_data['brand'] = $data['brand'] : $item_data['brand'] = null;
            $data['reference'] ? $item_data['reference'] = $data['reference'] : $item_data['reference'] = null;
            $data['material']['Value'] ? $item_data['material'] = $data['material']['Value'] : $item_data['material'] = null;
            $data['assembly'] ? $item_data['assembly'] = $data['assembly'] : $item_data['assembly'] = null;
            $data['code_article'] ? $item_data['code_article'] = $data['code_article'] : $item_data['code_article'] = null;
            $data['trust']['Value'] ? $item_data['trust'] = $data['trust']['Value'] : $item_data['trust'] = null;
            $data['quantity'] ? $item_data['quantity'] = $data['quantity'] : $item_data['quantity'] = null;
            $data['description'] ? $item_data['description'] = $data['description'] : $item_data['description'] = null;
            $data['caption'] ? $item_data['caption'] = $data['caption'] : $item_data['caption'] = null;
            $data['price_new'] ? $item_data['price_new'] = $data['price_new'] : $item_data['price_new'] = null;
            $data['price_unite'] ? $item_data['price_unite'] = $data['price_unite'] : $item_data['price_unite'] = null;
            $data['unite']['Value'] ? $item_data['unite'] = $data['unite']['Value'] : $item_data['unite'] = null;
            $data['packaging'] ? $item_data['packaging'] = $data['packaging'] : $item_data['packaging'] = null;
            $data['state']['Value'] ? $item_data['state'] = $data['state']['Value'] : $item_data['state'] = null;
            $data['carbon_footprint'] ? $item_data['carbon_footprint'] = $data['carbon_footprint'] : $item_data['carbon_footprint'] = null;
            $data['photos'] = null;
            $data['category_id'] = null;
            $data['storage_location'] ? $item_data['storage_location'] = $data['storage_location'] : $item_data['storage_location'] = null;
            $data['location_id'] = null;
            $data['available'] ? $item_data['available'] = $data['available'] : $item_data['available'] = 0;
            $data['availability_date'] = null;
            $item_data['active'] = 0;
            $item_data['booked'] = 0;
            $item_data['created_at'] = date('Y-m-d');
            $data['sold'] ? $item_data['sold'] = $data['sold'] : $item_data['sold'] = null;
            $data['sold_at'] ? $item_data['sold_at'] = $data['sold_at'] : $item_data['sold_at'] = null;

            dd($item_data);


            // On fait l'ajout en base de données
            $addedProduct = $this->apiModel->add_product($item_data);
            
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
