<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\BackupForms;


use Jhelom\Core\Forms\ButtonClickAction;
use Jhelom\Core\Forms\ButtonElement;
use Jhelom\Core\Forms\SimpleForm;
use Jhelom\WorldBackup\Messages;
use pocketmine\Player;

/**
 * Class TopForm
 * @package Jhelom\WorldBackup\BackupForms
 */
class TopForm extends SimpleForm
{
    public function __construct()
    {
        parent::__construct(Messages::topFormTitle());

        $this->addButton(Messages::topFormBackupButton())->onClick(new class extends ButtonClickAction
        {
            /**
             * @param Player $player
             * @param SimpleForm $form
             * @param ButtonElement $button
             */
            public function onAction(Player $player, SimpleForm $form, ButtonElement $button): void
            {
                (new BackupForm())->sendToPlayer($player);
            }
        });

        $this->addButton(Messages::topFormRestoreButton())->onClick(new class extends ButtonClickAction
        {
            /**
             * @param Player $player
             * @param SimpleForm $form
             * @param ButtonElement $button
             */
            public function onAction(Player $player, SimpleForm $form, ButtonElement $button): void
            {
                (new RestoreForm())->sendToPlayer($player);
            }
        });

        $this->addButton(Messages::topFormSettingsButton())->onClick(new class extends ButtonClickAction
        {
            /**
             * @param Player $player
             * @param SimpleForm $form
             * @param ButtonElement $button
             */
            public function onAction(Player $player, SimpleForm $form, ButtonElement $button): void
            {
                (new SettingsForm())->sendToPlayer($player);
            }
        });

        $this->addButton(Messages::topFormQuitButton());
    }

    /**
     * @param Player $player
     */
    static public function send(Player $player)
    {
        $form = new TopForm();
        $form->sendToPlayer($player);
    }
}
