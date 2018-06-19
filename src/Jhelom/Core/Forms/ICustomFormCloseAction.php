<?php
declare(strict_types=1);

namespace Jhelom\Core\Forms;


use pocketmine\Player;


/**
 * Interface ICustomFormCloseAction
 * @package Jhelom\Core\Forms
 */
interface ICustomFormCloseAction
{
    /**
     * @param Player $player
     * @param CustomForm $form
     */
    public function onCustomFormClose(Player $player, CustomForm $form): void;
}
