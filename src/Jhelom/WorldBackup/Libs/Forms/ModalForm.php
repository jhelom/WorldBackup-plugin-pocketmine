<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs\Forms;

use pocketmine\Player;

/**
 * Class ModalForm
 */
class ModalForm extends Form
{
    /** @var IModalFormCloseAction */
    private $acceptAction;

    /** @var IModalFormCloseAction */
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
     * @param IModalFormCloseAction $action
     */
    public function onAccept(IModalFormCloseAction $action): void
    {
        $this->acceptAction = $action;
    }

    /**
     * @param IModalFormCloseAction $action
     */
    public function onDismiss(IModalFormCloseAction $action): void
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

        if ($action instanceof IModalFormCloseAction) {
            $action->onModalFormClose($player, $this, true);
        }
    }

    /**
     * @param Player $player
     */
    private function dismiss(Player $player): void
    {
        $action = $this->dismissAction;

        if ($action instanceof IModalFormCloseAction) {
            $action->onModalFormClose($player, $this, false);
        }
    }
}

