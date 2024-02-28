<?php
require '../Manage/Product.php';
// Definisci un array associativo per mappare le route
$routes = ['GET' => [], 'POST' => [], 'PUT' => [], 'DELETE' => []];

// Funzione per aggiungere una route
function addRoute($method, $path, $callback)
{
    global $routes;
    $routes[$method][$path] = $callback;
}

// Funzione per ottenere il metodo della richiesta HTTP
function getRequestMethod()
{
    return $_SERVER['REQUEST_METHOD'];
}

// Funzione per ottenere il percorso richiesto
function getRequestPath()
{
    $path = $_SERVER['REQUEST_URI'];
    $path = parse_url($path, PHP_URL_PATH);
    return rtrim($path, '/');
}

// Funzione per gestire la richiesta
function handleRequest()
{
    global $routes;

    $method = getRequestMethod();
    $path = getRequestPath();

    // Verifica se esiste una route per il metodo e il percorso richiesti
    if (isset($routes[$method])) {
        foreach ($routes[$method] as $routePath => $callback) {
            // Verifica se il percorso richiesto corrisponde al percorso della route
            if (preg_match('#^' . $routePath . '$#', $path, $matches)) {
                // Chiamata al callback passando l'ID come parametro
                call_user_func_array($callback, $matches);
                return;
            }
        }
    }

    // Ritorna un errore 404 se la route non è stata trovata
    http_response_code(404);
    echo "404 Not Found";
}

// Aggiungi le tue route qui
addRoute('GET', '/products/(\d+)', function ($id) {
    // Trova il prodotto dal database utilizzando il metodo statico Find della classe Product
    $id = explode('/', $id);
    $id = $id[count($id) - 1];
    $product = Product::Find($id);


    if ($product) {
        header("Location: /products/" . $id);
        header('HTTP/1.1 200 ok');
        header('Content-Type: application/vnd.api+json');
        // Costruisci la risposta JSON conforme alla JSON API
        $data = ['type' => 'products', 'id' => $product->getId(), 'attributes' => ['nome' => $product->getNome(), 'marca' => $product->getMarca(), 'prezzo' => $product->getPrezzo()]];
        $response = ['data' => $data];

        // Restituisci la risposta JSON
        echo json_encode($response, JSON_PRETTY_PRINT);
    } else {
        header('HTTP/1.1 404 not found');
        header('Content-Type: application/vnd.api+json');
        // Ritorna un errore 404 se il prodotto non è stato trovato
        http_response_code(404);
        echo json_encode(['error' => 'Prodotto non trovato']);
    }
});

addRoute('GET', '/products', function () {
    // Recupera tutti i prodotti dal database utilizzando il metodo statico FetchAll della classe Product
    $products = Product::FetchAll();

    // Costruisci la risposta JSON conforme alla JSON API
    $data = [];
    foreach ($products as $product) {
        $data[] = [
            'type' => 'products',
            'id' => $product->getId(),
            'attributes' => [
                'nome' => $product->getNome(),
                'prezzo' => $product->getPrezzo(),
                'marca' => $product->getMarca()
            ]
        ];
    }
    header("Location: /products");
    header('HTTP/1.1 200 OK');
    header('Content-Type: application/vnd.api+json');
    $response = ['data' => $data];

    // Restituisci la risposta JSON
    echo json_encode($response, JSON_PRETTY_PRINT);
});


addRoute('POST', '/products', function () {

    $data = [];
    if (isset($_POST['data']))
        $postData = $_POST;
    else
        $postData = json_decode(file_get_contents("php://input"), true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($postData['data']['attributes']['marca'], $postData['data']['attributes']['nome'], $postData['data']['attributes']['prezzo'])) {
        $newProduct = Product::Create($postData["data"]["attributes"]);
        $data =
            [
                'type' => 'products',
                'id' => $newProduct->getId(),
                'attributes' =>
                    [
                        'nome' => $newProduct->getNome(),
                        'marca' => $newProduct->getMarca(),
                        'prezzo' => $newProduct->getPrezzo()
                    ]
            ];

        $response = ['data' => $data];
        echo json_encode($response, JSON_PRETTY_PRINT);
        header("Location: /products");
        header('HTTP/1.1 201 CREATED');
        http_response_code(201);
        header('Content-Type: application/vnd.api+json');
    } else {
        header("Location: /products");
        header('HTTP/1.1 500 INTERNAL SERVER ERROR');
        header('Content-Type: application/vnd.api+json');
        http_response_code(500);
        echo json_encode(['error' => 'Errore nella creazione del prodotto']);
    }

});

addRoute('PATCH', '/products/(\d+)', function ($id) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = explode('/', $id);
    $id = $id[count($id) - 1];
    $p = Product::Find($id);

    if ($p && $data) {
        $update = $p->Update($data["data"]["attributes"]);
        $dataProduct = ['type' => 'products', 'id' => $update->getId(), 'attributes' => ['nome' => $update->getNome(), 'marca' => $update->getMarca(), 'prezzo' => $update->getPrezzo()]];
        // Costruisci la risposta JSON conforme alla JSON API
        $response = ['data' => $dataProduct];


        header("Location: /products/" . $id);
        header('HTTP/1.1 200 OK');
        header('Content-Type: application/vnd.api+json');
        echo json_encode($response, JSON_PRETTY_PRINT);
    } else {
        header('HTTP/1.1 404 NOT FOUND');
        header('Content-Type: application/vnd.api+json');
        // Ritorna un errore 404 se il prodotto non è stato trovato
        http_response_code(404);
        echo json_encode(['error' => 'Prodotto non trovato']);
    }

});

addRoute('DELETE', '/products/(\d+)', function ($id) {

    $id = explode('/', $id);
    $id = $id[count($id) - 1];
    $p = Product::Find($id);

    if ($p) {
        if ($p->Delete()) {
            header("Location: /products/(\d+)");
            header('HTTP/1.1 204 NO CONTENT');
            header('Content-Type: application/vnd.api+json');
            http_response_code(204);
        } else {
            header("Location: /products/(\d+)");
            header('HTTP/1.1 500 INTERNAL SERVER ERROR');
            header('Content-Type: application/vnd.api+json');
            http_response_code(500);
            echo json_encode(['error' => 'Errore durante l\'eliminazione del prodotto']);

        }
    } else {
        header('HTTP/1.1 404 NOT FOUND');
        header('Content-Type: application/vnd.api+json');
        // Ritorna un errore 404 se il prodotto non è stato trovato
        http_response_code(404);
        echo json_encode(['error' => 'Prodotto non trovato']);
    }
});

// Esegui il gestore della richiesta
handleRequest();
