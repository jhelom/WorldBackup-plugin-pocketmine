<?php
declare(strict_types=1);

namespace Jhelom\Core\Forms;


use pocketmine\Player;


/**
 * Interface ICustomFormSubmitAction
 * @package Jhelom\Core\Forms
 */
interface ICustomFormSubmitAction
{
    /**
     * @param Player $player
     * @param CustomForm $form
     * @param CustomFormValues $values
     */
    public function onCustomFormSubmit(Player $player, CustomForm $form, CustomFormValues $values): void;
}
