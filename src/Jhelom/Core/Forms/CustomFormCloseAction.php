<?php
declare(strict_types=1);

namespace Jhelom\Core\Forms;


use pocketmine\Player;

/**
 * Class CustomFormCloseAction
 * @package Jhelom\Core\Forms
 */
abstract class CustomFormCloseAction
{
    /**
     * @param Player $player
     * @param CustomForm $form
     */
    abstract public function onAction(Player $player, CustomForm $form): void;
}
