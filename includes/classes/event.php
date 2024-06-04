<?php

include_once __DIR__ . "/../dbConnect.php";

class Event
{
    private $id;
    private $name;
    private $state;
    private $start_date;

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
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state): void
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * @param mixed $start_date
     */
    public function setStartDate($start_date): void
    {
        $this->start_date = $start_date;
    }

    public static function load($id) {
        global $db;

        $sql = $db->prepare("SELECT * FROM event WHERE id = :id");
        $sql->bindValue(":id", $id);

        if($sql->execute() && $row = $sql->fetch()){
            return self::objectFromResult($row);
        }

        return null;

    }

    public static function loadAll() {
        global $db;

        $events = array();

        $sql = $db->prepare("SELECT * FROM event ORDER BY id ASC");
        if($sql->execute()){
            while($row = $sql->fetch()){
                $events[] = self::objectFromResult($row);
            }
        }

        return $events;

    }

    private static function objectFromResult($row){
        $event = new event();
        $event->setId($row['id']);
        $event->setName($row['name']);
        $event->setState($row['state']);
        $event->setStartDate($row['start_date']);
        return $event;
    }
}
