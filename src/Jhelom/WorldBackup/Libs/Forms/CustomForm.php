<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs\Forms;


use pocketmine\Player;

/**
 * Class CustomForm
 */
class CustomForm extends Form
{
    private const CONTENT = 'content';

    /** @var ICustomFormSubmitAction */
    private $submitAction;

    /** @var ICustomFormCloseAction */
    private $closeAction;

    private $tagMap = [];

    /**
     * CustomForm constructor.
     * @param string $title
     */
    public function __construct(string $title)
    {
        parent::__construct('custom_form', $title);
        $this->formData[self::CONTENT] = [];
    }

    /**
     * @param string $text
     * @param string $tag
     * @param string $default
     * @param string $placeholder
     */
    public function addInput(string $text, string $tag, string $default = '', string $placeholder = ''): void
    {
        $this->addContent($tag, [
            'type' => 'input',
            'text' => $text,
            'placeholder' => $placeholder,
            'default' => $default,
        ]);
    }

    /**
     * @param null|string $tag
     * @param array $content
     */
    private function addContent(?string $tag, array $content): void
    {
        $this->formData[self::CONTENT][] = $content;
        $this->tagMap[] = $tag;
    }

    /**
     * @param string $text
     */
    public function addLabel(string $text): void
    {
        $this->addContent(null, [
            'type' => 'label',
            'text' => $text
        ]);
    }

    /**
     * @param string $text
     * @param string $tag
     * @param bool $default
     */
    public function addToggle(string $text, string $tag, bool $default = false): void
    {
        $this->addContent($tag, [
            'type' => 'toggle',
            'text' => $text,
            'default' => $default
        ]);
    }/** @noinspection PhpTooManyParametersInspection */

    /**
     * @param string $text
     * @param string $tag
     * @param int $default
     * @param int $min
     * @param int $max
     * @param int $step
     */
    public function addSlider(string $text, string $tag, int $default = 0, int $min = 0, int $max = 10, int $step = 1): void
    {
        $this->addContent($tag, [
            'type' => 'slider',
            'text' => $text,
            'min' => $min,
            'max' => $max,
            'step' => $step,
            'default' => $default
        ]);
    }

    /**
     * @param string $text
     * @param string $tag
     * @param array $steps
     * @param int $defaultIndex
     */
    public function addStepSlider(string $text, string $tag, array $steps, int $defaultIndex = 0): void
    {
        $this->addContent($tag, [
            'type' => 'slider',
            'text' => $text,
            'steps' => $steps,
            'default' => $defaultIndex
        ]);
    }

    /**
     * @param string $text
     * @param string $tag
     * @param array $options
     * @param int $default
     */
    public function addDropdown(string $text, string $tag, array $options, int $default = 0): void
    {
        $this->addContent($tag, [
            'type' => 'dropdown',
            'text' => $text,
            'options' => $options,
            'default' => $default
        ]);
    }

    /**
     * @param ICustomFormCloseAction $action
     */
    public function onClose(ICustomFormCloseAction $action): void
    {
        $this->closeAction = $action;
    }

    /**
     * @param ICustomFormSubmitAction $action
     */
    public function onSubmit(ICustomFormSubmitAction $action): void
    {
        $this->submitAction = $action;
    }

    /**
     * @param Player $player
     * @param $result
     */
    protected function onProcess(Player $player, $result): void
    {
        if (is_null($result)) {
            if (!is_null($this->closeAction)) {
                $this->close($player);
            }
        } else if (is_array($result)) {
            $values = [];

            foreach ($result as $index => $value) {
                $tag = $this->tagMap[$index];

                if (is_string($tag)) {
                    $values[$tag] = $value;
                }
            }

            $this->submit($player, $values);
        }
    }

    /**
     * @param Player $player
     */
    private function close(Player $player): void
    {
        $action = $this->closeAction;

        if ($action instanceof ICustomFormCloseAction) {
            $action->onCustomFormClose($player, $this);
        }
    }

    /**
     * @param Player $player
     * @param array $values
     */
    private function submit(Player $player, array $values): void
    {
        $action = $this->submitAction;

        if ($action instanceof ICustomFormSubmitAction) {
            $action->onCustomFormSubmit($player, $this, new CustomFormValues($values));
        }
    }
}
