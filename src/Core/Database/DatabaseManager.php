<?php

declare(strict_types=1);

namespace Lea\Core\Database;

use Lea\Core\Reflection\Reflection;
use Lea\Core\Exception\ResourceNotExistsException;
use Lea\Core\Exception\UpdatingNotExistingResource;
use Lea\Core\Reflection\ReflectionPropertyExtended;

abstract class DatabaseManager extends DatabaseQuery // implements DatabaseManagerInterface
{
    const PROPERTY = 2;
    const METHOD = 1;
    const VALUE = 0;

    private $root_object;
    private $root_reflector;
    private $tableName;

    private static $connection;

    protected function __construct(object $object, $user_id = null)
    {
        $this->root_object = $object;
        $this->root_reflector = new Reflection($object);
        $this->tableName = self::getTableNameByObject($object);
        if ($user_id)
            $this->user_id = $user_id;
    }

    protected function getRecordData($where_value, $where_column = "id"): object
    {
        return $this->getObjectData($where_value, $where_column, false);
    }

    protected function getNestedRecordData($where_value, $where_column = "id"): object
    {
        return $this->getObjectData($where_value, $where_column, true);
    }

    private function getObjectData($where_value, $where_column = "id", $is_nested): object
    {
        $columns = self::getTableColumnsByReflector($this->root_reflector);
        $query = $this->getSelectRecordDataQuery($this->tableName, $columns, $where_value, $where_column);

        $result = self::executeQuery($query, $this->tableName, $columns, $this->root_object);
        if ($result->num_rows == 0)
            throw new ResourceNotExistsException($this->root_object->getClassName());

        $row = mysqli_fetch_assoc($result);
        foreach ($row as $key => $val) {
            if ($val === null)
                continue;
            $key = self::convertToKey($key);
            $setVal = 'set' . self::processSnakeToPascal($key);
            $property = new ReflectionPropertyExtended(get_class($this->root_object), $key, $this->root_reflector->getNamespaceName());
            if (method_exists($this->root_object, $setVal) && $property->isObject()) {
                $children[] = $setVal;
            } else if (method_exists($this->root_object, $setVal)) {
                $type = $property->getType2();
                $this->root_object->$setVal(self::castVariable($val, $type));
            }
        }
        if ($is_nested)
            $this->includeNestedObjectData();

        return $this->root_object;
    }

    private function includeNestedObjectData(): void
    {
        $properties = $this->root_reflector->getObjectProperties();
        $constraints = [self::convertParentClassToForeignKey($this->root_object->getClassName()) => $this->root_object->getId()];
        foreach ($properties as $property) {
            $key = $property->getName();
            $setVal = 'set' . self::processSnakeToPascal($key);
            $child_object_name = $property->getType2();
            $child_object = new $child_object_name;
            /* TODO - Currently - Get Record by record -> Get multiple records at once */
            $children_objects = $this->getListDataByConstraints($child_object, $constraints);
            $this->root_object->$setVal($children_objects);
        }
    }

    protected function getListDataByConstraints(object $object, $constraints = [], $pagination = null, $nested = true)
    {
        $tableName = self::getTableNameByObject($object);
        $reflector = new Reflection($object);
        $columns = self::getTableColumnsByReflector($reflector);
        $query = DatabaseQuery::getQueryWithConstraints($object, $columns, $constraints, $pagination, $reflector);
        if ($pagination)
            $this->handlePaginationHeaders($pagination);

        $result = self::executeQuery($query, $tableName, $columns, $object);
        if ($result) {
            $Class = get_class($object);
            while ($row = mysqli_fetch_assoc($result)) {
                $object = new $Class;
                foreach ($row as $key => $val) {
                    if ($val === null)
                        continue;
                    $key = self::convertToKey($key);
                    $setVal = 'set' . self::processSnakeToPascal($key);
                    $property = new ReflectionPropertyExtended(get_class($object), $key);
                    if (method_exists($object, $setVal) && $property->isObject() && $nested) {
                        $children[] = $setVal; /* TODO - Nested Objects */
                    } else if (method_exists($object, $setVal)) {
                        $type = $property->getType2();
                        $object->$setVal(self::castVariable($val, $type));
                    }
                }
                if ($nested) {
                    foreach ($reflector->getObjectProperties() as $property) {
                        $key = $property->getName();
                        $setVal = 'set' . self::processSnakeToPascal($key);
                        $child_object_name = $property->getType2();
                        $child_object = new $child_object_name;
                        /* TODO - Currently - Get Record by record -> Get multiple records at once */
                        $children_objects = $this->getListDataByConstraints($child_object, [self::convertParentClassToForeignKey($object->getClassName()) => $object->getId()]);
                        $object->$setVal($children_objects);
                    }
                }
                $objects[] = $object;
            }
        }

        return $objects ?? [];
    }

