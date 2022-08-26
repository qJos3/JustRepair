<?php

namespace Jos3;

use Jos3\Commands\FixCommand;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class JustRepair extends PluginBase {

    use SingletonTrait;

    protected function onLoad(): void{
        self::setInstance($this);
    }

    protected function onEnable(): void{
        $permissions = [
            "fix-all.command.use",
            "fix-hand.command.use",
            "fix-all-others.command.use",
        ];
        $manager = PermissionManager::getInstance();
        foreach ($permissions as $permission){
            $manager->addPermission(new Permission($permission));
            $manager->getPermission(DefaultPermissions::ROOT_OPERATOR)->addChild($permission, true);
        }
        $this->saveResource("messages.yml");
        $this->getServer()->getCommandMap()->register("repair",new FixCommand());
    }

    protected function onDisable(): void{
        $file = new Config($this->getServer()->getDataPath()."messages.yml",Config::YAML);
        $file->save();
    }

    public static function getConfiguration($configuration): Config{
        return new Config(self::getInstance()->getDataFolder().$configuration, Config::YAML);
    }
}