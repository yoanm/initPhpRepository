<?php
namespace Yoanm\DefaultPhpRepository\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Yoanm\DefaultPhpRepository\Helper\PathHelper;

/**
 * Class VariableBagFactory
 */
class VariableBagFactory
{
    /**
     * @return ParameterBag
     *
     * @throws \Exception
     */
    public function load()
    {
        $bag = new ParameterBag();

        // - Git variables
        $gitUsername=trim(shell_exec('git config --global user.name'));
        if ('' === $gitUsername) {
            throw new \Exception("Git username cannot be empty ! Use git config user.name 'NAME' to define it");
        }
        // Ensure CamelCase style for git username
        $gitUsername=ContainerBuilder::camelize($gitUsername);

        $remoteListOutput = shell_exec('git remote -v show -n origin');
        if (0 === preg_match('#github\.com:(.*)(?:\.git)$#m', $remoteListOutput, $matches)) {
            preg_match('#github\.com\/([^\/]+\/[^\/]+)#m', $remoteListOutput, $matches);
        }
        $githubRepositoryUrlId = trim($matches[1]);
        if ('' === $githubRepositoryUrlId) {
            throw new \Exception("Unabled to define github repository url id !");
        }

        $bag->set('git.repository.url_id', $githubRepositoryUrlId);
        $tmp = explode('/', $githubRepositoryUrlId);
        $bag->set('git.repository.url_id_without_vendor', array_pop($tmp));
        $bag->set('git.repository.url', sprintf('github.com%s%s', PathHelper::separator(), $githubRepositoryUrlId));

        // - Composer variables
        $composerPackageName = str_replace('_', '-', ContainerBuilder::underscore($githubRepositoryUrlId));

        $bag->set('composer.package.name', $composerPackageName);

        // - Autoloading variables
        $autoloadNamespace = implode(
            '\\',
            array_map(
                function ($part) {
                    return ContainerBuilder::camelize($part);
                },
                explode('/', $githubRepositoryUrlId)
            )
        );
        $autoloadPsr0Namespace = str_replace('\\', '\\\\', $autoloadNamespace);

        $bag->set('git.username', $gitUsername);
        $bag->set('autoload.namespace', $autoloadNamespace);
        $bag->set('autoload.namespace.psr_0', $autoloadPsr0Namespace);
        $bag->set('autoload.namespace.psr_4', sprintf('%s\\\\',$autoloadPsr0Namespace));

        $id = preg_replace('#[^/]+/(.*)#', '\1', $composerPackageName);

        $bag->set('id', $id);
            $bag->set('name', ucwords(
            str_replace(
                '-',
                ' ',
                $id
            )
        ));

        $bag->set('current.year', date('Y'));

        $bag->resolve();

        return $bag;
    }
}
