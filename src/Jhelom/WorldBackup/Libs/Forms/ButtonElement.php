<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs\Forms;


use pocketmine\Player;

/**
 * Class ButtonElement
 */
class ButtonElement
{
    /** @var IButtonClickAction */
    private $clickAction;

    /** @var string */
    private $text;

    /** @var mixed|null */
    private $value;

    /**
     * ButtonElement constructor.
     * @param string $text
     * @param mixed|null $value
     */
    public function __construct(string $text, $value = null)
    {
        $this->text = $text;
        $this->value = $value;
    }

    /**
     * @param SimpleForm $form
     * @param Player $player
     */
    public function click(SimpleForm $form, Player $player): void
    {
        $action = $this->clickAction;

        if ($action instanceof IButtonClickAction) {
            $action->onButtonClick($player, $form, $this);
        }
    }

    /**
     * @param IButtonClickAction $action
     * @return ButtonElement
     */
    public function onClick(IButtonClickAction $action): ButtonElement
    {
        $this->clickAction = $action;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return null|string
     */
    public function getValueAsString(): ?string
    {
        $value = $this->getValue();

        if (is_string($value)) {
            return $value;
        } else {
            return null;
        }
    }

    /**
     * @return mixed|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return int|null
     */
    public function getValueAsInt(): ?int
    {
        $value = $this->getValue();

        if (is_int($value)) {
            return $value;
        } else {
            return null;
        }
    }

    /**
     * @return bool|null
     */
    public function getValueAsBool(): ?bool
    {
        $value = $this->getValue();

        if (is_bool($value)) {
            return $value;
        } else {
            return null;
        }
    }
}
