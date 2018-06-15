<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Forms;


use pocketmine\Player;

/**
 * Class ModalFormAction
 * @package Jhelom\WorldBackup\Forms
 */
abstract class ModalFormAction
{
    /**
     * @param Player $player
     * @param ModalForm $form
     */
    abstract public function onAction(Player $player, ModalForm $form): void;
}

