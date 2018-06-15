<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\BackupForms;

use Jhelom\WorldBackup\Forms\CustomForm;
use Jhelom\WorldBackup\Forms\CustomFormCloseAction;
use Jhelom\WorldBackup\Forms\CustomFormSubmitAction;
use Jhelom\WorldBackup\Forms\CustomFormValues;
use Jhelom\WorldBackup\Messages;
use pocketmine\Player;

/**
 * Class SettingsForm
 * @package Jhelom\WorldBackup\BackupForms
 */
class SettingsForm extends CustomForm
{
    public function __construct()
    {
        parent::__construct(Messages::settingsFormTitle());
        $this->onClose(new class extends CustomFormCloseAction
        {
            /**
             * @param Player $player
             * @param CustomForm $form
             */
            public function onAction(Player $player, CustomForm $form): void
            {
                TopForm::send($player);
            }
        });

        $this->onSubmit(new class extends CustomFormSubmitAction
        {

            /**
             * @param Player $player
             * @param CustomForm $form
             * @param CustomFormValues $values
             */
            public function onAction(Player $player, CustomForm $form, CustomFormValues $values): void
            {
                TopForm::send($player);
            }
        });
    }
}