<?php

namespace AventCube;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerMoveEvent;
 use pocketmine\event\player\PlayerJoinEvent;
 use pocketmine\event\player\PlayerChatEvent;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;

use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\RemovePlayerPacket;
use pocketmine\scheduler\CallbackTask;
use pocketmine\utils\TextFormat;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\level\Level;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;

use pocketmine\event\player\PlayerQuitEvent;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\entity\Entity;

use pocketmine\Player;
use pocketmine\tile\Sign;
use pocketmine\item\Item;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
class Main extends PluginBase implements Listener {
public function onLoad(){
$this->login = [];

}
	 public function onEnable() {
	
		@mkdir ( $this->getDataFolder () );
	 	$this->getServer ()->getPluginManager ()->registerEvents ( $this, $this );
$this->path = $this->getDataFolder(); 
		if(!is_file($this->path."Chat.yml")){
			file_put_contents($this->path."Chat.yml", $this->readResource("Chat.yml"));
			}
			if(!is_file($this->path."Ranklist.yml")){
			file_put_contents($this->path."Ranklist.yml", $this->readResource("Ranklist.yml"));
			}
			if(!is_file($this->path."Itemlist.yml")){
			file_put_contents($this->path."Itemlist.yml", $this->readResource("Itemlist.yml"));
			}
			
$this->moneyfile = new Config( $this->getDataFolder() . "Money.yml", Config::YAML);
$this->rankshopfile = new Config( $this->getDataFolder() . "Rankshop.yml", Config::YAML);
$this->authfile = new Config( $this->getDataFolder() . "Auth.yml", Config::YAML);
$this->rankfile = new Config( $this->getDataFolder() . "Ranks.yml", Config::YAML);
$this->chatfile = new Config( $this->getDataFolder() . "Chat.yml", Config::YAML);
$this->ranklistfile = new Config( $this->getDataFolder() . "Ranklist.yml", Config::YAML);
$this->shopfile = new Config( $this->getDataFolder() . "Shop.yml", Config::YAML);
$this->sellfile = new Config( $this->getDataFolder() . "Sell.yml", Config::YAML);
$this->itemlistfile = new Config( $this->getDataFolder() . "Itemlist.yml", Config::YAML);
$this->warpsfile = new Config( $this->getDataFolder() . "Warps.yml", Config::YAML);

		$this->money = $this->moneyfile->getAll();
		$this->rankshop = $this->rankshopfile->getAll();
		$this->auth = $this->authfile->getAll();
		$this->ranks = $this->rankfile->getAll();
		$this->chat = $this->chatfile->getAll();
		$this->ranklist = $this->ranklistfile->getAll();
		$this->shop = $this->shopfile->getAll();
		$this->sell = $this->sellfile->getAll();
$this->itemlist = $this->itemlistfile->getAll();
$this->warps = $this->warpsfile->getAll();
}
public function onDisable(){
$this->savee();
}
public function savee(){

$data = [
  "Money" => $this->money,
  "RankShop" => $this->rankshop,
  "Auth" => $this->auth,
  "Ranks" => $this->ranks,
  "Shop" => $this->shop,
  "Sell" => $this->sell,
  "Warps" => $this->warps
];
if(!is_dir($this->getDataFolder())) {
  mkdir($this->getDataFolder());
}
foreach($data as $file => $datum) {
  file_put_contents($this->getDataFolder() . "$file.yml",yaml_emit($datum));
}
}

	
public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {




	switch($cmd){
case "setrank":
if ($sender->isOp()){
$this->ranks["ranks"][$args[0]] = strtolower($args[1]);
$sender->sendMessage("Rank of ".$args[0]." is now ".$args[1]);
$sender->getServer()->getPlayerExact($args[0])->sendMessage("Your rank have been changed");
}

return true;
break;

case "wallet":
$player = $sender->getName();
$money = $this->money["money"][$player];
$sender->sendMessage("[Wallet] §2You have ".$money."$");
return true;
break;

case "setwarp":
	if ($sender->isOp() && isset($args[0]) && !is_numeric($args[0])){
		$x = $sender->getX();
		$y = $sender->getY();
		$z = $sender-> getZ();
		$level = $sender->getLevel()->getName();
		$this->warps["warps"][$args[0]]["x"] = $x;
		$this->warps["warps"][$args[0]]["y"] = $y;
		$this->warps["warps"][$args[0]]["z"] = $z;
		$this->warps["warps"][$args[0]]["level"] = $level;
		$sender->sendMessage("[Warps] Warp ."$args[0]." created.");
	}else{$sender->sendMessage("Usage : /setwarp <name>");}
	return true;
	break;
}
}

