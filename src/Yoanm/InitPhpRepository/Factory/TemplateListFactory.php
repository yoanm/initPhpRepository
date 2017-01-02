<?php
namespace Yoanm\InitPhpRepository\Factory;

use Symfony\Component\Finder\Finder;
use Yoanm\InitPhpRepository\Command\RepositoryType;
use Yoanm\InitPhpRepository\Helper\TemplateHelper;
use Yoanm\InitPhpRepository\Model\FolderTemplate;
use Yoanm\InitPhpRepository\Model\Template;
use Yoanm\InitPhpRepository\Resolver\NamespaceResolver;

/**
 * Class TemplateListFactory
 */
class TemplateListFactory
{
    /**
     * @param string $repositoryType
     * @param string $repositorySubType
     *
     * @return Template[]
     */
    public function create($repositoryType, $repositorySubType)
    {
        $templateList = $this->getTemplateList($repositoryType);

        (new NamespaceResolver())->resolve($templateList, $repositoryType, $repositorySubType);

        // Reorder final list
        $orderedList = [
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
            if (isset($templateList[$key])) {
                $finalList[$key] = $templateList[$key];
            }
        }

        return $finalList;
    }

    /**
     * @param $repositoryType
     *
     * @return array
     */
    protected function getTemplateList($repositoryType)
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

        $templateList = [];
        foreach ($fileTemplateList as $templateId => $templateName) {
            $templateList[$templateId] = $this->createTemplate($templateId, $templateName);
        }

        $folderTemplateList = [
            'phpunit.folders' => 'tests',
            'behat.folders' => 'features',
        ];

        $basePath = TemplateHelper::getTemplateBasePath();
        foreach ($folderTemplateList as $templateId => $templateFolder) {
            $folderTemplate = $templateList[$templateId] = new FolderTemplate($templateId, $templateFolder);
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
        return $templateList;
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
