<?php

namespace Lea\Core\Entity;

use Exception;
use Error;
use Lea\Core\Database\DatabaseManager;

class Entity  {
    protected $id;
    protected $active;
    protected $deleted;
    /**
     * @var DatabaseManager
     */
    private $db;
    private $tblName;

    public function __construct($db) {
        $this->db = $db;
        $words = preg_split('/(?=[A-Z])/', get_class($this), -1, PREG_SPLIT_NO_EMPTY);
        $this->tblName = strtolower(implode('_', $words));
    }

    /**
     * Ustawianie wartości pól
     * np.  $action=['id'=>12]
     */
    public function set(array $action, bool $safe = false) {
        foreach ($action as $key => $value) {
            if (!$safe)
                $this->$key = $value;
            else if (property_exists($this, $key))
                $this->$key = $value;
        }
    }

    /**
     * Pobieranie pól klasy.
     * Jeżeli jest $name, zwraca konkretne pole.
     * Bez $name zwraca wszystkie pola.
     */
    public function get(...$name) {
        if (sizeof($name) == 1) {
            $arg = $name[0];
            return $this->$arg;
        }
        if (!empty($name)) {
            foreach ($name as $arg) {
                if (property_exists($this, $arg)) {
                    if (is_a($this->$arg, 'Modal'))
                        $ret[$arg] = $this->$arg->get();
                    else if (is_array($this->$arg) && is_a($this->$arg[0], 'Modal')) {
                        foreach ($this->$arg as $obj)
                            $ret[$arg][] = $obj->get();
                    } else {
                        $ret[$arg] = $this->$arg;
                    }
                }
            }

            return $ret;
        } else {
            foreach ($this as $key => $val) {
                if (is_a($this->$key, 'Modal'))
                    $ret[$key] = $this->$key->get();
                else if (is_array($this->$key) && !empty($this->$key) && is_a($this->$key[0], 'Modal')) { // TODO - notice dla transz
                    foreach ($this->$key as $obj)
                        $ret[$key][] = $obj->get();
                } else if ($val !== "") { // wcześniej nie zapisywał zer, a teraz będzie
                    $ret[$key] = $val; /* Gdy Front przyśle pustego stringa konwertowany jest do NULL'a dla spójności w bazie */
                }
            }
            unset($ret['db']);
            unset($ret['tblName']);
            unset($ret['require']);
            return $ret;
        }
    }

    /**
     * Pobieranie pól z bazy danych z określonymi warunkami w $array
     *
     * @param [type] $array
     * @return
     */
    public static function load($array, $db) {
        $instance = new static($db);
        $array['deleted'] = 0;
        if (isset($array))
            $action = $instance->db->getListDataMultiCondition($instance->tblName, $array);

        // Wystapienie błędu pobierania z bazy danych
        if (!$action)
            throw new Exception("Nie można wczytać obiektu " . get_class($instance), 404);
        if ($action)
            $instance->set($action[0]);
        return $instance;
    }
    public static function loadAll($array, $db, $obj_to_arr = false): array {
        $instance = new static($db);
        $array['deleted'] = 0;

        $ret = [];
        $data = $instance->db->getListDataMultiCondition($instance->tblName, $array);
        if (!$data) {
            throw new Exception("Nie można wczytać obiektów " . get_class($instance), 404);
        }

        foreach ($data as $row) {
            $obj = new static($db);
            $obj->set($row);
            $obj_to_arr ? $ret[] = $obj->get() : $ret[] = $obj;
        }
        return $ret;
    }
    public static function search($db, $search = [], $fields = [], $to_array = false, $debug = false) {
        $instance = new static($db);
        $search['deleted'] = 0;
        $data = $instance->db->getFieldsDataMultiCondition($instance->tblName, $search, $fields, 0, 0, false, false, $debug);
        if (empty($data))
            throw new Error("Nie można wczytać obiektu", 404);
        $ret = array();
        foreach ($data as $row) {
            $obj = new static($db);
            $obj->set($row);
            $to_array ? $ret[] = $obj->get() : $ret[] = $obj;
        }
        return $ret;
    }

    public function save($debug = false): int {
        // $this->checkRequire();  // sprawdzanie wymaganych pól

        if ($this->deleted == 1)  // próba edycji 'usuniętego' pola
            throw new Exception("$this->tblName: Pole zostało usunięte.");

        if (!$this->id)
            $this->insert($debug);
        else
            $this->update();

        return $this->id;
    }

    public function remove(): void {
        $this->deleted = 1;
        $this->update(TRUE);
    }


    public function insert($debug = false) {
        $this->deleted = "0";
        $id = $this->db->insertRecordData($this->tblName, $this->get(), true, $debug);
        // if (!$id)
        //     throw new Exception($id);
        $this->id = $id;
    }

    private function  update($soft_delete = FALSE) {
        if ($soft_delete)
            $response = $this->db->updateData($this->tblName, ['id' => $this->id, 'deleted' => 1]);
        else
            $response = $this->db->updateData($this->tblName, $this->get());

        if ($response == "-1")
            throw new Exception("Błąd mysql: $response");
        return $response;
    }

    public static function getModelFields($modelaname) {
        return get_class_vars($modelaname);
    }

    private function checkRequire() {
        foreach ($this->require as $field) {
            if (!$this->$field)
                throw new Exception("Brak $field");
        }
    }

    protected function getDb(): DatabaseManager {
        return $this->db;
    }

    protected function getTableName(): string {
        return $this->tblName;
    }

    public function unsetId(): void {
        $this->id = null;
    }
}