 public function onPlayerChat(PlayerChatEvent $event) {
$player = $event->getPlayer()->getName();
if ( $this->login[$player] == 0){
 $this->auth["auth"][$player] = md5($event->getMessage());
$this->login[$player]= 2;
$event->getPlayer()->sendMessage("§8Password set succefully, you have been logged in");
$event->setCancelled(true);
return;
}
if ( $this->login[$player]==1){
if (md5($event->getMessage())== $this->auth["auth"][$player]){
$this->login[$player] = 2;
$event->getPlayer()->sendMessage("§8 You have been logged in");
$event->setCancelled(true);
return;
}else{$event->getPlayer()->sendMessage("§8Wrong password, try again");$event->setCancelled(true);return;}
}

$player = $event->getPlayer()->getName();
	$message = $event->getMessage ();
	$playerank = $this->ranks["ranks"][$player];
$format = $this->chat["chat"][$playerank];
$format = str_replace("{PLAYER}", $player, $format);
$format = str_replace("{MESSAGE}", $message, $format);
				$event->setFormat ("");
			foreach($event->getPlayer()->getServer()->getOnlinePlayers() as $player){
if ($this->login[$player->getName()]==2){
$event->getPlayer()->getServer()->getPlayerExact($player->getName())->sendMessage($format);
}
			
		}
	}

