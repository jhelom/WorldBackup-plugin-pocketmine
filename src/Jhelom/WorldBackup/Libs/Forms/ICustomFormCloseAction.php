<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs\Forms;


use pocketmine\Player;


/**
 * Interface ICustomFormCloseAction
 */
interface ICustomFormCloseAction
{
    /**
     * @param Player $player
     * @param CustomForm $form
     */
    public function onCustomFormClose(Player $player, CustomForm $form): void;
}
