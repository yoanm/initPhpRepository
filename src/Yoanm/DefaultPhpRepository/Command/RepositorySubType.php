<?php
namespace Yoanm\DefaultPhpRepository\Command;

/**
 * Class RepositorySubType
 */
class RepositorySubType
{
    const PHP = 'php';
    const SYMFONY = 'symfony';

    /**
     * @return array All available sub types
     */
    public static function all()
    {
        return [
            self::PHP,
            self::SYMFONY
        ];
    }
}
