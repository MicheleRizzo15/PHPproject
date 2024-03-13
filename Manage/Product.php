<?php

require_once "Connection.php";

class Product implements JsonSerializable
{
    private $id;
    private $nome;
    private $prezzo;
    private $marca;


    public function getId()
    {
        return $this->id;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;
    }

    public function getPrezzo()
    {
        return $this->prezzo;
    }

    public function setPrezzo($prezzo)
    {
        $this->prezzo = $prezzo;
    }

    public function getMarca()
    {
        return $this->marca;
    }

    public function setMarca($marca)
    {
        $this->marca = $marca;
    }

    public static function Find($id)
    {
        $pdo = self::Connect();
        $stmt = $pdo->prepare("select * from michele_rizzo_ecommerce.products where id = :id");
        $stmt->bindParam(":id", $id);
        if ($stmt->execute()) {
            return $stmt->fetchObject("Product");
        } else {
            return false;
        }
    }

    public static function Create($params)
    {
        $pdo = self::Connect();
        $stmt = $pdo->prepare("insert into michele_rizzo_ecommerce.products (nome,prezzo,marca) values (:nome,:prezzo,:marca)");
        $stmt->bindParam(":nome", $params["nome"]);
        $stmt->bindParam(":prezzo", $params["prezzo"]);
        $stmt->bindParam(":marca", $params["marca"]);
        if ($stmt->execute()) {
            $stmt = $pdo->prepare("select * from michele_rizzo_ecommerce.products order by id desc limit 1");
            $stmt->execute();
            return $stmt->fetchObject("Product");
        } else {
            throw new PDOException("Errore Nella Creazione");
        }
    }

    public function Delete()
    {
        if (!$this->getId()) {
            return false;
        }
        $id = $this->getId();
        $pdo = self::Connect();
        $stmt = $pdo->prepare("delete from michele_rizzo_ecommerce.products where id=:id");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public static function FetchAll()
    {
        $pdo = self::Connect();
        $stmt = $pdo->query("select * from michele_rizzo_ecommerce.products");
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Product');

    }

    public function Update($params)
    {
        $pdo = self::Connect();
        $stmt = $pdo->prepare("UPDATE michele_rizzo_ecommerce.products SET nome = :nome, prezzo = :prezzo, marca = :marca WHERE id = :id");
        $v1 = isset($params['nome']) ? $params['nome'] : $this->getNome();
        $v2 = isset($params['prezzo']) ? $params['prezzo'] : $this->getPrezzo();
        $v3 = isset($params['marca']) ? $params['marca'] : $this->getMarca();
        $stmt->bindParam(":nome", $v1);
        $stmt->bindParam(":prezzo", $v2);
        $stmt->bindParam(":marca", $v3);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            $stmt = $pdo->prepare("SELECT * FROM michele_rizzo_ecommerce.products where id = :id");
            $stmt->bindParam(":id", $this->id);
            $stmt->execute();
            return $stmt->fetchObject("Product");
        } else {
            return false;
        }
    }

    public static function Connect()
    {
        return DbManager::Connect("michele_rizzo_ecommerce");
    }


    public function jsonSerialize()
    {
        return ['id' => $this->getId(), 'attributes' => ['nome' => $this->getNome(), 'marca' => $this->getMarca(), 'prezzo' => $this->getPrezzo()]];

    }

}
