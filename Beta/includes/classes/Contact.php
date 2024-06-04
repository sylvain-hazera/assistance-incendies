<?php

include_once __DIR__ . "/../dbConnect.php";

class Contact
{

    private $id;
    private $type;
    private $name;
    private $firstName;
    private $phone;
    private $email;
    private $isMovable;
    private $isVisible;
    private $acceptNotification;

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
    private function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
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
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return boolean
     */
    public function isMovable()
    {
        return $this->isMovable;
    }

    /**
     * @param boolean $isMovable
     */
    public function setIsMovable($isMovable): void
    {
        $this->isMovable = $isMovable;
    }

    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->isVisible;
    }

    /**
     * @param boolean $isVisible
     */
    public function setIsVisible($isVisible): void
    {
        $this->isVisible = $isVisible;
    }

    /**
     * @return boolean
     */
    public function acceptNotification()
    {
        return $this->acceptNotification;
    }

    /**
     * @param boolean $acceptNotification
     */
    public function setAcceptNotification($acceptNotification): void
    {
        $this->acceptNotification = $acceptNotification;
    }

    public function save(){

        global $db;

        if ($this->getId()){
            return;
        }
        try {
            $db->beginTransaction();
            $sql = $db->prepare("INSERT INTO contact values(     
                           0,
                           :type,
                           :name,
                           :firstname,
                           :phone,
                           :email,
                           :move,
                           :visible,
                           :notification
                            )");
            $sql->bindValue(":type", $this->getType());
            $sql->bindValue(":name", $this->getName());
            $sql->bindValue(":firstname", $this->getFirstName());
            $sql->bindValue(":phone", $this->getPhone());
            $sql->bindValue(":email", $this->getEmail());
            $sql->bindValue(":move", intval($this->isMovable()));
            $sql->bindValue(":visible", intval($this->isVisible()));
            $sql->bindValue(":notification", intval($this->acceptNotification()));
            $sql->execute();
            $this->setId($db->lastInsertId());
            $db->commit();
        }catch(PDOException $e) {
            $db->rollback();
        }
    }

    public static function load($id) {

        global $db;

        $sql = $db->prepare("SELECT * FROM contact WHERE id = :id");
        $sql->bindValue(":id", $id);

        if($sql->execute() && $row = $sql->fetch()){
            return self::objectFromResult($row);
        }

        return null;

    }

    public static function deleteByOfferToken($token){

        global $db;

        $sql = $db->prepare("DELETE FROM contact WHERE id IN (SELECT contact_id FROM offer WHERE token = :token)");
        $sql->bindValue(":token", $token);
        $sql->execute();

    }

    private static function objectFromResult($row){

        $contact = new Contact();
        $contact->setId($row['id']);
        $contact->setName($row['name']);
        $contact->setFirstName($row['firstname']);
        $contact->setType($row['type']);
        $contact->setPhone($row['phone']);
        $contact->setEmail($row['email']);
        $contact->setIsMovable($row['move']);
        $contact->setIsVisible($row['visible']);
        $contact->setAcceptNotification($row['notification']);
        return $contact;

    }

}