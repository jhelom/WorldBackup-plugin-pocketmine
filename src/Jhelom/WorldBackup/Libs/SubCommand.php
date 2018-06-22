<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs;


/**
 * Class SubCommand
 */
abstract class SubCommand implements ICommandInvoker
{
    use SubCommandDispatchTrait;
}