    private function handlePaginationHeaders($pagination): void
    {
        $query = DatabaseQuery::getCountQuery($this->object);
        $result = self::executeQuery($query, $this->tableName);
        $row = mysqli_fetch_assoc($result);
        $page = (int)$pagination['page'] + 1;
        if(!$pagination['limit'])
            $pagination['limit'] = $row['count'];
        $all_pages = ceil(($row['count'] / $pagination['limit']));
        header("Accept-Ranges: Yes");
        $ranges = "Content-Range: pages " . $page . "-" . $pagination['limit'] . '/' . $all_pages;
        header($ranges);
    }

    protected function insertRecordData(object $object, string $parent_class = NULL, $parent_id = NULL)
    {
        $query = DatabaseQuery::getInsertIntoQuery($object, $parent_class, $parent_id);
        $tableName = self::getTableNameByObject($object);
        $columns = self::getTableColumnsByObject($object);

        self::executeQuery($query, $tableName, $columns, $object, $parent_class);
        $id = DatabaseConnection::getInsertId();
        $child_objects = $object->getChildObjects();
        $class = $object->getClassName();
        $this->insertOrUpdateOrDeleteIterablyChildrenObjects($child_objects, $class, $id);

        return $id;
    }

    protected function updateData(object $object, $where_value, $where_column = "id", $parent_id = NULL, $parent_key = NULL)
    {
        $query = DatabaseQuery::getUpdateQuery($object, $where_value, $where_column, $parent_id, $parent_key);
        $tableName = self::getTableNameByObject($object);
        $columns = self::getTableColumnsByObject($object);
        $this->updateProtection($object, $where_value, $where_column, $tableName, $columns);
        $result = self::executeQuery($query, $tableName, $columns, $object);
        $affected_rows = self::getAffectedRows();
        $child_objects = $object->getChildObjects();
        if (!$child_objects)
            return;
        $class = $object->getClassName();
        $id = $object->getId();
        $this->insertOrUpdateOrDeleteIterablyChildrenObjects($child_objects, $class, $id);

        return $affected_rows;
    }

    private function insertOrUpdateOrDeleteIterablyChildrenObjects(iterable $iterables, string $parent_class, int $parent_id)
    {
        foreach ($iterables as $iterable) {
            foreach ($iterable ?? [] as $obj) {
                if ($obj->hasId())
                    $this->updateData($obj, $obj->getId(), "id", $parent_id, self::convertParentClassToForeignKey($parent_class));
                else
                    $this->insertRecordData($obj, $parent_class, $parent_id);
            }
        }
    }

    private function updateProtection($object, $where_value, $where_column, $tableName, $columns): void
    {
        $query = DatabaseQuery::getCountQuery($object, $where_value, $where_column);
        $result = self::executeQuery($query, $tableName, $columns, $object);
        $row = mysqli_fetch_assoc($result);
        $table = $object->getClassName();
        if ($row['count'] == 0)
            throw new UpdatingNotExistingResource("Table: $table");
    }

    protected function removeRecordData(object $object, $where_value)
    {
        $query = DatabaseQuery::getSoftDeleteQuery($object, $where_value);
        $tableName = self::getTableNameByObject($object);
        $columns = self::getTableColumnsByObject($object);
        $this->updateProtection($object, $where_value, 'id', $tableName, $columns);
        self::executeQuery($query, $tableName, $columns, $object);
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

    function _stringtodb($strParamString)
    {
        return addslashes($strParamString);
    }
}
