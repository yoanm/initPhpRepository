<?php
namespace Yoanm\DefaultPhpRepository\Helper;

/**
 * Class PathHelper
 */
class PathHelper
{
    /**
     * @param string $component
     *
     * @return string
     */
    public static function appendPathSeparator($component)
    {
        if (strrpos($component, self::separator()) === (strlen($component) - 1)) {
            return $component;
        }

        return sprintf(
            '%s%s',
            $component,
            self::separator()
        );
    }

    public static function separator()
    {
        return '/';
    }
}
