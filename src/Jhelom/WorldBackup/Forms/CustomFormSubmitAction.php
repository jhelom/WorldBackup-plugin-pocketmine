<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Forms;


use pocketmine\Player;

/**
 * Class CustomFormSubmitAction
 * @package Jhelom\WorldBackup\Forms
 */
abstract class CustomFormSubmitAction
{
    /**
     * @param Player $player
     * @param CustomForm $form
     * @param CustomFormValues $values
     */
    abstract public function onAction(Player $player, CustomForm $form, CustomFormValues $values): void;
}
