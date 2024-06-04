<?php

include_once __DIR__ . "/../dbConnect.php";

class Category
{
    private $id;
    private $name;

    /**
     * @param mixed $id
     */
    private function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    public static function load($id) {
        global $db;

        $sql = $db->prepare("SELECT * FROM category WHERE id = :id");
        $sql->bindValue(":id", $id);

        if($sql->execute() && $row = $sql->fetch()){
            return self::objectFromResult($row);
        }

        return null;

    }

    public static function loadAll() {
        global $db;

        $categories = array();

        $sql = $db->prepare("SELECT * FROM category ORDER BY id ASC");
        if($sql->execute()){
            while($row = $sql->fetch()){
                $categories[] = self::objectFromResult($row);
            }
        }

        return $categories;

    }

    private static function objectFromResult($row){
        $category = new Category();
        $category->setId($row['id']);
        $category->setName($row['name']);
        return $category;
    }
}