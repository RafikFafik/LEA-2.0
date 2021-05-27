<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use ReflectionClass;
use Lea\Core\Database\DatabaseUtil;
use Lea\Core\Reflection\Reflection;

abstract class DatabaseManager extends DatabaseUtil // implements DatabaseManagerInterface
{
    public $uid = 0;


    function __construct()
    {
        if (!isset($this->connection)) {
            $this->connection = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
            // mysqli_connect(host, user, pass, db);
            if (!$this->connection) {
                echo "Error: Unable to connect to MySQL." . PHP_EOL;
                echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
                echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
                exit;
            }
            mysqli_set_charset($this->connection, "utf8");
        }
    }

    public function setUser($uid)
    {
        $this->uid = $uid;
    }

    protected function getRecordData(object $object, $where_value, $where_column = "id", $debug = false)
    {
        $this->object = $object;
        $tableName = self::getTableNameByObject($object);
        $columns = $this->getTableColumnsByObject($object);
        $query = DatabaseQuery::getSelectRecordDataQuery($object, $tableName, $columns, $where_value, $where_column);

        $result = $this->executeQuery($query, $tableName, $columns, $object);
        if ($result) {
            if ($row = mysqli_fetch_assoc($result)) {
                $className = get_class($object);
                $object = new $className();
                $class = get_class($object);
                $reflection = new ReflectionClass($class);
                $protected_properties = $reflection->getProperties(Reflection::IS_PROTECTED);
                $private_properties = $reflection->getProperties(Reflection::IS_PRIVATE);
                $properties = array_merge($private_properties, $protected_properties);
                $mi = $this->getMultipleIterator($row, $object->getSetters(), $properties);
                foreach ($mi as $triple) {
                    $key = self::processSnakeToPascal($triple[1]);
                    $reflection = new Reflection(get_class($object), $triple[2]->getName());
                    if($reflection->isObject())
                        continue;
                    $setValue = $triple[1];
                    $object->$setValue($triple[0]);
                }
            }
        }

        return $object;
    }

    protected function insertRecordData(object $object, $retId = TRUE)
    {
        $query = DatabaseQuery::getInsertIntoQuery($object);
        $tableName = self::getTableNameByObject($object);
        $columns = self::getTableColumnsByObject($object);
        $this->executeQuery($query, $tableName, $columns, $object);

        $child_objects = $object->getChildObjects();
        $this->insertIterablyObjects($child_objects);

        if (!$retId)
            $feedback = mysqli_affected_rows($this->connection);
        else
            $feedback = mysqli_insert_id($this->connection);

        return $feedback;
    }

    private function insertIterablyObjects(iterable $iterables)
    {
        foreach ($iterables as $iterable) {
            foreach ($iterable as $obj) {
                $this->insertRecordData($obj);
            }
        }
    }

