<?php
namespace Yoanm\DefaultPhpRepository\Helper;

/**
 * Class PathHelper
 */
class PathHelper
{
    /**
     * @param string[] $componentList
     *
     * @return string
     */
    public static function implodePathComponentList(array $componentList)
    {
        $path = '';
        foreach ($componentList as $component) {
            $path .= self::appendPathSeparator($component);
        }

        return $path;
    }

    /**
     * @param string $component
     *
     * @return string
     */
    public static function appendPathSeparator($component)
    {
        if (strrpos(self::separator(), $component) === (strlen($component) - 1)) {
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
        return DIRECTORY_SEPARATOR;
    }
}
