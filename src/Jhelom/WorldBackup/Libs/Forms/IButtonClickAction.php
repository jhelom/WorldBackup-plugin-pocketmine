<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs\Forms;


use pocketmine\Player;


/**
 * Interface IButtonClickAction
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
