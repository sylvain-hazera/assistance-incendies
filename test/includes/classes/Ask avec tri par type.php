<?php

include_once __DIR__ . "/../dbConnect.php";
include_once __DIR__ . "/../constants.php";
include_once __DIR__ . "/Contact.php";
include_once __DIR__ . "/City.php";
include_once __DIR__ . "/Category.php";

class ask
{

    private $id;
    private $city;
    private $category;
    private $description;
    private $contact;
    private $dateCreation;
    private $token;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return Contact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param Contact $contact
     */
    public function setContact($contact): void
    {
        $this->contact = $contact;
    }

    /**
     * @return mixed
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * @param mixed $dateCreation
     */
    public function setDateCreation($dateCreation): void
    {
        $this->dateCreation = $dateCreation;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    private function setToken($token): void
    {
        $this->token = $token;
    }

    private function generateToken(): void
    {
        $bytes = random_bytes(20);
        $this->setToken(bin2hex($bytes));
    }

    public function save(){

        global $db;

        if ($this->getId()){
            return;
        }
        try {
            $this->getContact()->save();
            $this->generateToken();

            $db->beginTransaction();
            $sql = $db->prepare("INSERT INTO ask values(
                         0,
                         :city_id,
                         :category_id,
                         :description,
                         :contact_id,
                         NOW(),
                         :token
                            )");
            $sql->bindValue(":city_id", $this->getCity()->getId());
            $sql->bindValue(":category_id", $this->getCategory()->getId());
            $sql->bindValue(":description", $this->getDescription());
            $sql->bindValue(":contact_id", $this->getContact()->getId());
            $sql->bindValue(":token", $this->getToken());
            $sql->execute();
            $this->setId($db->lastInsertId());
            $db->commit();
        }catch(PDOException $e) {
            $db->rollback();
        }
    }

    /**
     * @param $id
     * @return ask|null
     */
    public static function load($id) {

        global $db;

        $sql = $db->prepare("SELECT * FROM ask WHERE id = :id");
        $sql->bindValue(":id", $id);

        if($sql->execute() && $row = $sql->fetch()){
            return self::objectFromResult($row);
        }

        return null;

    }

    /**
     * @param $filters
     * @return array
     */
    public static function loadWithFilters($filters){

        global $db;

        $asks = array();

        $currentPage = !empty($filters['page']) ? $filters['page'] : 1;
        $city = !empty($filters['city']) ? $filters['city'] : 0;
        $category = !empty($filters['category']) ? $filters['category'] : 0;

        $offset = intval((($currentPage - 1) * NB_askS_PER_PAGE));
        $max = NB_askS_PER_PAGE;

        $request_select = "SELECT * FROM ask WHERE 1=1";
        $request_count = "SELECT COUNT(*) AS count FROM ask WHERE 1=1";

        $request_where = "";
        if ($city > 0){
            $request_where .= " AND city_id = :city_id";
        }
        if ($category > 0){
            $request_where .= " AND category_id = :category_id";
        }

        $sql = $db->prepare($request_select . $request_where . " ORDER BY date_creation DESC LIMIT {$offset},{$max}");
        if ($city > 0){
            $sql->bindValue(":city_id", $city);
        }
        if ($category > 0){
            $sql->bindValue(":category_id", $category);
        }
        if($sql->execute()){
            while($row = $sql->fetch()){
                $asks[] = self::objectFromResult($row);
            }
        }
        $result = [];
        $result['asks'] = $asks;

        $sql = $db->prepare($request_count . $request_where);
        if ($city > 0){
            $sql->bindValue(":city_id", $city);
        }
        if ($category > 0){
            $sql->bindValue(":category_id", $category);
        }
        if($sql->execute()){
            if($row = $sql->fetch()){
                $result['count'] = $row['count'];
            }
        }
        return $result;

    }

    public static function loadByToken($token) {

        global $db;

        $sql = $db->prepare("SELECT * FROM ask WHERE token = :token LIMIT 1");
        $sql->bindValue(":token", $token);

        if($sql->execute() && $row = $sql->fetch()){
            return self::objectFromResult($row);
        }

        return null;

    }

    /**
     * @return array
     */
    public static function loadAll() {

        global $db;

        $asks = array();

        $sql = $db->prepare("SELECT * FROM ask ORDER BY date_creation DESC");
        if($sql->execute()){
            while($row = $sql->fetch()){
                $asks[] = self::objectFromResult($row);
            }
        }

        return $asks;

    }

    /**
     * @return int
     */
    public static function totalasks(){

        global $db;

        $sql = $db->prepare("SELECT COUNT(*) AS count FROM ask");
        if($sql->execute()){
            $row = $sql->fetch();
            return $row['count'];
        }

        return 0;

    }

    public static function deleteByToken($token){

        global $db;

        $db->beginTransaction();

        Contact::deleteByaskToken($token);

        $sql = $db->prepare("DELETE FROM ask WHERE token = :token");
        $sql->bindValue(":token", $token);
        $sql->execute();
        $count = $sql->rowCount();

        $db->commit();

        return $count;

    }

    /**
     * @param $row
     * @return ask
     */
    private static function objectFromResult($row){

        $ask = new ask();
        $ask->setId($row['id']);

        $category = Category::load($row['category_id']);
        $ask->setCategory($category);

        $city = City::load($row['city_id']);
        $ask->setCity($city);

        $contact = Contact::load($row['contact_id']);
        $ask->setContact($contact);

        $ask->setDescription($row['description']);

        $ask->setDateCreation($row['date_creation']);

        $ask->setToken($row['token'] ?: '');

        return $ask;

    }

}
