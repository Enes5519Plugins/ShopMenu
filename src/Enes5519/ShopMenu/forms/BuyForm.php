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

    public function __construct(Player $player, string $title, Item $item, int $price){
        $this->item = $item;
        $this->price = $price;
        $this->cfg = ShopMenu::getAPI()->getConfig();

        parent::__construct($title, [
            new Label(str_replace(["{money}", "{monetary_unit}"], [EconomyAPI::getInstance()->myMoney($player), EconomyAPI::getInstance()->getMonetaryUnit()], $this->cfg->getNested("messages.money"))."\n\n\n"),
            new Label(str_replace("{item}", $title, $this->cfg->getNested("messages.items-to-buy"))),
            new Label(str_replace(["{price}", "{monetary_unit}"], [$this->price, EconomyAPI::getInstance()->getMonetaryUnit()], $this->cfg->getNested("messages.price"))."\n\n\n"),
            new Slider($this->cfg->getNested("messages.amount"), 1, 64)
        ]);
    }

    public function onSubmit(Player $player) : ?Form{
        $miktar = (int) $this->getElement(3)->getValue();
        $ucret = $this->price * $miktar;
        $item = $this->item->setCount($miktar);

        if($player->getInventory()->canAddItem($item)){
            if(EconomyAPI::getInstance()->myMoney($player) >= $ucret){
                EconomyAPI::getInstance()->reduceMoney($player, $ucret);
                $player->getInventory()->addItem($item);
                $player->sendMessage(str_replace(["{amount}", "{item}", "{price}", "{monetary_unit}"], [$miktar, $item->getName(), $this->price, EconomyAPI::getInstance()->getMonetaryUnit()], $this->cfg->getNested("messages.bought")));
            }else{
                $player->sendMessage(str_replace(["{amount}", "{item}", "{price}", "{monetary_unit}"], [$miktar, $item->getName(), $this->price, EconomyAPI::getInstance()->getMonetaryUnit()], $this->cfg->getNested("messages.not-enough-money")));
            }
        }else{
            $player->sendMessage($this->cfg->getNested("messages.inventory-full"));
        }

        return null;
    }
}