<?php

declare(strict_types=1);

namespace Lea\Core\Database;


final class DatabaseQuery extends DatabaseUtil
{
    public static function getInsertQueryPart(object $object): string
    {
        $table_name = self::getTableNameByObject($object);
        $columns = self::convertKeyToColumn()
        $query = 'INSERT INTO ' . $table_name . $columns . $values;

        return $query;
    }
}
