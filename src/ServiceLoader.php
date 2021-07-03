<?php

declare(strict_types=1);

namespace Lea;

class ServiceLoader
{
    private static $entity_classes = [];

    public static function load()
    {
        $core = __DIR__ . "/Core/**/*.php";
        $list = glob($core);
        $index2 = array_search(__DIR__ . "/Core/Database/DatabaseConnection.php", $list); /* Workaround */
        $index = array_search(__DIR__ . "/Core/Database/DatabaseUtil.php", $list); /* Workaround */
        $index3 = array_search(__DIR__ . "/Core/Controller/ControllerInterface.php", $list); /* Workaround */
        $index4 = array_search(__DIR__ . "/Core/Validator/ValidatorInterface.php", $list); /* Workaround */
        $index5 = array_search(__DIR__ . "/Core/Entity/FileInterface.php", $list); /* Workaround */
        $index6 = array_search(__DIR__ . "/Core/Entity/EntityInterface.php", $list); /* Workaround */
        $index7 = array_search(__DIR__ . "/Core/Repository/RepositoryInterface.php", $list); /* Workaround */
        $index8 = array_search(__DIR__ . "/Core/Database/DatabaseQuery.php", $list); /* Workaround */
        include $list[$index2]; /* Workaround */
        include $list[$index]; /* Workaround */
        include $list[$index3]; /* Workaround */
        include $list[$index4]; /* Workaround */
        include $list[$index5]; /* Workaround */
        include $list[$index6]; /* Workaround */
        include $list[$index7]; /* Workaround */
        include $list[$index8]; /* Workaround */
        foreach ($list as $filename) {
            require_once $filename;
        }
        $module = __DIR__ . "/**/**/**/*.php";
        $modules = glob($module);
        $index = array_search(__DIR__ . "/Core/Security/Service/AuthenticationService.php", $modules); /* Workaround */
        include $modules[$index]; /* Workaround */
        foreach ($modules as $filename) {
            require_once $filename;
            if (str_contains($filename, "Entity")) {
                $classes = get_declared_classes();
                self::$entity_classes[] = end($classes);
            }
        }
        $additional = __DIR__ . '/**/*.php';
        foreach (glob($additional) as $filename) {
            require_once $filename;
            if (str_contains($filename, "Entity")) {
                $classes = get_declared_classes();
                self::$entity_classes[] = end($classes);
            }
        }
    }

    public static function getLeaEntityClasses(): array
    {
        return self::$entity_classes;
    }

    public static function getLeaEntityClass(string $input): ?string
    {
        $result = array_filter(self::$entity_classes, function ($item) use ($input) {
            return (stripos("\\" . $item, "\\" . $input) !== false) ? true : false;
        });

        return empty($result) ? null : array_values($result)[0];
    }
}
