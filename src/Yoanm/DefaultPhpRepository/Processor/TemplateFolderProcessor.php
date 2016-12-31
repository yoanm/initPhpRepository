<?php
namespace Yoanm\DefaultPhpRepository\Processor;

use Symfony\Component\Finder\Finder;
use Yoanm\DefaultPhpRepository\Helper\PathHelper;
use Yoanm\DefaultPhpRepository\Registry\TemplateRegistry;

/**
 * Class TemplateFolderProcessor
 */
class TemplateFolderProcessor extends TemplateFileProcessor
{
    /**
     * @param string $templateFolderPath
     * @param string $templateOutputRootDir
     */
    public function process($templateFolderPath, $templateOutputRootDir = '.')
    {
        $finder = new Finder();
        $finder->files()->in($templateFolderPath);

        /** @var \SplFileInfo $file */
        foreach ($finder as $file) {
            $templateFilename = $file->getFilename();
            $templateFilePath = $file->getRealPath();
            $templateOutputFileDir = $this->resolveTemplateOutputDir(
                $templateFilename,
                $templateFilePath,
                $templateOutputRootDir
            );

            $templateOutputFilePath = sprintf(
                '%s%s',
                PathHelper::appendPathSeparator($templateOutputFileDir),
                $this->getHelper()->resolveOutputFilename($templateFilename)
            );

            $this->getHelper()->dumpTemplate($templateFilePath, $templateOutputFilePath);
        }
    }

    /**
     * @param string $templateFilename
     * @param string $templateFilePath
     * @param string $templateOutputRootDir
     * @return string
     */
    protected function resolveTemplateOutputDir(
        $templateFilename,
        $templateFilePath,
        $templateOutputRootDir
    ) {
        $resolved = sprintf(
            '%s%s',
            PathHelper::appendPathSeparator($templateOutputRootDir),
            str_replace([TemplateRegistry::getRootTemplateDir(), $templateFilename], '', $templateFilePath)
        );

        return $resolved;
    }
}
