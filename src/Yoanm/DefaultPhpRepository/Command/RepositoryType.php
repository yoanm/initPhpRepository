<?php
namespace Yoanm\DefaultPhpRepository\Command;

/**
 * Class RepositoryType
 */
class RepositoryType
{
    const LIBRARY = 'library';
    const PROJECT = 'project';

    /**
     * @return array All available modes
     */
    public static function all()
    {
        return [
            self::LIBRARY,
            self::PROJECT
        ];
    }
}
