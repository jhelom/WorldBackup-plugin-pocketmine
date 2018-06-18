<?php
declare(strict_types=1);

namespace Jhelom\Core\Forms;


use pocketmine\Player;

/**
 * Class SimpleFormCloseAction
 * @package Jhelom\Core\Forms
 */
abstract class SimpleFormCloseAction
{
    /**
     * @param Player $player
     * @param SimpleForm $form
     */
    abstract public function onAction(Player $player, SimpleForm $form): void;
}
