<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\BackupForms;


use Jhelom\WorldBackup\Forms\ModalForm;
use Jhelom\WorldBackup\Forms\ModalFormAction;
use Jhelom\WorldBackup\Messages;
use Jhelom\WorldBackup\Services\WorldBackupService;
use pocketmine\Player;

/**
 * Class RestoreConfirmForm
 * @package Jhelom\WorldBackup\BackupForms
 */
class RestoreConfirmForm extends ModalForm
{
    /**
     * RestoreConfirmForm constructor.
     * @param string $world
     * @param int $historyNumber
     * @param string $historyDate
     */
    public function __construct(string $world, int $historyNumber, string $historyDate)
    {
        parent::__construct(Messages::restoreConfirmFormTitle(), Messages::restoreConfirmFormContent($world, $historyNumber, $historyDate));

        $this->onAccept(new class($world, $historyNumber, $historyDate) extends ModalFormAction
        {
            private $world;
            private $historyNumber;
            private $historyDate;

            /**
             *  constructor.
             * @param string $world
             * @param int $historyNumber
             * @param string $historyDate
             */
            public function __construct(string $world, int $historyNumber, string $historyDate)
            {
                $this->world = $world;
                $this->historyNumber = $historyNumber;
                $this->historyDate = $historyDate;
            }

            /**
             * @param Player $player
             * @param ModalForm $form
             */
            public function onAction(Player $player, ModalForm $form): void
            {
                WorldBackupService::getInstance()->restore($this->world, $this->historyNumber);
                $player->sendPopup(Messages::restoreCompleted($this->world, $this->historyNumber));
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