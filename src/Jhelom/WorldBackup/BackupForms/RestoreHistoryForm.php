<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\BackupForms;


use Jhelom\Core\Forms\ButtonClickAction;
use Jhelom\Core\Forms\ButtonElement;
use Jhelom\Core\Forms\SimpleForm;
use Jhelom\Core\Forms\SimpleFormCloseAction;
use Jhelom\WorldBackup\Messages;
use Jhelom\WorldBackup\Services\WorldBackupService;
use pocketmine\Player;

/**
 * Class RestoreHistoryForm
 * @package Jhelom\WorldBackup\BackupForms
 */
class RestoreHistoryForm extends SimpleForm
{
    /**
     * RestoreHistoryForm constructor.
     * @param string $world
     * @throws \Jhelom\Core\ServiceException
     */
    public function __construct(string $world)
    {
        parent::__construct(Messages::restoreHistoryFormTitle(), Messages::restoreHistoryFormContent($world));

        $this->onClose(new class extends SimpleFormCloseAction
        {
            /**
             * @param Player $player
             * @param SimpleForm $form
             */
            public function onAction(Player $player, SimpleForm $form): void
            {
                TopForm::send($player);
            }
        });

        $service = WorldBackupService::getInstance();
        /** @noinspection PhpUnhandledExceptionInspection */
        $histories = $service->getHistories($world);

        foreach ($histories as $index => $historyDate) {
            $historyNumber = $index + 1;
            $text = $historyDate;
            $this->addButton($text)->onClick(new class($world, $historyNumber, $historyDate) extends ButtonClickAction
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
                 * @param SimpleForm $form
                 * @param ButtonElement $button
                 */
                public function onAction(Player $player, SimpleForm $form, ButtonElement $button): void
                {
                    (new RestoreConfirmForm($this->world, $this->historyNumber, $this->historyDate))->sendToPlayer($player);
                }
            });
        }
    }
}