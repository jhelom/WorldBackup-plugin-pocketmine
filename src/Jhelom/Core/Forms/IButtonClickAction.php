<?php
declare(strict_types=1);

namespace Jhelom\Core\Forms;


use pocketmine\Player;


/**
 * Interface IButtonClickAction
 * @package Jhelom\Core\Forms
 */
interface IButtonClickAction
{
    /**
     * @param Player $player
     * @param SimpleForm $form
     * @param ButtonElement $button
     */
    public function onButtonClick(Player $player, SimpleForm $form, ButtonElement $button): void;
}
