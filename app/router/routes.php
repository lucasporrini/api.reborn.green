<?php
// Routes de l'application

// Route pour afficher les pages du site
//$router->method('url', [$MainController, 'method_name'], $requireAuth=false, $composedUrl = false);
$router->get('/', [$ApiController, 'home']);
$router->get('/check_page/:page_name', [$ApiController, 'check_page']);
$router->get('/get_header', [$ApiController, 'get_header']);
$router->get(('/get_header_admin'), [$ApiController, 'get_header_admin']);
$router->post('/login', [$ApiController, 'login']);
$router->get('/get_last_fiveteen_products', [$ApiController, 'get_last_fiveteen_products']);

$router->get('/user/:id', [$ApiController, 'get_user']);
$router->get('/products', [$ApiController, 'get_products']);
$router->get('/product/:slug', [$ApiController, 'get_product']);
$router->get('/products/:cat_id', [$ApiController, 'get_products_by_category']);
$router->get('/company', [$ApiController, 'get_company']);
$router->get('/categories', [$ApiController, 'get_categories']);
$router->get('/categorie/:id', [$ApiController, 'get_categorie_by_id']);
$router->get('/categorie/:slug', [$ApiController, 'get_categorie_by_slug']);
$router->get('/subcategories', [$ApiController, 'get_subcategories']);
$router->get('/chantier/:id', [$ApiController, 'get_location_by_id']);
$router->get('/address', [$ApiController, 'get_company_address']);
$router->get('/locations', [$ApiController, 'get_locations']);
$router->get('/location/:id', [$ApiController, 'get_location_by_id']);

$router->get('/clients', [$ApiController, 'get_clients']);

// Route pour gérer les produits
$router->get('/delete-product/:slug', [$ApiController, 'delete_product']);
$router->get('/enable-product/:slug', [$ApiController, 'enable_product']);
$router->get('/get_structure/:table', [$ApiController, 'get_structure']);
$router->post('/add-product', [$ApiController, 'add_product']);
$router->post('/add-product-from-json', [$ApiController, 'add_product_from_json']);
$router->post('/edit-product/:slug', [$ApiController, 'edit_product']);
$router->post('/edit-product-photo/:slug', [$ApiController, 'edit_product_photo']);
$router->post('/add_product_from_app', [$ApiController, 'add_product_from_app']);
$router->post('/sales', [$ApiController, 'sales']);
$router->get('/get_sales', [$ApiController, 'get_sales']);
$router->get('/users', [$ApiController, 'get_users']);
$router->get('/category/:id', [$ApiController, 'get_category_by_id']);
$router->get('/category-slug/:slug', [$ApiController, 'get_category_by_slug']);
$router->post('/edit-user/:id', [$ApiController, 'edit_profile']);
$router->post('/create-user', [$ApiController, 'create_profile']);
$router->post('/delete-user', [$ApiController, 'delete_profile']);

$router->post('/edit-category/:id', [$ApiController, 'edit_category']);
$router->post('/create-category', [$ApiController, 'create_category']);
$router->post('/delete-category', [$ApiController, 'delete_category']);

$router->post('/edit-client/:id', [$ApiController, 'edit_client']);
$router->post('/create-client', [$ApiController, 'create_client']);
$router->post('/delete-client', [$ApiController, 'delete_client']);

$router->get('/chantiers', [$ApiController, 'get_chantiers']);
$router->post('/edit-chantier/:id', [$ApiController, 'edit_chantier']);
$router->post('/create-chantier', [$ApiController, 'create_chantier']);
$router->post('/delete-chantier', [$ApiController, 'delete_chantier']);

$router->post('/edit-company/:id', [$ApiController, 'edit_company']);
$router->post('/create-company', [$ApiController, 'create_company']);
$router->post('/delete-company', [$ApiController, 'delete_company']);

$router->run();