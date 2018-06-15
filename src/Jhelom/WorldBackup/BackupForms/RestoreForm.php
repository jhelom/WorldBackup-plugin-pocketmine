<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\BackupForms;

use Jhelom\WorldBackup\Forms\ButtonClickAction;
use Jhelom\WorldBackup\Forms\ButtonElement;
use Jhelom\WorldBackup\Forms\SimpleForm;
use Jhelom\WorldBackup\Forms\SimpleFormCloseAction;
use Jhelom\WorldBackup\Messages;
use Jhelom\WorldBackup\Services\WorldBackupService;
use pocketmine\Player;

/**
 * Class RestoreForm
 * @package Jhelom\WorldBackup\BackupForms
 */
class RestoreForm extends SimpleForm
{
    public function __construct()
    {
        parent::__construct(Messages::restoreFormTitle(), Messages::restoreFormContent());

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
        $worlds = $service->getBackupWorlds();

        foreach ($worlds as $world) {
            $this->addButton($world, $world)->onClick(new class extends ButtonClickAction
            {
                /**
                 * @param Player $player
                 * @param SimpleForm $form
                 * @param ButtonElement $button
                 */
                public function onAction(Player $player, SimpleForm $form, ButtonElement $button): void
                {
                    (new RestoreHistoryForm($button->getValueAsString()))->sendToPlayer($player);
                }
            });
        }
    }
}