    protected function getListDataMultiCondition($tableName, $arr = array(), $start = 0, $limit = 0, $sortBy = "", $sortOrder = "", $debug = false)
    {
        $strToQuery = "";
        foreach ($arr as $key => $val) {
            if (strstr($key, "_IN") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_IN", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_IN", "", $key)] . " IN ('" . join("','", $val) . "')";
            elseif (strstr($key, "_INOR-START") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_INOR-START", "", $key)])) $strToQuery .= " AND (" . $this->cfgArrDatabaseInterface[$tableName][str_replace("_INOR-START", "", $key)] . " IN ('" . join("','", $val) . "')";
            elseif (strstr($key, "_INOR-END") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_INOR-END", "", $key)])) $strToQuery .= " OR " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_INOR-END", "", $key)] . " IN ('" . join("','", $val) . "'))";
            elseif (strstr($key, "_NOTIN") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_NOTIN", "", $key)])) {

                $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_NOTIN", "", $key)] . " NOT IN ('" . join("','", $val) . "')";
                //  var_dump($val);
            } elseif (strstr($key, "_LIKE") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKE", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKE", "", $key)] . " LIKE '%" . $this->_stringtodb($val) . "%'";
            elseif (strstr($key, "_LIKEOR-ARR") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-ARR", "", $key)])) {
                //wymagana tablica elementów ! podajemy tablice jako arrCriterias, zapytanie robi nam dowolną ilość or'ów w kwerendzie
                if (sizeof($val) == 1) {
                    $strToQuery .= " AND (" . $this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-ARR", "", $key)] . " LIKE '%" . $this->_stringtodb($val[0]) . "%')";
                } else if (sizeof($val) > 1) {
                    $strToQuery .= " AND (" . $this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-ARR", "", $key)] . " LIKE '%" . $this->_stringtodb($val[0]) . "%'";
                    for ($i = 1; $i < sizeof($val) - 1; $i++) {
                        $strToQuery .= " OR " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-ARR", "", $key)] . " LIKE '%" . $this->_stringtodb($val[$i]) . "%'";
                    }
                    $strToQuery .= " OR " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-ARR", "", $key)] . " LIKE '%" . $this->_stringtodb($val[sizeof($val) - 1]) . "%')";
                }
            } elseif (strstr($key, "_<=DATE") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_<=DATE", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_<=DATE", "", $key)] . "<='" . $val . "'";
            elseif (strstr($key, "_>=DATE") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_>=DATE", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_>=DATE", "", $key)] . ">='" . $val . "'";
            elseif (strstr($key, "_LIKEOR-START") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-START", "", $key)])) $strToQuery .= " AND (" . $this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-START", "", $key)] . " LIKE '%" . $this->_stringtodb($val) . "%'";
            elseif (strstr($key, "_LIKEOR-END") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-END", "", $key)])) $strToQuery .= " OR " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-END", "", $key)] . " LIKE '%" . $this->_stringtodb($val) . "%')";
            elseif (strstr($key, "_LIKEOR") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR", "", $key)])) $strToQuery .= " OR " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR", "", $key)] . " LIKE '%" . $this->_stringtodb($val) . "%'";
            elseif (strstr($key, "_<=") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_<=", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_<=", "", $key)] . "<=" . $val . "";
            elseif (strstr($key, "_>=") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_>=", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_>=", "", $key)] . ">=" . $val . "";
            elseif (strstr($key, "_>") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_>", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_>", "", $key)] . ">" . $val . "";
            elseif (strstr($key, "_<") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_<", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_<", "", $key)] . "<" . $val . "";
            elseif (strstr($key, "_>DATE") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_>DATE", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_>DATE", "", $key)] . ">'" . $val . "'";
            elseif (strstr($key, "_<DATE") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_<DATE", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_<DATE", "", $key)] . "<'" . $val . "'";
            elseif (strstr($key, "_=") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_=", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_=", "", $key)] . "=" . $val . "";
            elseif (strstr($key, "_<>") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_<>", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_<>", "", $key)] . "<>" . $val . "";
            elseif (strstr($key, "_NULL") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_NULL", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_NULL", "", $key)] . " IS NULL";
            elseif (strlen($this->cfgArrDatabaseInterface[$tableName][$key])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][$key] . "='" . $val . "'";
        }

        if (is_array($sortBy) && count($sortBy)) {
            $sortString = " ORDER BY ";
            foreach ($sortBy as $key => $val) {
                $sortString .= " " . $this->cfgArrDatabaseInterface[$tableName][$key] . " " . $val . ",";
            }
            $sortString = substr($sortString, 0, -1);
        } else
            $sortString = (strlen($sortBy) ? " ORDER BY " . $this->cfgArrDatabaseInterface[$tableName][$sortBy] . (strlen($sortOrder) ? " " . $sortOrder : " ASC") : "");

        $query = "SELECT * ";
        $query .= "FROM " . $this->cfgArrDatabaseTables[$tableName] . " ";
        if (strlen($strToQuery)) $query .= "WHERE " . substr($strToQuery, 5);
        $query .= $sortString;
        $query .= ($limit > 0 ? " LIMIT " . $start . "," . $limit : "");
        if ($debug) return $query;
        $result = mysqli_query($this->connection, $query);
        if (mysqli_error($this->connection)) {

            $this->handleError($tableName, $query, $arr);
        }
        $i = 0;
        $returnArr = array();
        if ($result == false)
            return $result;
        while ($row = mysqli_fetch_assoc($result)) {
            foreach ($row as $key => $val) {
                $strTempKey = array_search($key, $this->cfgArrDatabaseInterface[$tableName]);
                if ($strTempKey !== false)
                    $returnArr[$i][$strTempKey] = stripslashes($val);
            }

            $i++;
        }

        return $returnArr;
    }

    function getFieldsDataMultiCondition($tableName, $arr = array(), $fields = array(),  $start = 0, $limit = 0, $sortBy = "", $sortOrder = "", $debug = false)
    {
        $strToQuery = "";
        foreach ($arr as $key => $val) {
            if (strstr($key, "_IN") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_IN", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_IN", "", $key)] . " IN ('" . join("','", $val) . "')";
            elseif (strstr($key, "_INOR-START") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_INOR-START", "", $key)])) $strToQuery .= " AND (" . $this->cfgArrDatabaseInterface[$tableName][str_replace("_INOR-START", "", $key)] . " IN ('" . join("','", $val) . "')";
            elseif (strstr($key, "_INOR-END") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_INOR-END", "", $key)])) $strToQuery .= " OR " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_INOR-END", "", $key)] . " IN ('" . join("','", $val) . "'))";
            elseif (strstr($key, "_NOTIN") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_NOTIN", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_NOTIN", "", $key)] . " NOT IN ('" . join("','", $val) . "')";
            elseif (strstr($key, "_LIKE") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKE", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKE", "", $key)] . " LIKE '%" . $this->_stringtodb($val) . "%'";
            elseif (strstr($key, "_LIKEOR-ARR") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-ARR", "", $key)])) {
                //wymagana tablica elementów ! podajemy tablice jako arrCriterias, zapytanie robi nam dowolną ilość or'ów w kwerendzie
                if (sizeof($val) == 1) {
                    $strToQuery .= " AND (" . $this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-ARR", "", $key)] . " LIKE '%" . $this->_stringtodb($val[0]) . "%')";
                } else if (sizeof($val) > 1) {
                    $strToQuery .= " AND (" . $this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-ARR", "", $key)] . " LIKE '%" . $this->_stringtodb($val[0]) . "%'";
                    for ($i = 1; $i < sizeof($val) - 1; $i++) {
                        $strToQuery .= " OR " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-ARR", "", $key)] . " LIKE '%" . $this->_stringtodb($val[$i]) . "%'";
                    }
                    $strToQuery .= " OR " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-ARR", "", $key)] . " LIKE '%" . $this->_stringtodb($val[sizeof($val) - 1]) . "%')";
                }
            } elseif (strstr($key, "_<=DATE") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_<=DATE", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_<=DATE", "", $key)] . "<='" . $val . "'";
            elseif (strstr($key, "_>=DATE") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_>=DATE", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_>=DATE", "", $key)] . ">='" . $val . "'";
            elseif (strstr($key, "_LIKEOR-START") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-START", "", $key)])) $strToQuery .= " AND (" . $this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-START", "", $key)] . " LIKE '%" . $this->_stringtodb($val) . "%'";
            elseif (strstr($key, "_LIKEOR-END") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-END", "", $key)])) $strToQuery .= " OR " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR-END", "", $key)] . " LIKE '%" . $this->_stringtodb($val) . "%')";
            elseif (strstr($key, "_LIKEOR") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR", "", $key)])) $strToQuery .= " OR " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKEOR", "", $key)] . " LIKE '%" . $this->_stringtodb($val) . "%'";
            elseif (strstr($key, "_<=") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_<=", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_<=", "", $key)] . "<=" . $val . "";
            elseif (strstr($key, "_>=") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_>=", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_>=", "", $key)] . ">=" . $val . "";
            elseif (strstr($key, "_>") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_>", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_>", "", $key)] . ">" . $val . "";
            elseif (strstr($key, "_<") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_<", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_<", "", $key)] . "<" . $val . "";
            elseif (strstr($key, "_>DATE") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_>DATE", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_>DATE", "", $key)] . ">'" . $val . "'";
            elseif (strstr($key, "_<DATE") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_<DATE", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_<DATE", "", $key)] . "<'" . $val . "'";
            elseif (strstr($key, "_=") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_=", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_=", "", $key)] . "=" . $val . "";
            elseif (strstr($key, "_<>") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_<>", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_<>", "", $key)] . "<>" . $val . "";
            elseif (strstr($key, "_NULL") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_NULL", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_NULL", "", $key)] . " IS NULL";
            elseif (strlen($this->cfgArrDatabaseInterface[$tableName][$key])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][$key] . "='" . $val . "'";
        }

        if (is_array($sortBy) && count($sortBy)) {
            $sortString = " ORDER BY ";
            foreach ($sortBy as $key => $val) {
                $sortString .= " " . $this->cfgArrDatabaseInterface[$tableName][$key] . " " . $val . ",";
            }
            $sortString = substr($sortString, 0, -1);
        } else
            $sortString = (strlen($sortBy) ? " ORDER BY " . $this->cfgArrDatabaseInterface[$tableName][$sortBy] . (strlen($sortOrder) ? " " . $sortOrder : " ASC") : "");

        if (sizeof($fields)) {
            $query = "SELECT ";
            foreach ($fields as $f) {
                $query .= ($this->cfgArrDatabaseInterface[$tableName][$f] . ', ');
            }
            $query = rtrim($query, ', ');
            $query .= ' ';
        } else {

            $query = "SELECT * ";
        }
        $query .= "FROM " . $this->cfgArrDatabaseTables[$tableName] . " ";
        if (strlen($strToQuery)) $query .= "WHERE " . substr($strToQuery, 5);
        $query .= $sortString;
        $query .= ($limit > 0 ? " LIMIT " . $start . "," . $limit : "");
        if ($debug) return $query;
        $result = mysqli_query($this->connection, $query);
        if (mysqli_error($this->connection)) {

            $this->handleError($tableName, $query, $arr);
        }
        $i = 0;
        $returnArr = array();
        while ($row = mysqli_fetch_assoc($result)) {
            foreach ($row as $key => $val) {
                $strTempKey = array_search($key, $this->cfgArrDatabaseInterface[$tableName]);
                if ($strTempKey !== false)
                    $returnArr[$i][$strTempKey] = stripslashes($val);
            }

            $i++;
        }

        return $returnArr;
    }



    function insertLog($query_str, $id = 0, $type, $table)
    {

        if ($table == "opers_sessions" || $table == "keys" || $table == "drives") return 0;

        if (isset($_POST['password']) || isset($_POST['password2'])) {
            $_POST['password'] = 'HIDDEN';
            $_POST['password2'] = 'HIDDEN_ALSO';
        }

        $query = "INSERT INTO tbl_logs ";

        $query .= "(fld_Table, fld_Data, fld_Uri, fld_RequestId, fld_Type, fld_Query , fld_CreateDate, fld_CreateIP, fld_CreateUId) ";
        $query .= "VALUES ('" . $table . "','" . http_build_query($_POST) . "','" . $_SERVER['REQUEST_URI'] . "','" . $id . "', '" . $type . "', '(" . str_replace("'", "\"", $query_str) . ")','" . date("Y-m-d H-i-s") . "'" . ",'" . $_SERVER["REMOTE_ADDR"] . "'," . $this->user_id . ")";

        mysqli_query($this->connection, $query);
        //  return $query;
    }
    function getAllUniqueMonthsYears($tableName, $fld, $mindate = "2019-05-01", $debug = false)
    {

        $tableIndex = "";
        if (gettype($tableName) == "array") {
            $tableIndex = $tableName[1];
            $tableName = $tableName[0];
        }
        $query = "SELECT DISTINCT YEAR(" . $this->cfgArrDatabaseInterface[$tableName][$fld] . ") as 'year', ";
        $query .= "MONTH(" . $this->cfgArrDatabaseInterface[$tableName][$fld] . ") as month FROM " . $this->cfgArrDatabaseTables[$tableName] . $tableIndex . " WHERE `fld_Deleted` = '0' AND `fld_OrderDate` >= '$mindate'";
        $result = mysqli_query($this->connection, $query);
        $resArr = array();
        $resArr["months"] = array();
        $resArr["years"] = array();
        if ($debug) return $query;
        foreach ($result as $v) {
            if ($v["year"])
                $resArr["years"][] = $v["year"];
            if ($v["month"])
                $resArr["months"][] = $v["month"];
        }

        $resArr["years"] = array_unique($resArr["years"]);
        $resArr["months"] = array_unique($resArr["months"]);
        sort($resArr["years"]);
        sort($resArr["months"]);

        return $resArr;
    }

    function updateData($tableName, $arr, $fldId = "id", $debug = false)
    {
        $query = '';
        $_query1 = "";
        $_query2 = "";
        foreach ($arr as $key => $val) {
            if (!isset($this->cfgArrDatabaseInterface[$tableName][$key]))
                continue;
            if ($val === NULL) /* Wymuszone === w momencie próby zapisania statusu = 0, okazuje się, że 0 == NULL ? prawda */
                $_query1 .= $this->cfgArrDatabaseInterface[$tableName][$key] . "=NULL,";
            elseif ($key == $fldId)
                $_query2 .= $this->cfgArrDatabaseInterface[$tableName][$key] . "='" . $this->_stringtodb($val) . "'";
            elseif (isset($this->cfgArrDatabaseInterface[$tableName][$key]))
                $_query1 .= $this->cfgArrDatabaseInterface[$tableName][$key] . "='" . $this->_stringtodb($val) . "',";
        }

        if (strlen($_query1) && strlen($_query2)) {
            $query = "UPDATE " . $this->cfgArrDatabaseTables[$tableName] . " ";
            $query .= "SET " . $_query1 . "fld_ModifyDate=" . "'" . date("Y-m-d H-i-s") . "'" . ",fld_ModifyIP='" . $_SERVER["REMOTE_ADDR"] . "',fld_ModifyUId=" . $this->user_id . " ";
            $query .= "WHERE " . $_query2;
            if ($debug) return $query;
            mysqli_query($this->connection, $query);
            if (mysqli_error($this->connection) || mysqli_affected_rows($this->connection) == "-1") {

                $this->handleError($tableName, $query, $arr);
            }
            $feedback = mysqli_affected_rows($this->connection);
        } else
            $feedback = false;



        $tmp_id = 0;
        if (isset($arr['id'])) {
            $tmp_id = $arr["id"];
        }
        $this->insertLog($query, $tmp_id, 2, $tableName);

        return $feedback;
    }


    function removeRecordData($tableName, $fldVal, $fldName = "id", $debug = false)
    {
        $query = "DELETE FROM " . $this->cfgArrDatabaseTables[$tableName] . " ";
        $query .= "WHERE " . $this->cfgArrDatabaseInterface[$tableName][$fldName] . "='" . $this->_stringtodb($fldVal) . "'";
        if ($debug) return $query;
        mysqli_query($this->connection, $query);
        if (mysqli_error($this->connection)) {

            $this->handleError($tableName, $query, $fldVal);
        }
        $feedback = mysqli_affected_rows($this->connection);

        return $feedback;
    }


    function removeRecordDataMultiCondition($tableName, $arr, $debug = false)
    {
        $strToQuery = "";
        foreach ($arr as $key => $val) {
            if (strstr($key, "_IN") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_IN", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_IN", "", $key)] . " IN ('" . join("','", $val) . "')";
            elseif (strstr($key, "_NOTIN") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_NOTIN", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_NOTIN", "", $key)] . " NOT IN ('" . join("','", $val) . "')";
            elseif (strstr($key, "_LIKE") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKE", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_LIKE", "", $key)] . " LIKE '%" . $this->_stringtodb($val) . "%'";
            elseif (strstr($key, "_<=") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_<=", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_<=", "", $key)] . "<=" . $val . "";
            elseif (strstr($key, "_>=") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_>=", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_>=", "", $key)] . ">=" . $val . "";
            elseif (strstr($key, "_>") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_>", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_>", "", $key)] . ">" . $val . "";
            elseif (strstr($key, "_<") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_<", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_<", "", $key)] . "<" . $val . "";
            elseif (strstr($key, "_=") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_=", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_=", "", $key)] . "=" . $val . "";
            elseif (strstr($key, "_<>") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_<>", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_<>", "", $key)] . "<>" . $val . "";
            elseif (strstr($key, "_NULL") && strlen($this->cfgArrDatabaseInterface[$tableName][str_replace("_NULL", "", $key)])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][str_replace("_NULL", "", $key)] . " IS NULL";
            elseif (strlen($this->cfgArrDatabaseInterface[$tableName][$key])) $strToQuery .= " AND " . $this->cfgArrDatabaseInterface[$tableName][$key] . "='" . $val . "'";
        }

        $query = "DELETE FROM " . $this->cfgArrDatabaseTables[$tableName] . " ";
        $query .= "WHERE " . substr($strToQuery, 5);
        if ($debug) return $query;
        mysqli_query($this->connection, $query);
        if (mysqli_error($this->connection)) {

            $this->handleError($tableName, $query, $arr);
        }
        $feedback = mysqli_affected_rows($this->connection);

        return $feedback;
    }
    function clearTable($tableName)
    {
        $query = "DELETE FROM " . $this->cfgArrDatabaseTables[$tableName];
        //  mysqli_query($this->connection, $query);
    }

    function getCustomQueryList($query)
    {
        $result = mysqli_query($this->connection, $query);
        $returnArr = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $returnArr[] = $row;
        }
        return $returnArr;
    }

    function getMostOccuredData($tableName, $fldName, $limit = 0, $debug = false)
    {
        $query = "SELECT " . $this->cfgArrDatabaseInterface[$tableName][$fldName];
        $query .= "," . "count(" . $this->cfgArrDatabaseInterface[$tableName][$fldName] . ") ";
        $query .= "FROM " . $this->cfgArrDatabaseTables[$tableName] . " ";
        $query .= "GROUP BY " . $this->cfgArrDatabaseInterface[$tableName][$fldName] . " ";
        $query .= "ORDER BY " . "count(" . $this->cfgArrDatabaseInterface[$tableName][$fldName] . ") ";
        $query .= ($limit > 0 ? " LIMIT " . $limit : "");
        if ($debug) return $query;
        $result = mysqli_query($this->connection, $query);
        $result = mysqli_query($this->connection, $query);

        $i = 0;
        $returnArr = array();
        while ($row = mysqli_fetch_assoc($result)) {
            foreach ($row as $key => $val) {
                if ($val == "")
                    $val = "NULL";
                $strTempKey = array_search($key, $this->cfgArrDatabaseInterface[$tableName]);
                if ($strTempKey == false)
                    $strTempKey = "number_of";
                $returnArr[$i][$strTempKey] = stripslashes($val);
            }

            $i++;
        }

        return $returnArr;
    }
    function getUniqueData($tableName, $fldName, $limit = 0, $debug = false)
    {
        $query = "SELECT  DISTINCT " . $this->cfgArrDatabaseInterface[$tableName][$fldName];
        $query .= " FROM " . $this->cfgArrDatabaseTables[$tableName] . " ";
        if ($debug) return $query;
        $result = mysqli_query($this->connection, $query);
        $i = 0;
        $returnArr = array();
        while ($row = mysqli_fetch_assoc($result)) {
            foreach ($row as $key => $val) {
                if ($val == "")
                    $val = "NULL";
                $strTempKey = array_search($key, $this->cfgArrDatabaseInterface[$tableName]);
                if ($strTempKey == false)
                    $strTempKey = "number_of";
                $returnArr[$i][$strTempKey] = stripslashes($val);
            }

            $i++;
        }

        return $returnArr;
    }
    function _stringtodb($strParamString)
    {
        return addslashes($strParamString);
    }

    /* Funkcje do uzupełniania bazy o brakujące pola */
    private function fixDbState($tableName, $payload): void
    {
        // die("KIERWA: $tableName");
        // $cfg_columns = $this->getCfgColumnList($tableName);
        // $db_columns = $this->getDbColumnList($tableName);
        // $diff = array_diff($cfg_columns, $db_columns);

        // $message = "Rozbieżnośc configu z bazą. Dodano następujące kolumny:";
        // $message .= json_encode(array_values($diff));
        // $this->alterTableAddColumns($tableName, $diff, $payload);
        // $message .= "\nMożna powtórzyć request";

        // die($message);

    }

    private function fixDbStateAndGetColumnList($tableName, $payload): string
    {
        // $cfg_columns = $this->getCfgColumnList($tableName);
        // $db_columns = $this->getDbColumnList($tableName);
        // $diff = array_diff($cfg_columns, $db_columns);
        // if(empty($diff))
        //   return "";

        // $message = "Rozbieżnośc configu z bazą. Dodano następujące kolumny:";
        // $message .= json_encode(array_values($diff));
        // $this->alterTableAddColumns($tableName, $diff, $payload);
        // $message .= "\nMożna powtórzyć request";

        // return $message;
        return '';
    }

    private function alterTableAddColumns($tableName, $columns, $payload): void
    {
        $query = null;
        foreach ($columns as $column) {
            $query .= "ALTER TABLE `" . $this->cfgArrDatabaseTables[$tableName] . "` ADD COLUMN `$column` ";
            $query .= $this->getColumnType($tableName, $column, $payload);
        }
        if (empty($query))
            return;
        mysqli_multi_query($this->connection, $query);
        if (mysqli_error($this->connection))

            die(json_encode(mysqli_error($this->connection)));
    }

    private function getColumnType($tableName, $column, $payload)
    {
        $cfg_key = array_search($column, $this->cfgArrDatabaseInterface[$tableName]);
        $val = isset($payload[$cfg_key]) ? $payload[$cfg_key] : NULL;
        switch ($cfg_key) {
            case is_a(\DateTime::createFromFormat('Y-m-d', $val), 'DateTime'):
                $ret = "DATETIME; ";
                break;
            case gettype((int)$val) == "integer" && strlen($val) == strlen($val);
                $ret = "INT; ";
                break;
            default:
                $ret = "VARCHAR(150); ";
                break;
        }

        return $ret;
    }

    private function getCfgColumnList(string $tableName): array
    {
        $class_database_correspondence_default_columns = [
            'fld_CreateDate',
            'fld_CreateIP',
            'fld_CreateUId',
            'fld_ModifyDate',
            'fld_ModifyIP',
            'fld_ModifyUId',
            'fld_Deleted'
        ];
        $cfg_columns = array_merge(array_values($this->cfgArrDatabaseInterface[$tableName]), $class_database_correspondence_default_columns);

        return $cfg_columns;
    }

    private function getDbColumnList(string $tableName): array
    {
        $query = "DESCRIBE ";
        $query .= $this->cfgArrDatabaseTables[$tableName];
        $result = mysqli_query($this->connection, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $list[] = $row['Field'];
        }

        return $list;
    }

    private function handleError($tableName, $query, $arr = []): void
    {
        http_response_code(500);
        $err['db'] = mysqli_error($this->connection);
        $err['query'] = $query;
        $err['api'] = $this->fixDbStateAndGetColumnList($tableName, $arr);

        die(json_encode($err));
    }
}
