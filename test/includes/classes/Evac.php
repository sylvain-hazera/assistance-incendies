<?php

include_once __DIR__ . "/../dbConnect.php";
include_once __DIR__ . "/../constants.php";
include_once __DIR__ . "/Contact.php";
include_once __DIR__ . "/City.php";
include_once __DIR__ . "/Category.php";
include_once __DIR__ . "/event.php";

class Evac
{

    private $id;
    private $city;
    private $adresse;
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
     * @return Adresse
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @param mixed $adresse
     */
    public function setAdresse($adresse): void
    {
        $this->adresse = $adresse;
    }

    /**
     * @return event
     */
    public function getevent()
    {
        return $this->event;
    }

    /**
     * @param event $event
     */
    public function setevent($event): void
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
            $sql = $db->prepare("INSERT INTO evactest values(
                         0,
                         :city_id,
                         :adresse,
                         :description,
                         :contact_id,
                         NOW(),
                         :token,
			 :event_id
                            )");
            $sql->bindValue(":city_id", $this->getCity()->getId());
            $sql->bindValue(":adresse", $this->getAdresse());
            $sql->bindValue(":description", $this->getDescription());
            $sql->bindValue(":contact_id", $this->getContact()->getId());
            $sql->bindValue(":event_id", $this->getevent()->getId());
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
     * @return Evac|null
     */
    public static function load($id) {

        global $db;

        $sql = $db->prepare("SELECT * FROM evactest WHERE id = :id");
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

        $evacs = array();

        $currentPage = !empty($filters['page']) ? $filters['page'] : 1;
        $city = !empty($filters['city']) ? $filters['city'] : 0;
        $adresse = !empty($filters['adresse']) ? $filters['adresse'] : 0;
        $event = !empty($filters['event']) ? $filters['event'] : 0;

        $offset = intval((($currentPage - 1) * NB_OFFERS_PER_PAGE));
        $max = NB_OFFERS_PER_PAGE;

        $request_select = "SELECT * FROM evactest WHERE 1=1";
        $request_count = "SELECT COUNT(*) AS count FROM evactest WHERE 1=1";

        $request_where = "";
        if ($city > 0){
            $request_where .= " AND city_id = :city_id";
        }
	if ($event > 0){
            $request_where .= " AND event_id = :event_id";
        }

        $sql = $db->prepare($request_select . $request_where . " ORDER BY date_creation DESC LIMIT {$offset},{$max}");
        if ($city > 0){
            $sql->bindValue(":city_id", $city);
        }
	if ($event > 0){
            $sql->bindValue(":event_id", $event);
	}
        if($sql->execute()){
            while($row = $sql->fetch()){
                $evacs[] = self::objectFromResult($row);
            }
        }
        $result = [];
        $result[' evacs'] = $evacs;

        $sql = $db->prepare($request_count . $request_where);
        if ($city > 0){
            $sql->bindValue(":city_id", $city);
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

        $sql = $db->prepare("SELECT * FROM evactest WHERE token = :token LIMIT 1");
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

        $evacs = array();

        $sql = $db->prepare("SELECT * FROM evactest ORDER BY date_creation DESC");
        if($sql->execute()){
            while($row = $sql->fetch()){
                $evacs[] = self::objectFromResult($row);
            }
        }

        return $evacs;

    }

    /**
     * @return int
     */
    public static function totalEvacs(){

        global $db;

        $sql = $db->prepare("SELECT COUNT(*) AS count FROM evactest");
        if($sql->execute()){
            $row = $sql->fetch();
            return $row['count'];
        }

        return 0;

    }

    public static function deleteByToken($token){

        global $db;

        $db->beginTransaction();

        Contact::deleteByEvacToken($token);

        $sql = $db->prepare("DELETE FROM evactest WHERE token = :token");
        $sql->bindValue(":token", $token);
        $sql->execute();
        $count = $sql->rowCount();

        $db->commit();

        return $count;

    }

    /**
     * @param $row
     * @return Evac
     */
    private static function objectFromResult($row){

        $evac = new Evac();
        $evac->setId($row['id']);

        $event = Event::load($row['event_id']);
        $evac->setEvent($event);
		
        $city = City::load($row['city_id']);
        $evac->setCity($city);

        $contact = Contact::load($row['contact_id']);
        $evac->setContact($contact);

        $evac->setDescription($row['description']);

        $evac->setDateCreation($row['date_creation']);
		
		$evac->setEvent($row['event_id']);

        $evac->setToken($row['token'] ?: '');

        return $evac;

    }

}
