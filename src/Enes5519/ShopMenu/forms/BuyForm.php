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

namespace Enes5519\ShopMenu\forms;

use Enes5519\ShopMenu\lang\Lang;
use Enes5519\ShopMenu\ShopMenu;
use onebone\economyapi\EconomyAPI;
use pocketmine\form\CustomForm;
use pocketmine\form\element\Label;
use pocketmine\form\element\Slider;
use pocketmine\form\Form;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\Config;

class BuyForm extends CustomForm{

    /** @var Item */
    protected $item;
    /** @var int */
    protected $price;
    /** @var Config */
    protected $cfg;
    /** @var Lang */
    protected $lang;
    /** @var string */
    protected $monetaryUnit;

    public function __construct(Player $player, Item $item, int $price){
        $this->item = $item;
        $this->price = $price;
        $this->cfg = ShopMenu::getAPI()->getConfig();
        $this->lang = new Lang($player);
        $this->monetaryUnit = EconomyAPI::getInstance()->getMonetaryUnit();

        parent::__construct($this->lang->translate("title.buy"), [
            new Label(str_pad($this->lang->translate("your.money", [EconomyAPI::getInstance()->myMoney($player), $this->monetaryUnit]), 34, " ", STR_PAD_LEFT)."\n\n\n"),
            new Label($this->lang->translate("items.to.buy", [$item->getName()])."\n".$this->lang->translate("price", [$price, $this->monetaryUnit])."\n\n"),
            new Slider($this->lang->translate("amount"), 1, 64)
        ]);
    }

    public function onSubmit(Player $player) : ?Form{
        $miktar = (int) $this->getElement(2)->getValue();
        $ucret = $this->price * $miktar;
        $item = $this->item->setCount($miktar);

        if($player->getInventory()->canAddItem($item)){
            if(EconomyAPI::getInstance()->myMoney($player) >= $ucret){
                EconomyAPI::getInstance()->reduceMoney($player, $ucret);
                $player->getInventory()->addItem($item);
                $player->sendMessage($this->lang->translate("bought", [$miktar, $item->getName(), $ucret, $this->monetaryUnit]));
            }else{
                $player->sendMessage($this->lang->translate("not.enough.money", [$miktar, $item->getName()]));
            }
        }else{
            $player->sendMessage($this->lang->translate("inventory.full"));
        }

        return null;
    }
}