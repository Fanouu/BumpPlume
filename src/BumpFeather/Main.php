<?php

namespace BumpFeather;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;

class Main extends PluginBase implements Listener{

    private static $boost;
    private static $bumpId;
    private static $coolds;
    private static $coold;
    private static $messages;

    public function onEnable(){
        @mkdir($this->getDataFolder());
        $this->saveResource("settings.yml");
        $settings = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
        self::$boost = $settings->get("BumpFeatherBoost");
        self::$bumpId = $settings->get("BumpFeather");
        self::$coolds = $settings->get("BumpFeatherCooldown");
        self::$messages = $settings->get("BumpFeatherMessage");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("BumpFeather - By Fan");
    }

    public function onInteract(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $pname = $player->getName();
        $Ids = $event->getItem()->getId();
        $meta = $event->getItem()->getDamage();
        $id = "$Ids:$meta";
        if($id === self::$bumpId){
            if(!isset(self::$coold[$pname]) || self::$coold[$pname] - time() <= 0){
                self::$coold[$pname] = time() + self::$coolds;
                $direction = $player->getDirectionVector();
                $player->setMotion(new Vector3($direction->getX(), $direction->getY() + (int)self::$boost, $direction->getZ()));
            }else{
                $timer = intval(self::$coold[$player->getName()] - time());
                $minutes = intval(abs($timer / 60));
                $secondes = intval(abs($timer  - $minutes * 60));
                if($minutes > 0){
                    $TempRestant = "{$minutes} minute(s)";
                } else {
                    $TempRestant = "{$secondes} second(s)";
                }
                $player->sendPopup(str_replace("{time}", $TempRestant, self::$messages));
            }
        }
    }
}