<?php
require_once __DIR__ . '/../Manage/Product.php';

use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testFind()
    {
        $product = Product::Find(4);
        $this->assertInstanceOf(Product::class, $product);
    }

    public function testCreate()
    {
        $params = array(
            'nome' => 'Test Product',
            'prezzo' => 10.99,
            'marca' => 'Test Brand'
        );

        $product = Product::Create($params);
        $this->assertInstanceOf(Product::class, $product);

        // Clean up: Delete the created product
        $product->Delete();
    }

    public function testDelete()
    {
        $params = array(
            'nome' => 'Test Product',
            'prezzo' => 10.99,
            'marca' => 'Test Brand'
        );

        $product = Product::Create($params);
        $this->assertTrue($product->Delete());
    }

    public function testUpdate()
    {
        $params = array(
            'nome' => 'Test Product',
            'prezzo' => 10.99,
            'marca' => 'Test Brand'
        );

        $product = Product::Create($params);

        $updatedParams = array(
            'nome' => 'Updated Test Product',
            'prezzo' => 19.99,
            'marca' => 'Updated Test Brand'
        );

        $updatedProduct = $product->Update($updatedParams);
        $this->assertInstanceOf(Product::class, $updatedProduct);

        // Clean up: Delete the created and updated product
        $updatedProduct->Delete();
    }

    public function testFetchAll()
    {
        $products = Product::FetchAll();
        $this->assertIsArray($products);
        $this->assertNotEmpty($products);
        foreach ($products as $product) {
            $this->assertInstanceOf(Product::class, $product);
        }
    }
}
?>
