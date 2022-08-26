<?php
namespace Jos3\Commands;



use Jos3\JustRepair;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Tool;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;


class FixCommand extends Command {
    public function __construct()
    {
        parent::__construct("fix","Fix your inventory or the item what you are holding.",null,["repair"]);
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player){
            $file = JustRepair::getConfiguration("messages.yml");
            if ($sender->hasPermission("fix-all.command.use") or $sender->hasPermission("fix-all-others.command.use") or $sender->hasPermission("fix-all-others.command.use")){
                $hold = $sender->getInventory()->getItemInHand();
                if (empty($args)){
                    $msg = TextFormat::colorize($file->get("fix-usage"));
                    $sender->sendMessage($msg);
                    return;
                }
                switch ($args[0]){
                    case "hand":
                        if ($sender->hasPermission("fix-hand.command.use")){
                            if ($hold instanceof Armor or $hold instanceof Tool){
                                if($hold->getDamage() > 0){
                                    $sender->getInventory()->setItemInHand($hold->setDamage(0));
                                    $msg = TextFormat::colorize($file->get("fix-hand-success"));
                                    $sender->sendMessage($msg);
                                    return;
                                }
                                $msg = TextFormat::colorize($file->get("fix-max-error"));
                                $sender->sendMessage($msg);
                                return;
                            }
                            $msg = TextFormat::colorize($file->get("fix-error"));
                            $sender->sendMessage($msg);
                            return;
                        }
                        $msg = TextFormat::colorize($file->get("non-permission"));
                        break;
                    case "all":
                        if (empty($args[1])){
                            if ($sender->hasPermission("fix-all.command.use")) {
                                foreach ($sender->getInventory()->getContents() as $index => $item) {
                                    if ($item instanceof Armor or $item instanceof Tool) {
                                        if ($item->getDamage() > 0) {
                                            $sender->getInventory()->setItem($index, $item->setDamage(0));
                                        }
                                    }
                                }
                                foreach ($sender->getArmorInventory()->getContents() as $index => $item) {
                                    if ($item instanceof Armor) {
                                        if ($item->getDamage() > 0) {
                                            $sender->getArmorInventory()->setItem($index, $item->setDamage(0));
                                        }
                                    }
                                }
                                $msg = TextFormat::colorize($file->get("fix-all-success"));
                                $sender->sendMessage($msg);
                                return;
                            }
                            $msg = TextFormat::colorize($file->get("non-permission"));
                            $sender->sendMessage($msg);
                            return;
                        }
                        if ($sender->hasPermission("fix-all-others.command.use")){
                            $player = JustRepair::getInstance()->getServer()->getPlayerByPrefix($args[1]);
                            if (is_null($player)){

                                $msg = str_replace("{player}",$args[1],$file->get("no-player-found"));
                                $msg = TextFormat::colorize($msg);
                                $sender->sendMessage($msg);
                                return;
                            }
                            foreach ($player->getInventory()->getContents() as $index => $item){
                                if ($item instanceof Armor or $item instanceof Tool){
                                    if($item->getDamage() > 0){
                                        $player->getInventory()->setItem($index,$item->setDamage(0));
                                    }
                                }
                            }
                            foreach ($sender->getArmorInventory()->getContents() as $index => $item) {
                                if ($item instanceof Armor) {
                                    if ($item->getDamage() > 0) {
                                        $sender->getArmorInventory()->setItem($index, $item->setDamage(0));
                                    }
                                }
                            }
                            $msg = TextFormat::colorize($file->get("fix-all-others-success"));
                            $msg = str_replace("{player}",$player->getName(),$msg);
                            $sender->sendMessage($msg);
                            $msg2 = TextFormat::colorize($file->get("fix-all-others-receiver"));
                            $msg2 = str_replace("{sender}",$sender->getName(),$msg2);
                            $player->sendMessage($msg2);
                            return;
                        }
                        $msg = TextFormat::colorize($file->get("non-permission"));
                        break;
                    default:
                        $msg = TextFormat::colorize($file->get("fix-usage"));
                        break;
                }
                $sender->sendMessage($msg);
                return;
            }
            $msg = TextFormat::colorize($file->get("non-permission"));
            $sender->sendMessage($msg);
        }
    }
}