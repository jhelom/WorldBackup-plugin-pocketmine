<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs\Forms;


use pocketmine\Player;

/**
 * Class SimpleForm
 */
class SimpleForm extends Form
{
    private const CONTENT = 'content';
    private const BUTTONS = 'buttons';


    /** @var ISimpleFormCloseAction */
    private $closeAction;

    /** @var bool */
    private $clicked = false;

    /** @var array */
    private $buttonElements = [];

    /**
     * SimpleForm constructor.
     * @param string $title
     * @param string $content
     */
    public function __construct(string $title, string $content = '')
    {
        parent::__construct('form', $title);
        $this->formData[self::CONTENT] = $content;
        $this->formData[self::BUTTONS] = [];
    }

    /**
     * @param string $text
     * @param null $value
     * @param null|string $image
     * @return ButtonElement
     */
    public function addButton(string $text, $value = null, ?string $image = null): ButtonElement
    {
        $this->formData[self::BUTTONS][] = [
            'type' => 'button',
            'text' => $text,
        ];

        if (!is_null($image)) {
            $type = strpos($image, 'http') === 0 ? 'url' : 'path';
            $attributes['image'] = [
                'type' => $type,
                'data' => $image
            ];
        }

        $button = new ButtonElement($text, $value);
        $this->buttonElements[] = $button;
        return $button;
    }

    /**
     * @param ISimpleFormCloseAction $action
     * @return SimpleForm
     */
    public function onClose(ISimpleFormCloseAction $action): SimpleForm
    {
        $this->closeAction = $action;
        return $this;
    }

    /**
     * @param Player $player
     * @param mixed|null $result
     */
    protected function onProcess(Player $player, $result): void
    {
        if (is_null($result)) {
            if ($this->clicked) {
                $this->clicked = false;
                return;
            }

            $this->close($player);
        } else {
            if (is_numeric($result)) {
                $index = intval($result);
                $button = $this->buttonElements[$index];

                if ($button instanceof ButtonElement) {
                    $button->click($this, $player);
                }

                $this->clicked = true;
            }
        }
    }

    /**
     * @param Player $player
     */
    private function close(Player $player): void
    {
        if ($this->clicked) {
            $this->clicked = false;
            return;
        }

        $action = $this->closeAction;

        if ($action instanceof ISimpleFormCloseAction) {
            $action->onAction($player, $this);
        }
    }
}
