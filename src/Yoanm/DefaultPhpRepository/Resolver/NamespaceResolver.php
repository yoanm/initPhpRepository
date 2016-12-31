<?php
namespace Yoanm\DefaultPhpRepository\Resolver;

use Yoanm\DefaultPhpRepository\Command\RepositorySubType;
use Yoanm\DefaultPhpRepository\Command\RepositoryType;
use Yoanm\DefaultPhpRepository\Model\Template;

/**
 * Class NamespaceResolver
 */
class NamespaceResolver
{
    /** Repository type namespaces */
    const LIBRARY_NAMESPACE = 'library';

    /** Repository sub type namespaces */
    const SYMFONY_LIBRARY_NAMESPACE = 'symfony-library';

    /**
     * @param Template[] $templateList
     * @param string     $repositoryType
     * @param string     $repositorySubType
     */
    public function resolve(array $templateList, $repositoryType, $repositorySubType)
    {
        // Override base namespace for specific files
        if (RepositoryType::LIBRARY === $repositoryType) {
            $namespace = self::LIBRARY_NAMESPACE;

            $templateList['composer.config']->setNamespace($namespace);
            $templateList['git.gitignore']->setNamespace($namespace);
            $templateList['git.readme']->setNamespace($namespace);

            // Override with sub type
            if (RepositorySubType::SYMFONY === $repositorySubType) {
                $subNamespace = self::SYMFONY_LIBRARY_NAMESPACE;
                $templateList['ci.travis']->setNamespace($subNamespace);
                $templateList['git.readme']->setNamespace($subNamespace);
            }
        }
    }
}
