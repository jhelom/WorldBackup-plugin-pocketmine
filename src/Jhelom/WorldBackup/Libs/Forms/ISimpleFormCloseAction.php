<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs\Forms;


use pocketmine\Player;

/**
 * Interface ISimpleFormCloseAction
 */
interface ISimpleFormCloseAction
{
    /**
     * @param Player $player
     * @param SimpleForm $form
     */
    public function onAction(Player $player, SimpleForm $form): void;
}
