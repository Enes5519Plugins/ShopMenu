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
use pocketmine\form\Form;
use pocketmine\form\FormIcon;
use pocketmine\form\MenuForm;
use pocketmine\form\MenuOption;
use pocketmine\Player;
use pocketmine\utils\Config;

class ShopForm extends MenuForm{

    /** @var Config */
    protected $cfg;
    /** @var Lang */
    protected $lang;

    public function __construct(Player $player){
        $this->cfg = ShopMenu::getAPI()->getConfig();
        $this->lang = new Lang($player);

        $options = [];
        foreach($this->cfg->get("categories", []) as $name => $data){
            $options[] = new MenuOption(isset($data["text"]) ? $data["text"] : $name, ShopMenu::createIconFromConfigData($data));
        }

        parent::__construct(
            $this->lang->translate("title.categories"),
            "",
            $options
        );
    }

    public function onSubmit(Player $player) : ?Form{
        $index = $this->getSelectedOptionIndex();
        $categories = $this->cfg->get("categories", []);
        $index = array_keys($categories)[$index];

        $items = $this->cfg->get("items");
        if(isset($items[$index]) && is_array($items[$index])){
            $options = [];
            foreach($items[$index] as $item){
                $decode = explode(":", $item);

                $imgPath = "";
                foreach($decode as $i => $value)
                    if($i >= 5) $imgPath .= $value;

                $options[] = new MenuOption($this->lang->translate("title.item", [$decode[2], $decode[3], EconomyAPI::getInstance()->getMonetaryUnit()]), new FormIcon($imgPath, $decode[4]));
            }

            return new ItemsForm($this->getSelectedOption()->getText(), $options, $index);
        }

        return null;
    }
}