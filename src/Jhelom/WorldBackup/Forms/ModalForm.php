<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Forms;

use pocketmine\Player;

/**
 * Class ModalForm
 * @package Jhelom\WorldBackup\Forms
 */
class ModalForm extends Form
{
    /** @var ModalFormAction */
    private $acceptAction;

    /** @var ModalFormAction */
    private $dismissAction;

    /**
     * ModalForm constructor.
     * @param string $title
     * @param string $content
     * @param string $acceptText
     * @param string $dismissText
     */
    public function __construct(string $title, string $content = '', $acceptText = 'OK', $dismissText = 'CANCEL')
    {
        parent::__construct('modal', $title);
        $this->formData['content'] = $content;
        $this->formData['button1'] = $acceptText;
        $this->formData['button2'] = $dismissText;
    }

    /**
     * @param ModalFormAction $action
     */
    public function onAccept(ModalFormAction $action): void
    {
        $this->acceptAction = $action;
    }

    /**
     * @param ModalFormAction $action
     */
    public function onDismiss(ModalFormAction $action): void
    {
        $this->dismissAction = $action;
    }

    /**
     * @param Player $player
     * @param mixed|null $result
     */
    protected function onProcess(Player $player, $result): void
    {
        if ($result === true) {
            $this->accept($player);
        } else if ($result === false) {
            $this->dismiss($player);
        }
    }

    /**
     * @param Player $player
     */
    private function accept(Player $player): void
    {
        $action = $this->acceptAction;

        if ($action instanceof ModalFormAction) {
            $action->onAction($player, $this);
        }
    }

    /**
     * @param Player $player
     */
    private function dismiss(Player $player): void
    {
        $action = $this->dismissAction;

        if ($action instanceof ModalFormAction) {
            $action->onAction($player, $this);
        }
    }
}

