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
use pocketmine\form\Form;
use pocketmine\form\MenuForm;
use pocketmine\item\ItemFactory;
use pocketmine\Player;

class ItemsForm extends MenuForm{

    /** @var string */
    protected $index;

    public function __construct(string $title, array $options, string $index){
        $this->index = $index;
        parent::__construct($title, "", $options);
    }

    public function onSubmit(Player $player) : ?Form{
        $index = $this->getSelectedOptionIndex();
        $decode = ShopMenu::getAPI()->getConfig()->get("items");
        $decode = $decode[$this->index];
        $decode = explode(":", $decode[$index]);

        $item = ItemFactory::get((int) $decode[0], (int) $decode[1]);

        return new BuyForm($player, $item, intval($decode[3]));
    }
}