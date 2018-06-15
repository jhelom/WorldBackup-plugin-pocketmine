<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\BackupForms;


use Jhelom\WorldBackup\Forms\ModalForm;
use Jhelom\WorldBackup\Forms\ModalFormAction;
use Jhelom\WorldBackup\Messages;
use Jhelom\WorldBackup\Services\WorldBackupService;
use pocketmine\Player;

/**
 * Class BackupConfirmForm
 * @package Jhelom\WorldBackup\BackupForms
 */
class BackupConfirmForm extends ModalForm
{
    /**
     * BackupConfirmForm constructor.
     * @param string $world
     */
    function __construct(string $world)
    {
        parent::__construct(Messages::backupConfirmFormTitle(), Messages::backupConfirmFormContent($world));

        $this->onAccept(new class($world) extends ModalFormAction
        {
            private $world;

            /**
             *  constructor.
             * @param string $world
             */
            public function __construct(string $world)
            {
                $this->world = $world;
            }

            /**
             * @param Player $player
             * @param ModalForm $form
             */
            public function onAction(Player $player, ModalForm $form): void
            {
                WorldBackupService::getInstance()->backup($this->world);
                $player->sendMessage(Messages::backupCompleted($this->world));
            }
        });

        $this->onDismiss(new class extends ModalFormAction
        {
            /**
             * @param Player $player
             * @param ModalForm $form
             */
            public function onAction(Player $player, ModalForm $form): void
            {
                TopForm::send($player);
            }
        });
    }
}