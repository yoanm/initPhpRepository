<?php
namespace Yoanm\DefaultPhpRepository\Command;

/**
 * Class Mode
 */
class Mode
{
    const PHP_LIBRARY = 'library';
    const SYMFONY_LIBRARY = 'symfony-library';
    const PROJECT = 'project';

    /**
     * @return array All available modes
     */
    public static function all()
    {
        return [
            self::PHP_LIBRARY,
            self::SYMFONY_LIBRARY,
            self::PROJECT
        ];
    }
}
