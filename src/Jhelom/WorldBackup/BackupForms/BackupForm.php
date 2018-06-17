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
 * Class BackupForm
 * @package Jhelom\WorldBackup\BackupForms
 */
class BackupForm extends SimpleForm
{
    public function __construct()
    {
        parent::__construct(Messages::backupFormTitle(), Messages::backupFormContent());

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
        $worlds = $service->getSourceWorlds();

        foreach ($worlds as $world) {
            $this->addButton($world)->onClick(new class($world) extends ButtonClickAction
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
                 * @param SimpleForm $form
                 * @param ButtonElement $button
                 */
                public function onAction(Player $player, SimpleForm $form, ButtonElement $button): void
                {
                    (new BackupConfirmForm($this->world))->sendToPlayer($player);
                }
            });
        }
    }
}
