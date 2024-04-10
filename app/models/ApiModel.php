<?php

require_once 'lib/config.php';
require_once 'Database.php';

class ApiModel
{
    private $db;
    
    public function __construct()
    {
        global $_CONFIG;
        $this->db = new Database($_CONFIG);
    }

    public function verify_token($token)
    {
        return $this->db->simpleSelect('*', 'token', ['value' => $token]);
    }

    public function middleware_auth($token)
    {
        // On vérifie que le token existe
        if(!isset($token)) {
            write_log('auth', 'Error', 'Bearer token not found', 'red');
            http_response_code(401);
            echo json_encode(['error' => 'Vous devez être connecté pour accéder à cette page']);
            return false;
        }

        // On vérifie que le token est bien un bearer token
        if(isset($token)) {
            $pattern = '/Bearer\s(\S+)/';

            if(preg_match($pattern, $token, $matches)) {
                $token = $matches[1];
                write_log('auth', 'Success', 'Bearer token found', 'green');
            } else {
                write_log('auth', 'Error', 'Bearer token not found', 'red');
                http_response_code(401);
                echo json_encode(['error' => 'L\'authentification a échoué']);
                return false;
            }
        }

        // On vérifie que le token est valide
        $token_in_db = $this->verify_token($token);
        if(!$token_in_db) {
            write_log('auth', 'Error', 'Bearer token not found', 'red');
            http_response_code(401);
            echo json_encode(['error' => 'Le token n\'est pas renseigné ou n\'est pas valide']);
            return false;
        }
        
        if($token_in_db['value'] !== $token) {
            write_log('auth', 'Error', 'Unvalid token', 'red');
            http_response_code(401);
            echo json_encode(['error' => 'L\'authentification a échoué']);
            return false;
        }

        write_log('auth', 'Success', 'Request done', 'green');
        
        return true;
    }

    public function authenticate_user($email, $password)
    {
        $user = $this->db->simpleSelect('*', 'users', ['email' => $email]);
        
        if ($user && password_verify($password, $user['password'])) {
            $user['logged_in'] = true;
            return $user; // L'utilisateur est authentifié avec succès
        } else {
            return false; // Échec de l'authentification
        }
    }

    public function check_page($page_name)
    {
        return $this->db->simpleSelect('*', 'pages', ['page_name' => $page_name]);
    }
    
    public function get_menu()
    {
        return $this->db->select('*', 'menus', ['admin' => 0, 'active' => 1]);
    }

    public function get_menu_admin()
    {
        return $this->db->select('*', 'menus', ['admin' => 1, 'active' => 1]);
    }

    public function get_user($user_id)
    {
        return $this->db->simpleSelect('id, email', 'users', ['id' => $user_id]);
    }

    public function get_products()
    {
        return $this->db->select('*', 'products');
    }

    public function get_products_with_conditions($conditions, $limit = null)
    {
        return $this->db->select('*', 'products', $conditions, $limit);
    }

    public function get_products_by_category($cat_id)
    {
        return $this->db->select('*', 'products', ['category_id' => $cat_id]);
    }

    public function get_last_fiveteen_products()
    {
        return $this->db->select('*', 'products', [], 50);
    }

    public function get_company()
    {
        return $this->db->select('*', 'company');
    }

    public function get_categories()
    {
        return $this->db->select('*', 'categories');
    }

    public function get_categorie_by_id($id)
    {
        return $this->db->simpleSelect('*', 'categories', ['id' => $id]);
    }

    public function get_categorie_by_slug($slug)
    {
        return $this->db->simpleSelect('*', 'categories', ['slug' => $slug]);
    }

    public function get_subcategories()
    {
        return $this->db->select('*', 'subcategories');
    }

    public function get_locations()
    {
        return $this->db->select('*', 'locations', ['active' => 1]);
    }

    public function get_location_by_id($id)
    {
        return $this->db->simpleSelect('*', 'locations', ['id' => $id]);
    }

    public function get_clients()
    {
        return $this->db->select('*', 'clients');
    }

    public function delete_product($slug)
    {
        return $this->db->update('products', ['active' => 0], ['slug' => $slug]);
    }

    public function enable_product($slug)
    {
        return $this->db->update('products', ['active' => 1], ['slug' => $slug]);
    }

    public function get_structure($table)
    {
        $sql = "SHOW COLUMNS FROM " . $table;
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add_product($data)
    {
        return $this->db->insert('products', $data);
    }

    public function edit_product($slug, $data)
    {
        return $this->db->update('products', $data, ['slug' => $slug]);
    }

    public function edit_product_photo($slug, $photos)
    {
        return $this->db->update('products', ['photos' => $photos], ['slug' => $slug]);
    }

    public function sold_products($data)
    {
        return $this->db->insert('sold_products', $data);
    }

    public function decrease_product_quantity($productId, $quantity)
    {
        $product = $this->db->simpleSelect('*', 'products', ['id' => $productId]);
        $newQuantity = $product['quantity'] - $quantity;
        return $this->db->update('products', ['quantity' => $newQuantity], ['id' => $productId]);
    }
}