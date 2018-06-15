<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Forms;


use pocketmine\Player;

/**
 * Class ButtonClickAction
 * @package Jhelom\WorldBackup\Forms
 */
abstract class ButtonClickAction
{
    /**
     * @param Player $player
     * @param SimpleForm $form
     * @param ButtonElement $button
     */
    abstract public function onAction(Player $player, SimpleForm $form, ButtonElement $button): void;
}
