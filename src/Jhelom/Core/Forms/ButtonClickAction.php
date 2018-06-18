<?php
declare(strict_types=1);

namespace Jhelom\Core\Forms;


use pocketmine\Player;

/**
 * Class ButtonClickAction
 * @package Jhelom\Core\Forms
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
