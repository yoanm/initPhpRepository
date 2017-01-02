<?php
namespace Yoanm\InitPhpRepository\Resolver;

use Yoanm\InitPhpRepository\Command\RepositorySubType;
use Yoanm\InitPhpRepository\Command\RepositoryType;
use Yoanm\InitPhpRepository\Model\Template;

/**
 * Class NamespaceResolver
 */
class NamespaceResolver
{
    /** Repository type namespaces */
    const LIBRARY_NAMESPACE = 'library';
    const PROJECT_NAMESPACE = 'project';

    /** Repository sub type namespaces */
    const SYMFONY_LIBRARY_NAMESPACE = 'symfony-library';

    /**
     * @param Template[] $templateList
     * @param string     $repositoryType
     * @param string     $repositorySubType
     */
    public function resolve(array $templateList, $repositoryType, $repositorySubType)
    {
        // Update only templates that are not in base namespace
        if (RepositoryType::LIBRARY === $repositoryType) {
            $namespace = self::LIBRARY_NAMESPACE;

            $this->setNamespace($templateList, 'composer.config', $namespace);
            $this->setNamespace($templateList, 'git.gitignore', $namespace);
            $this->setNamespace($templateList, 'git.readme', $namespace);

            // Override with sub type
            if (RepositorySubType::SYMFONY === $repositorySubType) {
                $subNamespace = self::SYMFONY_LIBRARY_NAMESPACE;

                $this->setNamespace($templateList, 'ci.travis', $subNamespace);
                $this->setNamespace($templateList, 'git.readme', $subNamespace);
                $this->setNamespace($templateList, 'ci.scrutinizer', $subNamespace);
            }
        } else {
            $namespace = self::PROJECT_NAMESPACE;

            $this->setNamespace($templateList, 'composer.config', $namespace);
            $this->setNamespace($templateList, 'ci.scrutinizer', $namespace);
        }
    }

    /**
     * @param Template[] $templateList
     * @param string     $key
     * @param string     $namespace
     */
    protected function setNamespace(array $templateList, $key, $namespace)
    {
        if (isset($templateList[$key])) {
            $templateList[$key]->setNamespace($namespace);
        }
    }
}
