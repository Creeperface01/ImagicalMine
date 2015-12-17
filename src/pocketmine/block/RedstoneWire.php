<?php

/*
 *
 *  _                       _           _ __  __ _             
 * (_)                     (_)         | |  \/  (_)            
 *  _ _ __ ___   __ _  __ _ _  ___ __ _| | \  / |_ _ __   ___  
 * | | '_ ` _ \ / _` |/ _` | |/ __/ _` | | |\/| | | '_ \ / _ \ 
 * | | | | | | | (_| | (_| | | (_| (_| | | |  | | | | | |  __/ 
 * |_|_| |_| |_|\__,_|\__, |_|\___\__,_|_|_|  |_|_|_| |_|\___| 
 *                     __/ |                                   
 *                    |___/                                                                     
 * 
 * This program is a third party build by ImagicalMine.
 * 
 * PocketMine is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author ImagicalMine Team
 * @link http://forums.imagicalcorp.ml/
 * 
 *
*/

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\Player;

class RedstoneWire extends Flowable implements Redstone{
	protected $id = self::REDSTONE_WIRE;
	//protected $power = 0;
	protected $PowerSource = array();
	protected $DirectPowerSource = array();
	
	//print_r($this->getPowerSource());
	//$DirectPowerSource = $this->getDirectPowerSource();
	
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getPower(){
		return $this->meta;
	}
	
	public function setPower($power){
		$this->meta = $power;
	}
	
	public function getHardness(){
		return 0;
	}

	public function isSolid(){
		return true;
	}
	
	public function canBeActivated(){
		return true;
	}
	
	public function onActivate(Item $item, Player $player = null){
		print_r($this->getPowerSource());
	}
	
	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$down = $this->getSide(0);
		if($down instanceof Transparent && $down->getId() !== Block::GLOWSTONE_BLOCK) return false;
		else{
			$this->getLevel()->setBlock($block, $this, true, true);
			$this->BroadcastRedstoneUpdate($PowerSource = $this->getblockHash(),$Power = 0);
			return true;
		}
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			$down = $this->getSide(0);
			if($down instanceof Transparent){
				$this->getLevel()->useBreakOn($this);
				return Level::BLOCK_UPDATE_NORMAL;
			}
		}
		return true;
	}

	public function onRedstoneUpdate($PowerSource = null,$Power = 0){
			$fetchedPower = $this->fetchPower() - 1;
			if($fetchedPower == $this->getPower())
				return true;
			if($fetchedPower<0)
				$this->setPower(0);
			else
				$this->setPower($fetchedPower);
			$this->getLevel()->setBlock($this, $this, true, true);
	}
	
	public function getName(){
		return "Redstone Wire";
	}

	public function getDrops(Item $item){
		return [[Item::REDSTONE_DUST,0,1]];
	}

	public function onBreak(Item $item){
		$this->BroadcastRedstoneUpdate($PowerSource = $this->getblockHash(),$Power = -1);
		return $this->getLevel()->setBlock($this, new Air(), true, true);
	}

	public function __toString(){
		return $this->getName() . (isPowered()?"":"NOT ") . "POWERED";
	}
}
