<?php
declare(strict_types=1);

namespace Jhelom\Core\Forms;


use pocketmine\Player;

/**
 * Interface ISimpleFormCloseAction
 * @package Jhelom\Core\Forms
 */
interface ISimpleFormCloseAction
{
    /**
     * @param Player $player
     * @param SimpleForm $form
     */
    public function onAction(Player $player, SimpleForm $form): void;
}
