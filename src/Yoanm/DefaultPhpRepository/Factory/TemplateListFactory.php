<?php
namespace Yoanm\DefaultPhpRepository\Factory;

use Symfony\Component\Finder\Finder;
use Yoanm\DefaultPhpRepository\Command\RepositoryType;
use Yoanm\DefaultPhpRepository\Helper\TemplateHelper;
use Yoanm\DefaultPhpRepository\Model\Template;
use Yoanm\DefaultPhpRepository\Model\FolderTemplate;

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
            'git.readme' => $list['git.readme'],
            'git.license' => $list['git.license'],
            'git.contributing' => $list['git.contributing'],
            'git.gitignore' => $list['git.gitignore'],
            'composer.config' => $list['composer.config'],
            'phpcs.config' => $list['phpcs.config'],
            'phpunit.config' => $list['phpunit.config'],
            'phpunit.folders' => $list['phpunit.folders'],
            'behat.config' => $list['behat.config'],
            'behat.folders' => $list['behat.folders'],
            'ci.scrutinizer' => $list['ci.scrutinizer'],
        ];
        if (RepositoryType::LIBRARY === $repositoryType) {
            $orderedList['ci.travis'] = $list['ci.travis'];
        }

        return $orderedList;
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
