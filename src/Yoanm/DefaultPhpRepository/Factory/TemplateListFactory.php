<?php
namespace Yoanm\DefaultPhpRepository\Factory;

use Symfony\Component\Finder\Finder;
use Yoanm\DefaultPhpRepository\Command\RepositoryType;
use Yoanm\DefaultPhpRepository\Helper\TemplateHelper;
use Yoanm\DefaultPhpRepository\Model\FolderTemplate;
use Yoanm\DefaultPhpRepository\Model\Template;

/**
 * Class TemplateListFactory
 */
class TemplateListFactory
{
    /**
     * @param string $repositoryType
     *
     * @return Template[]
     */
    public function create($repositoryType)
    {
        $fileTemplateList = [
            'git.readme' => 'README.md.twig',
            'git.license' => 'LICENSE.twig',
            'git.contributing' => 'CONTRIBUTING.md.twig',
            'git.gitignore' => '.gitignore.twig',
            'composer.config' => 'composer.json.twig',
            'phpcs.config' => 'phpcs.xml.dist.twig',
            'phpunit.config' => 'phpunit.xml.dist.twig',
            'behat.config' => 'behat.yml.twig',
            'ci.scrutinizer' => '.scrutinizer.yml.twig',
        ];

        if (RepositoryType::LIBRARY === $repositoryType) {
            $fileTemplateList['ci.travis'] = '.travis.yml.twig';
        }

        $list = [];
        foreach ($fileTemplateList as $templateId => $templateName) {
            $list[$templateId] = $this->createTemplate($templateId, $templateName);
        }

        $folderTemplateList = [
            'phpunit.folders' => 'tests',
            'behat.folders' => 'features',
        ];

        $basePath = TemplateHelper::getTemplateBasePath();
        foreach ($folderTemplateList as $templateId => $templateFolder) {
            $folderTemplate = $list[$templateId] = new FolderTemplate($templateId, $templateFolder);
            // Iterate over files
            $count = 0;
            $path = realpath(sprintf('%s/base/%s', $basePath, $templateFolder));
            $toRemove = sprintf('%s/base/', $basePath);
            foreach ((new Finder())->files()->in($path) as $file) {
                $folderTemplate->addFile(
                    $this->createTemplate(
                        sprintf('%s.%s', $templateId, $count),
                        str_replace($toRemove, '', $file->getPathname())
                    )
                );
                $count++;
            }
        }

        // Reorder final list
        $orderedList =  [
            'git.readme',
            'git.license',
            'git.contributing',
            'git.gitignore',
            'composer.config',
            'phpcs.config',
            'phpunit.config',
            'phpunit.folders',
            'behat.config',
            'behat.folders',
            'ci.scrutinizer',
            'ci.travis',
        ];

        $finalList = [];
        foreach ($orderedList as $key) {
            if (isset($list[$key])) {
                $finalList[$key] = $list[$key];
            }
        }

        return $finalList;
    }

    /**
     * @param string $id
     * @param string $templateName
     *
     * @return Template
     */
    protected function createTemplate($id, $templateName)
    {
        return new Template($id, $templateName, $this->getOutputFilePath($templateName));
    }


    /**
     * @param string $templateName
     *
     * @return string
     */
    protected function getOutputFilePath($templateName)
    {
        return str_replace('.twig', '', $templateName);
    }
}
