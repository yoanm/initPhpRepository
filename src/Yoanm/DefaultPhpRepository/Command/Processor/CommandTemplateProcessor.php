<?php
namespace Yoanm\DefaultPhpRepository\Command\Processor;

use Yoanm\DefaultPhpRepository\Command\Helper\CommandTemplateHelper;
use Yoanm\DefaultPhpRepository\Processor\TemplateProcessor;

/**
 * Class CommandTemplateProcessor
 * @package Yoanm\DefaultPhpRepository\Processor
 */
class CommandTemplateProcessor extends TemplateProcessor
{
    /**
     * @param CommandTemplateHelper $commandTemplateHelper
     */
    public function __construct(CommandTemplateHelper $commandTemplateHelper) {
        parent::__construct($commandTemplateHelper);

    }
}
