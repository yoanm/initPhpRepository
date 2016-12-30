<?php
namespace Yoanm\DefaultPhpRepository\Factory;

use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Yoanm\DefaultPhpRepository\Command\Mode;
use Yoanm\DefaultPhpRepository\Registry\TemplateRegistry;

/**
 * Class TemplatePathBagFactory
 */
class TemplatePathBagFactory
{
    /** @var TemplateRegistry */
    private $templateRegistry;

    public function __construct()
    {
        $this->templateRegistry = new TemplateRegistry();
    }

    /**
     * @param $mode
     * 
     * @return array
     */
    public function load($mode)
    {
        return $this->getTemplatePathList($mode);
    }

    /**
     * @param string $mode
     *
     * @return array
     */
    protected function getTemplatePathList($mode)
    {
        $list = [
            // Init
            'template.init.readme' => $this->templateRegistry->getTemplatePath('README.md'),
            'template.init.license' => $this->templateRegistry->getTemplatePath('LICENSE'),
            'template.init.contributing' => $this->templateRegistry->getTemplatePath('CONTRIBUTING.md'),
            // Git
            'template.git.gitignore' => $this->templateRegistry->getTemplatePath('.gitignore'),
            // Composer
            'template.composer.config' => $this->templateRegistry->getTemplatePath('composer.json'),
            // Tests
            'template.test.phpcs' => $this->templateRegistry->getTemplatePath('phpcs.xml.dist'),
            'template.test.phpunit' => $this->templateRegistry->getTemplatePath('phpunit.xml.dist'),
            'template.test.behat' => $this->templateRegistry->getTemplatePath('behat.yml'),
            // Continuous integration
            'template.ci.scrutinizer' => $this->templateRegistry->getTemplatePath('.scrutinizer.yml'),
        ];

        if (Mode::PROJECT !== $mode) {
            $list['template.ci.travis'] = $this->templateRegistry->getTemplatePath('.travis.yml');
        }

        return $list;
    }
}