 public function onJoin(PlayerJoinEvent $event){
  $player = $event->getPlayer()->getName();  	


if (!isset($this->ranks["ranks"][$player])){
$this->ranks["ranks"][$player] = "player";
$this->money["money"][$player] = 1000;
 $this->auth["auth"][$player] = null;

}
if ($this->ranks["ranks"][$player]== "admin" or $this->ranks["ranks"][$player] == "modo" or $this->ranks["ranks"][$player]== "builder" ){

	$message = "joined the game !";
	$playerank = $this->ranks["ranks"][$player];
$format = $this->chat["chat"][$playerank];
$format = str_replace("{PLAYER}", $player, $format);
$format = str_replace("{MESSAGE}", $message, $format);
$event->setJoinMessage($format);
}else{ $event->setJoinMessage("");}
$message = "";
$playerank = $this->ranks["ranks"][$player];
$format = $this->chat["chat"][$playerank];
$format = str_replace("{PLAYER}", $player, $format);
$format = str_replace("{MESSAGE}", $message, $format);
$event->getPlayer()->setNameTag($format);
$event->getPlayer()->sendMessage("***********************************\n*                                         *\n*        Welcome in AventCube      *\n*                                         *\n***********************************");
if ($this->auth["auth"][$player] == null){
$this->login[$player] = 0;

$event->getPlayer()->sendMessage("§4You are not registered, please tape you password in the chat.");
}else{
$this->login[$player] = 1;
$event->getPlayer()->sendMessage("§4You are not log in, tape your password on the chat");
}

}

public function SignChange(SignChangeEvent $event) {
		
		if ($event->getLine ( 0 ) == "[RankShop]" && $event->getPlayer()->isOp()){
$event->setLine(0,"§e[RankShop]");
$rank = $event->getLine(1);
$price = $event->getline(2);
$event->setLine(1,$rank);
$event->setLine(2,"Price:".$price."$");
$event->setLine(3,"******");
$this->rankshop["rankshop"][$event->getBlock()->getX().":".$event->getBlock()->getY().":".$event->getBlock()->getZ()] =strtolower($rank).",".$price;

$event->getPlayer()->sendMessage("[RankShop] Succefully created !");
}
	if ($event->getLine ( 0 ) == "[Shop]" && $event->getPlayer()->isOp()){
$event->setLine(0,"§e[Shop]");
$number = $event->getLine(1);
$price = $event->getline(2);
$id = $event->getline(3);
$event->setLine(1,"Quantity: ".$number);
$event->setLine(2,$price."$");
$event->setLine(3,$this->getItemName($id));
$this->shop["shop"][$event->getBlock()->getX().":".$event->getBlock()->getY().":".$event->getBlock()->getZ()] = $number.",".$price.",".$id;
$event->getPlayer()->sendMessage("[Shop] Succefully created !");

}


if ($event->getLine ( 0 ) == "[Sell]" && $event->getPlayer()->isOp()){
$event->setLine(0,"§e[Sell]");
$number = $event->getLine(1);
$price = $event->getline(2);
$id = $event->getline(3);
$event->setLine(1,"Quantity: ".$number);
$event->setLine(2,$price."$");
$event->setLine(3,$this->getItemName($id));
$this->sell["sell"][$event->getBlock()->getX().":".$event->getBlock()->getY().":".$event->getBlock()->getZ()] = $number.",".$price.",".$id;
$event->getPlayer()->sendMessage("[Sell] Succefully created !");
}
}
public function onPlayerTouch(PlayerInteractEvent $event){
		$block = $event->getBlock();
		$loc = $block->getX().":".$block->getY().":".$block->getZ();
$player = $event->getPlayer()->getName();
if ($this->login[$player]<2){
$event->setCancelled(true);
}
if (isset($this->rankshop["rankshop"][$loc])){
$info = explode(",",$this->rankshop["rankshop"][$loc]);
$player = $event->getPlayer()->getName();  
if ( $this->ranklist["ranklist"][$this->ranks["ranks"][$player]]<$this->ranklist["ranklist"][$info[0]]){

if ($this->money["money"][$player]>$info[1]){
$this->money["money"][$player] = $this->money["money"][$player]-$info[1];
$this->ranks["ranks"][$event->getPlayer()->getName()] = $info[0];
 	
$message = "";
$playerank = $this->ranks["ranks"][$player];
$format = $this->chat["chat"][$playerank];
$format = str_replace("{PLAYER}", $player, $format);
$format = str_replace("{MESSAGE}", $message, $format);
$event->getPlayer()->setNameTag($format);
$event->getPlayer()->sendPopup("You have buy the rank ".$info[0]." for ".$info[1]."$");

}else{$event->getPlayer()->sendPopup("§4[RankShop] You don't have enought money !");
}

}else{$event->getPlayer()->sendPopup("§4[RankShop] You can't buy this rank !");}
}

if (isset($this->shop["shop"][$loc])){
$info = explode(",",$this->shop["shop"][$loc]);
$player = $event->getPlayer()->getName();  

if ($this->money["money"][$player]>$info[1]){
$this->money["money"][$player] = $this->money["money"][$player]-$info[1];
$item = explode(":", $info[2]);
$event->getPlayer()->getInventory()->addItem(new Item($item[0], $item[1], $info[0]));

$event->getPlayer()->sendPopup("You have buy ".$info[0]." of ".$this->getItemName($info[2])." for ".$info[1]."$");

}else{$event->getPlayer()->sendPopup("§4[Shop] You don't have enought money !");
}


}

if (isset($this->sell["sell"][$loc])){
$info = explode(",",$this->sell["sell"][$loc]);
$player = $event->getPlayer()->getName();  
$item = explode(":", $info[2]);
$cnt = 0;
			foreach($event->getPlayer()->getInventory()->getContents() as $items){
				if($items->getID() == $item[0] and $items->getDamage() == $item[1]){
					$cnt++;
				}
			}

if ($cnt>0){
$this->money["money"][$player] = $this->money["money"][$player]+$info[1];
$item = explode(":", $info[2]);
$this->removeItem($event->getPlayer(), new Item($item[0], $item[1], $info[0]));

$event->getPlayer()->sendPopup("You have sell ".$info[0]." of ".$this->getItemName($info[2])." for ".$info[1]."$");

}else{$event->getPlayer()->sendPopup("§4[Sell] You don't have enought items !");
}


}



}
public function onBreakEvent(BlockBreakEvent $event){
$block = $event->getBlock();
$player = $event->getPlayer()->getName();
		$loc = $block->getX().":".$block->getY().":".$block->getZ();
if ($this->login[$player]<2)
{
$event->setCancelled(true);
return;
}

if (isset($this->rankshop["rankshop"][$loc])){
if ($event->getPlayer()->isOp()){
unset($this->rankshop["rankshop"][$loc]);
$event->getPlayer()->sendPopup("[RankShop] shop deleted");
}else{$event->setCancelled(true);}
}

if (isset($this->shop["shop"][$loc])){
if ($event->getPlayer()->isOp()){
unset($this->shop["shop"][$loc]);
$event->getPlayer()->sendPopup("[Shop] shop deleted");
}else{$event->setCancelled(true);}
}
if (isset($this->sell["sell"][$loc])){
if ($event->getPlayer()->isOp()){
unset($this->sell["sell"][$loc]);
$event->getPlayer()->sendPopup("[Sell] sell deleted");
}else{$event->setCancelled(true);}
}

}
public function onPlayerMove(PlayerMoveEvent $event){
$player = $event->getPlayer()->getName();
if ($this->login[$player]<2){
$event->setCancelled(true);
}



}

public function removeItem($sender, $getitem){
		$getcount = $getitem->getCount();
		if($getcount <= 0)
			return;
		for($index = 0; $index < $sender->getInventory()->getSize(); $index ++){
			$setitem = $sender->getInventory()->getItem($index);
			if($getitem->getID() == $setitem->getID() and $getitem->getDamage() == $setitem->getDamage()){
				if($getcount >= $setitem->getCount()){
					$getcount -= $setitem->getCount();
					$sender->getInventory()->setItem($index, Item::get(Item::AIR, 0, 1));
				}else if($getcount < $setitem->getCount()){
					$sender->getInventory()->setItem($index, Item::get($getitem->getID(), 0, $setitem->getCount() - $getcount));
					break;
				}
			}
		}
	}
public function getItemName($id){
return $this->itemlist["itemlist"][$id];
}
public function onPlayerCommand(PlayerCommandPreprocessEvent $event){

		if($this->login[$event->getPlayer()->getName()]!=2){

			$message = $event->getMessage();

			if($message{0} === "/"){ //Command

				$event->setCancelled(true);
$event->getPlayer()->sendMessage("You are not loging in !");
}}}
public function onPlayerDropItem(PlayerDropItemEvent $event){
		$player = $event->getPlayer()->getName();
if ($this->login[$player]<2){
$event->setCancelled(true);
}

	}
	
	public function onPlayerQuit(PlayerQuitEvent $event){
		$event->setQuitMessage("");
	}
	
	
	public function onPlayerItemConsume(PlayerItemConsumeEvent $event){
		$player = $event->getPlayer()->getName();
if ($this->login[$player]<2){
$event->setCancelled(true);
}
}
	public function onEntityDamage(EntityDamageEvent $event){
	$player = $event->getEntity()->getName();
		if($event->getEntity() instanceof Player and $this->login[$player]<2){
			$event->setCancelled(true);
		}
	}
	
	public function onBlockPlace(BlockPlaceEvent $event){
		$player = $event->getPlayer()->getName();
if ($this->login[$player]<2){
$event->setCancelled(true);
}
	}
	
	
	


	private function readResource($res){
		$resource = $this->getResource($res);
		if($resource !== null){
			return stream_get_contents($resource);
		}
		return false;
	}
	
	
	
	
}



?>
