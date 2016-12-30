<?php
namespace Yoanm\DefaultPhpRepository\Factory;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Yoanm\DefaultPhpRepository\Command\Mode;
use Yoanm\DefaultPhpRepository\Registry\TemplateRegistry;

/**
 * Class TemplatePathListFactory
 */
class TemplatePathListFactory
{
    /**
     * @param string $mode
     *
     * @return ParameterBag
     */
    public function load($mode = Mode::PHP_LIBRARY)
    {
        $templateRegistry = new TemplateRegistry();

        $list = [
            // Init
            'template.init.license' => $templateRegistry->getTemplatePathFor('LICENSE', $mode),
            'template.init.contributing' => $templateRegistry->getTemplatePathFor('CONTRIBUTING.md', $mode),
            // Git
            'template.git.gitignore' => $templateRegistry->getTemplatePathFor('.gitignore', $mode),
            // Composer
            'template.composer.config' => $templateRegistry->getTemplatePathFor('composer.json', $mode),
            // Tests
            'template.test.phpcs' => $templateRegistry->getTemplatePathFor('phpcs.xml.dist', $mode),
            'template.test.phpunit' => $templateRegistry->getTemplatePathFor('phpunit.xml.dist', $mode),
            'template.test.behat' => $templateRegistry->getTemplatePathFor('behat.yml', $mode),
            // Continuous integration
            'template.ci.scrutinizer' => $templateRegistry->getTemplatePathFor('.scrutinizer.yml', $mode),
        ];

        if (Mode::PROJECT !== $mode) {
            $list['template.ci.travis'] = $templateRegistry->getTemplatePathFor('.travis.yml', $mode);
        }

        return $list;
    }
}
