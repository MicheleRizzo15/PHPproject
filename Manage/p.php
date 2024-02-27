<?php
require_once "Product.php";


$params = [
    "nome" => "nome",
    "prezzo" => 50,
    "marca" => "marca"


];

$prodotto = Product::Create($params);

echo $prodotto->getId();
echo $prodotto->getNome();
echo $prodotto->getMarca();
echo $prodotto->getPrezzo();

$params = [
    "nome" => "nome",
    "prezzo" => 5,
    "marca" => "marca"

];

$prodotto->Update($params);

echo $prodotto->getId();
echo $prodotto->getNome();
echo $prodotto->getMarca();
echo $prodotto->getPrezzo();

$params = [
    "nome" => "nome2",
    "prezzo" => 10,
    "marca" => "marca2"

];

$prodotto = Product::Create($params);

echo $prodotto->getId();
echo $prodotto->getNome();
echo $prodotto->getMarca();
echo $prodotto->getPrezzo();


$prodotto = Product::Find(16);

echo $prodotto->getId();
echo $prodotto->getNome();
echo $prodotto->getMarca();
echo $prodotto->getPrezzo();

$prodotto->Delete();

?>