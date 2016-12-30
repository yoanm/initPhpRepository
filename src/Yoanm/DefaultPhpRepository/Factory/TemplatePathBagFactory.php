<?php
namespace Yoanm\DefaultPhpRepository\Factory;

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
            'template.init.readme' => $this->templateRegistry->getTemplatePath('README.md.tmpl'),
            'template.init.license' => $this->templateRegistry->getTemplatePath('LICENSE.tmpl'),
            'template.init.contributing' => $this->templateRegistry->getTemplatePath('CONTRIBUTING.md.tmpl'),
            // Git
            'template.git.gitignore' => $this->templateRegistry->getTemplatePath('.gitignore.tmpl'),
            // Composer
            'template.composer.config' => $this->templateRegistry->getTemplatePath('composer.json.tmpl'),
            // Tests
            'template.test.phpcs.config' => $this->templateRegistry->getTemplatePath('phpcs.xml.dist.tmpl'),
            'template.test.phpunit.config' => $this->templateRegistry->getTemplatePath('phpunit.xml.dist.tmpl'),
            'template.test.phpunit.folder' => $this->templateRegistry->getTemplatePath('tests'),
            'template.test.behat.config' => $this->templateRegistry->getTemplatePath('behat.yml.tmpl'),
            'template.test.behat.folder' => $this->templateRegistry->getTemplatePath('features'),
            // Continuous integration
            'template.ci.scrutinizer' => $this->templateRegistry->getTemplatePath('.scrutinizer.yml.tmpl'),
        ];

        if (Mode::PROJECT !== $mode) {
            $list['template.ci.travis'] = $this->templateRegistry->getTemplatePath('.travis.yml.tmpl');
        }

        return $list;
    }
}
