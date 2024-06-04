<?php

include_once __DIR__ . "/../dbConnect.php";

class City
{

    private $id;
    private $name;
    private $postalCode;

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

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param mixed $postalCode
     */
    public function setPostalCode($postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public static function load($id) {
        global $db;

        $sql = $db->prepare("SELECT * FROM city WHERE id = :id");
        $sql->bindValue(":id", $id);

        if($sql->execute() && $row = $sql->fetch()){
            return self::objectFromResult($row);
        }

        return null;

    }

    public static function loadAll() {
        global $db;

        $cities = array();

        $sql = $db->prepare("SELECT * FROM city ORDER BY name ASC");
        if($sql->execute()){
            while($row = $sql->fetch()){
                $cities[] = self::objectFromResult($row);
            }
        }

        return $cities;

    }

    public static function loadCitiesWithOffer() {
        global $db;

        $cities = array();

        $sql = $db->prepare("SELECT DISTINCT city.* FROM city, offer WHERE city.id = offer.city_id ORDER BY city.name ASC");
        if($sql->execute()){
            while($row = $sql->fetch()){
                $cities[] = self::objectFromResult($row);
            }
        }

        return $cities;

    }

    private static function objectFromResult($row){
        $city = new City();
        $city->setId($row['id']);
        $city->setName($row['name']);
        $city->setPostalCode($row['postalcode']);
        return $city;
    }

}