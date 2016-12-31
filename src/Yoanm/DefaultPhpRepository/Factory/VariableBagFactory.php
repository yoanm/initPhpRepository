<?php
namespace Yoanm\DefaultPhpRepository\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Yoanm\DefaultPhpRepository\Command\Mode;

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
    public function load($mode)
    {
        $bag = new ParameterBag();

        $this->setGlobalVar($bag, $mode);
        $this->setExtraVar($bag, $mode);

        $bag->resolve();

        return $bag;
    }

    /**
     * @param ParameterBag $bag
     * @throws \Exception
     */
    protected function setGlobalVar(ParameterBag $bag, $mode)
    {
        // - Git variables
        $gitUsername = trim(shell_exec('git config --global user.name'));
        if ('' === $gitUsername) {
            throw new \Exception("Git username cannot be empty ! Use git config user.name 'NAME' to define it");
        }
        // Ensure CamelCase style for git username
        $gitUsername = ContainerBuilder::camelize($gitUsername);

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
        $bag->set('git.repository.url', sprintf('github.com/%s', $githubRepositoryUrlId));

        // - Composer variables
        $composerPackageName = str_replace('_', '-', ContainerBuilder::underscore($githubRepositoryUrlId));

        $bag->set('composer.package.name', $composerPackageName);
        $bag->set('composer.config.type', Mode::PROJECT === $mode ? 'project' : 'library');

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
        $bag->set('autoload.namespace.psr_4', sprintf('%s\\\\', $autoloadPsr0Namespace));

        $id = preg_replace('#[^/]+/(.*)#', '\1', $composerPackageName);

        $bag->set('id', $id);
        $bag->set('name', ucwords(str_replace('-', ' ', $id)));

        $bag->set('current.year', date('Y'));
    }

    /**
     * @param ParameterBag $bag
     * @param string       $mode
     */
    protected function setExtraVar(ParameterBag $bag, $mode)
    {
        $extraList = [
            'gitignore.extra' => '',
            'composer.config.extra.description' => '',
            'composer.config.extra.keyword' => '',
            'composer.config.extra.version' => '',
            'composer.config.extra.provide' => '',
            'composer.config.extra.suggest' => '',
            'travis.config.extra.env' => '',
            'travis.config.extra.install' => '',
            'readme.extra.badges' => '',
            'readme.extra.badges.travis' => '',
            'readme.extra.install_steps' => 'composer require %composer.package.name%',
        ];

        if (Mode::PROJECT !== $mode) {
// @codingStandardsIgnoreStart
            $extraList['readme.extra.badges'] = <<<EOS

[![Travis Build Status](https://img.shields.io/travis/%git.repository.url_id%/master.svg?label=travis)](https://travis-ci.org/%git.repository.url_id%) [![PHP Versions](https://img.shields.io/badge/php-5.5%%20%%2F%%205.6%%20%%2F%%207.0-8892BF.svg)](https://php.net/)%readme.extra.badges.travis%

EOS;
// @codingStandardsIgnoreEnd
            // Git ignore - only project need a composer.lock
            $extraList['gitignore.extra'] = <<<EOS

composer.lock
EOS;
            // Composer
            $extraList['composer.config.extra.description'] = <<<EOS

  "description": "XXX",
EOS;
            $extraList['composer.config.extra.keyword'] = <<<EOS

  "keywords": ["XXX"],
EOS;
            $extraList['composer.config.extra.version'] = <<<EOS

  "version": "0.1.0",
EOS;
            $extraList['composer.config.extra.provide'] = <<<EOS

  "provide": {
    "yoanm/XXX": "~0.1"
  },
EOS;
            $extraList['composer.config.extra.suggest'] = <<<EOS

  "suggest": {
    "YYY/ZZZ": "Description"
  },
EOS;
        } else {
            // Readme - install steps
            $extraList['readme.extra.install_steps'] = <<<EOS
git clone git@github.com:%git.repository.url_id%.git
cd %git.repository.url_id_without_vendor%
composer build
EOS;
        }

        if (Mode::SYMFONY_LIBRARY === $mode) {
            // Travis
            $extraList['travis.config.extra.env'] = <<<EOS

env:
  - SYMFONY_VERSION=2.7.*
  - SYMFONY_VERSION=2.8.*
  - SYMFONY_VERSION=3.*

EOS;
            $extraList['travis.config.extra.install'] = <<<EOS
  - composer require "symfony/symfony:\${SYMFONY_VERSION}"

EOS;
            // Readme - extra travis badges
// @codingStandardsIgnoreStart
            $extraList['readme.extra.badges.travis'] = <<<EOS
 [![Symfony Versions](https://img.shields.io/badge/Symfony-2.7%%20%%2F%%202.8%%20%%2F%%203.0-312933.svg)](https://symfony.com/)
EOS;
// @codingStandardsIgnoreEnd
        }

        foreach ($extraList as $extraKey => $extraValue) {
            $bag->set($extraKey, $extraValue);
        }
    }
}
