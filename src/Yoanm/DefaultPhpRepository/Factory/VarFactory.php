<?php
namespace Yoanm\DefaultPhpRepository\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Yoanm\DefaultPhpRepository\Command\Mode;

/**
 * Class VarFactory
 */
class VarFactory
{
    /**
     * @return []
     *
     * @throws \Exception
     */
    public function create($repositoryType)
    {
        $bag = new ParameterBag();

        // - Git variables
        $this->setGitVariables($bag);

        $id = str_replace('_', '-', ContainerBuilder::underscore($bag->get('github.id')));
        $bag->set('id', $id);
        $bag->set('name', ucwords(str_replace('-', ' ', $id)));

        // - Composer variables
        $this->setComposerVariables($bag, $repositoryType);
        // - Autoloading
        $this->setAutoloadVariables($bag);

        $bag->resolve();

        return $bag->all();
    }

    /**
     * @param ParameterBag $bag
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function setGitVariables(ParameterBag $bag)
    {
        $gitUsername = trim(shell_exec('git config --global user.name'));
        if ('' === $gitUsername) {
            throw new \Exception("Git username cannot be empty ! Use git config user.name 'NAME' to define it");
        }

        preg_match('#github\.com(?:(?:(.*)\/)|(?::(.*)\.git))$#m', shell_exec('git remote -v show -n origin'), $matches);
        $gitId = trim($matches[1]);
        $gitId = '' === $gitId
            ? trim($matches[2])
            : $gitId
        ;
        if ('' === $gitId) {
            throw new \Exception("Unabled to define github id !");
        }

        $bag->set('git.username', $gitUsername);
        $bag->set('git.id', $gitId);
        $bag->set('git.url', 'github.com/%git.id%');

        list($vendor, $id) = explode('/', $gitId);
        $bag->set('github.vendor', $vendor);
        $bag->set('github.id', $id);
        $bag->set('github.url', 'https://%git.url%');
    }

    /**
     * @param ParameterBag $bag
     * @param $repositoryType
     */
    protected function setComposerVariables(ParameterBag $bag, $repositoryType)
    {
        $bag->set(
            'composer.package.name',
            str_replace(
                '_',
                '-',
                ContainerBuilder::underscore($bag->get('git.id'))
            )
        );
        $bag->set('composer.config.type', $repositoryType);
    }

    /**
     * @param ParameterBag $bag
     */
    protected function setAutoloadVariables(ParameterBag $bag)
    {
        $bag->set(
            'autoload.namespace',
            sprintf(
                '%s\\%s',
                ContainerBuilder::camelize($bag->get('github.vendor')),
                ContainerBuilder::camelize($bag->get('github.id'))
            )
        );
    }

}
