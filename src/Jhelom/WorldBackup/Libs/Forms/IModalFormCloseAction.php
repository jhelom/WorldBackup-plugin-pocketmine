<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs\Forms;


use pocketmine\Player;

/**
 * Interface IModalFormCloseAction
 */
interface IModalFormCloseAction
{
    /**
     * @param Player $player
     * @param ModalForm $form
     * @param bool $isAccepted
     */
    public function onModalFormClose(Player $player, ModalForm $form, bool $isAccepted): void;
}

