<?php

/*
 *    ______                 _____ _____ __  ___
 *   |  ____|               | ____| ____/_ |/ _ \
 *   | |__   _ __   ___  ___| |__ | |__  | | (_) |
 *   |  __| | '_ \ / _ \/ __|___ \|___ \ | |\__, |
 *   | |____| | | |  __/\__ \___) |___) || |  / /
 *   |______|_| |_|\___||___/____/|____/ |_| /_/
 *
 *
 * @author Enes5519
 * @link https://github.com/Enes5519
 *
 */

declare(strict_types=1);

namespace Enes5519\ShopMenu;

use Enes5519\ShopMenu\forms\ShopForm;
use Enes5519\ShopMenu\lang\Lang;
use onebone\economyapi\EconomyAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\form\FormIcon;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class ShopMenu extends PluginBase{

    /** @var ShopMenu */
    private static $api;
    /** @var EconomyAPI */
    private $economyAPI;

    public function onLoad(){
        self::$api = $this;
    }

    public static function getAPI() : ShopMenu{
        return self::$api;
    }

    public function onEnable(){
        if(!class_exists('onebone\economyapi\EconomyAPI')){
            $this->getLogger()->error("EconomyAPI is required to use this plugin.");
            $this->setEnabled(false);
            return;
        }

        $this->economyAPI = EconomyAPI::getInstance();

        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        Lang::init();
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(!($sender instanceof Player) or !($command->testPermission($sender))){
            return true;
        }

        $sender->sendForm(new ShopForm($sender));

        return true;
    }

    public function getEconomyAPI() : EconomyAPI{
        return $this->economyAPI;
    }

    public static function createIconFromConfigData(array $data) : ?FormIcon{
        $check = [FormIcon::IMAGE_TYPE_URL, FormIcon::IMAGE_TYPE_PATH];
        foreach($check as $c)
            if(isset($data["img-".$c]))
                return new FormIcon($data["img-".$c], $c);

        return null;
    }

}