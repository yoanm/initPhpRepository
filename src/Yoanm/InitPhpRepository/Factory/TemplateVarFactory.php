<?php
namespace Yoanm\InitPhpRepository\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class TemplateVarFactory
 */
class TemplateVarFactory
{
    /**
     * @return array
     *
     * @throws \Exception
     */
    public function create()
    {
        $bag = new ParameterBag();

        // - Git
        $this->setGitVariables($bag);
        // - Global
        $this->setGlobalVariables($bag);
        // - Composer
        $this->setComposerVariables($bag);
        // - Autoload
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

        preg_match(
            '#github\.com(?:(?:(.*)\/)|(?::(.*)\.git))$#m',
            shell_exec('git remote -v show -n origin'),
            $matches
        );
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
     */
    protected function setGlobalVariables(ParameterBag $bag)
    {
        $bag->set('id', str_replace('_', '-', ContainerBuilder::underscore($bag->get('github.id'))));
        $bag->set('name', ucwords(str_replace('_', ' ', ContainerBuilder::underscore($bag->get('github.id')))));
    }

    /**
     * @param ParameterBag $bag
     */
    protected function setComposerVariables(ParameterBag $bag)
    {
        $bag->set('composer.package.name', str_replace('_', '-', ContainerBuilder::underscore($bag->get('git.id'))));
    }

    /**
     * @param ParameterBag $bag
     */
    protected function setAutoloadVariables(ParameterBag $bag)
    {
        // Namespaces
        $bag->set(
            'autoload.namespace.base',
            sprintf(
                '%s\\%s',
                ContainerBuilder::camelize($bag->get('github.vendor')),
                ContainerBuilder::camelize($bag->get('github.id'))
            )
        );
        $bag->set('autoload.namespace.tests.technical.unit', 'Technical\Unit\%autoload.namespace.base%');
        $bag->set('autoload.namespace.tests.technical.integration', 'Technical\Integration\%autoload.namespace.base%');
        $bag->set('autoload.namespace.tests.functional.base', 'Functional\%autoload.namespace.base%');
        $bag->set(
            'autoload.namespace.tests.functional.behat_context',
            '%autoload.namespace.tests.functional.base%\BehatContext'
        );
        // Folders
        $bag->set('autoload.folders.source', 'src');
        $bag->set(
            'autoload.folders.source_psr0',
            sprintf(
                '%s/%s/%s',
                '%autoload.folders.source%',
                ContainerBuilder::camelize($bag->get('github.vendor')),
                ContainerBuilder::camelize($bag->get('github.id'))
            )
        );

        $bag->set('autoload.folders.test.phpunit', 'tests');
        $bag->set('autoload.folders.test.behat', 'features');

        $bag->set('autoload.folders.test.technical.base', '%autoload.folders.test.phpunit%/Technical');

        $bag->set('autoload.folders.test.technical.unit', '%autoload.folders.test.technical.base%/Unit');
        $bag->set('autoload.folders.test.technical.integration', '%autoload.folders.test.technical.base%/Integration');

        $bag->set('autoload.folders.test.functional.phpunit', '%autoload.folders.test.phpunit%/Functional');
        $bag->set('autoload.folders.test.functional.behat_context', '%autoload.folders.test.behat%/bootstrap');
    }
}
