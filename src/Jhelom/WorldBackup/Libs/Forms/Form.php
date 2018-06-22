<?php
declare(strict_types=1);

namespace Jhelom\WorldBackup\Libs\Forms;


use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\Player;

/**
 * Class Form
 */
abstract class Form
{
    /** @var array */
    static $forms = [];

    /** @var array */
    protected $formData = [];

    /** @var int */
    private $id = 0;

    /**
     * Form constructor.
     * @param string $type
     * @param string $title
     */
    public function __construct(string $type, string $title)
    {
        $this->formData['type'] = $type;
        $this->formData['title'] = $title;
    }

    /**
     * @param DataPacketReceiveEvent $event
     */
    static public function process(DataPacketReceiveEvent $event): void
    {
        $pk = $event->getPacket();

        if ($pk instanceof ModalFormResponsePacket) {
            $formId = $pk->formId;
            $player = $event->getPlayer();
            $playerName = $player->getLowerCaseName();
            $form = self::getForm($playerName, $formId);

            //self::purge($playerName);
            unset(self::$forms[$playerName][$formId]);

            if (!is_null($form)) {
                $result = json_decode($pk->formData, true);
                $form->onProcess($player, $result);
            }
        }
    }

    /**
     * @param string $playerName
     * @param int $id
     * @return Form|null
     */
    static private function getForm(string $playerName, int $id): ?Form
    {
        if (array_key_exists($playerName, self::$forms)) {
            if (array_key_exists($id, self::$forms[$playerName])) {
                return self::$forms[$playerName][$id];
            }
        }

        return null;
    }

    /**
     * @param Player $player
     * @param mixed|null $result
     */
    abstract protected function onProcess(Player $player, $result): void;

    /**
     * @param string $playerName
     */
    static public function purge(string $playerName): void
    {
        unset(self::$forms[$playerName]);
    }

    /**
     * @param Player $player
     */
    public function sendToPlayer(Player $player): void
    {
        $playerName = $player->getLowerCaseName();

        if (!array_key_exists($playerName, self::$forms)) {
            self::$forms[$playerName] = [];
        }

        $this->id = mt_rand(0, 30000);
        self::$forms[$playerName][$this->getId()] = $this;

        $pk = new ModalFormRequestPacket();
        $pk->formId = $this->getId();
        $pk->formData = json_encode($this->formData, JSON_UNESCAPED_UNICODE);
        $player->dataPacket($pk);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}





