<?php

namespace Sterkh\Activica;

use Exception;
use PDO;

class Model
{
    protected $db;

    public function __construct()
    {
        $user = 'root';
        $pass = '';
        $opts = array(
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );
        $this->db = new PDO('mysql:host=localhost;dbname=activica', $user, $pass, $opts);
        $this->createTables();
    }

    public function multiInsert($table, $rows, $data)
    {
        $keys = implode(',', $rows);
        $values = ":" . implode(",:", $rows);
        $q = $this->db->prepare("INSERT INTO $table($keys) VALUES($values)");
        foreach ($data as $row) {
            $q->execute($row);
        }
    }

    public function getProducts()
    {
        $sql = "SELECT p.id, p.name,p.url,p.price,p.optprice, p.picture, p.article, p.category_id, p.description, p.available, p.status_new, p.status_action, p.status_top, v.name as vendor from products p LEFT JOIN vendors v ON p.vendor_id = v.id";
        $where = [];
        if (isset($_GET['category'])) {
            $sql .= ' LEFT JOIN categories c on p.category_id = c.id';
            $where[] = "p.category_id IN (SELECT id from categories where parent_id = $_GET[category] OR id = $_GET[category])";
        }
        if (isset($_GET['vendor'])) {
            $where[] = 'p.vendor_id in (' . implode(',', $_GET['vendor']) . ')';
        }
        if(!empty($where)){

        $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        return $this->db->query($sql)->fetchAll();

    }

    public function setOption($key, $value)
    {
        $q = $this->db->prepare("INSERT INTO options(`key`,value) VALUES (:key,:value) ON DUPLICATE KEY UPDATE value = :value");
        $q->execute(compact('key', 'value'));
    }

    public function getOption($key)
    {
        return $this->db->query("SELECT value FROM options WHERE `key` = '$key'")->fetch(PDO::FETCH_COLUMN);
    }

    public function getAll($table)
    {
        return $this->db->query("SELECT * FROM $table")->fetchAll();
    }

    private function createTables()
    {
        // categories;
        $sql = "CREATE TABLE IF NOT EXISTS categories(
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title varchar(255) NOT NULL,
                    parent_id INT,
                    FOREIGN KEY (parent_id) REFERENCES categories(id)) ENGINE=InnoDB; ";

        // options
        $sql .= "CREATE TABLE IF NOT EXISTS options(
        id INT AUTO_INCREMENT PRIMARY KEY,
        `key` varchar(255) NOT NULL,
        value varchar(255),
        UNIQUE KEY (`key`)) ENGINE=InnoDB;";

        // vendors
        $sql .= "CREATE TABLE IF NOT EXISTS vendors(
        id INT AUTO_INCREMENT PRIMARY KEY,
        name varchar(255) NOT NULL,
        UNIQUE KEY (name)) ENGINE=InnoDB; ";

        // products
        $sql .= "CREATE TABLE IF NOT EXISTS products(
            id INT AUTO_INCREMENT PRIMARY KEY,
            name varchar(255) NOT NULL,
            url varchar(255),
            price DECIMAL(10,2) NOT NULL,
            optprice DECIMAL(10,2), 
            category_id INT,
            vendor_id INT, 
        picture varchar(255),
        article varchar(255),
        description text,
        available tinyint(1),
        status_new tinyint(1),
        status_action tinyint(1), 
        status_top tinyint(1),    
        FOREIGN KEY (category_id) references categories(id),
        FOREIGN KEY (vendor_id) references vendors(id)
        ) ENGINE=InnoDB;";

        // product properties
        $sql .= "CREATE TABLE IF NOT EXISTS product_props(
            product_id INT NOT NULL,
            name varchar(255) NOT NULL,
            value text,
            INDEX(product_id, name)
) ENGINE=InnoDB;";


        $this->db->exec($sql);
    }

}