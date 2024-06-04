<?php

include_once __DIR__ . "/../dbConnect.php";
include_once __DIR__ . "/../constants.php";
include_once __DIR__ . "/Contact.php";
include_once __DIR__ . "/City.php";
include_once __DIR__ . "/Category.php";
include_once __DIR__ . "/event.php";

class Offer
{

    private $id;
    private $city;
    private $category;
    private $description;
    private $contact;
    private $dateCreation;
    private $token;
    private $event;


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
     * @return event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param event $event
     */
    public function setEvent($event): void
    {
        $this->event = $event;
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
            $sql = $db->prepare("INSERT INTO offer values(
                         0,
                         :city_id,
                         :category_id,
                         :description,
                         :contact_id,
                         NOW(),
                         :token,
						 :event_id,
						 1
                            )");
            $sql->bindValue(":city_id", $this->getCity()->getId());
            $sql->bindValue(":category_id", $this->getCategory()->getId());
            $sql->bindValue(":description", $this->getDescription());
            $sql->bindValue(":contact_id", $this->getContact()->getId());
            $sql->bindValue(":token", $this->getToken());
            $sql->bindValue(":event_id", $this->getEvent()->getId());
            $sql->execute();
            $this->setId($db->lastInsertId());
            $db->commit();
        }catch(PDOException $e) {
            $db->rollback();
        }
    }

    /**
     * @param $id
     * @return Offer|null
     */
    public static function load($id) {

        global $db;

        $sql = $db->prepare("SELECT * FROM offer WHERE id = :id");
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

        $offers = array();

        $currentPage = !empty($filters['page']) ? $filters['page'] : 1;
        $city = !empty($filters['city']) ? $filters['city'] : 0;
        $category = !empty($filters['category']) ? $filters['category'] : 0;
        $event = !empty($filters['event']) ? $filters['event'] : 0;

        $offset = intval((($currentPage - 1) * NB_OFFERS_PER_PAGE));
        $max = NB_OFFERS_PER_PAGE;

        $request_select = "SELECT * FROM offer WHERE 1=1";
        $request_count = "SELECT COUNT(*) AS count FROM offer WHERE 1=1";

        $request_where = "";
        if ($city > 0){
            $request_where .= " AND city_id = :city_id";
        }
        if ($category > 0){
            $request_where .= " AND category_id = :category_id";
        }
		if ($event > 0){
            $request_where .= " AND event_id = :event_id";
        }

        $sql = $db->prepare($request_select . $request_where . " ORDER BY date_creation DESC LIMIT {$offset},{$max}");
        if ($city > 0){
            $sql->bindValue(":city_id", $city);
        }
        if ($category > 0){
            $sql->bindValue(":category_id", $category);
        }
		if ($event > 0){
            $sql->bindValue(":event_id", $event);
		}
        if($sql->execute()){
            while($row = $sql->fetch()){
                $offers[] = self::objectFromResult($row);
            }
        }
        $result = [];
        $result['offers'] = $offers;

        $sql = $db->prepare($request_count . $request_where);
        if ($city > 0){
            $sql->bindValue(":city_id", $city);
        }
        if ($category > 0){
            $sql->bindValue(":category_id", $category);
        }
		if ($event > 0){
            $sql->bindValue(":event_id", $event);
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

        $sql = $db->prepare("SELECT * FROM offer WHERE token = :token LIMIT 1");
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

        $offers = array();

        $sql = $db->prepare("SELECT * FROM offer ORDER BY date_creation DESC");
        if($sql->execute()){
            while($row = $sql->fetch()){
                $offers[] = self::objectFromResult($row);
            }
        }

        return $offers;

    }

    /**
     * @return int
     */
    public static function totalOffers(){

        global $db;

        $sql = $db->prepare("SELECT COUNT(*) AS count FROM offer");
        if($sql->execute()){
            $row = $sql->fetch();
            return $row['count'];
        }

        return 0;

    }

    public static function deleteByToken($token){

        global $db;

        $db->beginTransaction();

        Contact::deleteByOfferToken($token);

        $sql = $db->prepare("DELETE FROM offer WHERE token = :token");
        $sql->bindValue(":token", $token);
        $sql->execute();
        $count = $sql->rowCount();

        $db->commit();

        return $count;

    }

    /**
     * @param $row
     * @return Offer
     */
    private static function objectFromResult($row){

        $offer = new Offer();
        $offer->setId($row['id']);

        $category = Category::load($row['category_id']);
        $offer->setCategory($category);

        $event = Event::load($row['event_id']);
        $offer->setEvent($event);
		
        $city = City::load($row['city_id']);
        $offer->setCity($city);

        $contact = Contact::load($row['contact_id']);
        $offer->setContact($contact);

        $offer->setDescription($row['description']);

        $offer->setDateCreation($row['date_creation']);
		
        $offer->setToken($row['token'] ?: '');

	$offer->setEvent($row['event_id']);

        return $offer;

    }

}
