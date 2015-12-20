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
use pocketmine\math\Vector3;

class LitRedstoneTorch extends Flowable implements Redstone{

	protected $id = self::LIT_REDSTONE_TORCH;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getLightLevel(){
		return 7;
	}

	public function getName(){
		return "Redstone Torch";
	}
	
	public function getPower(){
		return 15;
	}

	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			$below = $this->getSide(0);
			$side = $this->getDamage();
			$faces = [
					1 => 4,
					2 => 5,
					3 => 2,
					4 => 3,
					5 => 0,
					6 => 0,
					0 => 0
					];
			
			if($this->getSide($faces[$side])->isTransparent() === true){
				$this->getLevel()->useBreakOn($this);
				
				return Level::BLOCK_UPDATE_NORMAL;
			}
			
			if($this->getSide(0,2)->getId() === Block::LIT_REDSTONE_TORCH){
				$this->getLevel()->setBlock($this, Block::UNLIT_REDSTONE_TORCH, true, true);
				$this->BroadcastRedstoneUpdate($this,-1);
				
				return Level::BLOCK_UPDATE_NORMAL;
			}
		}
		
		return false;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		$below = $this->getSide(0);

		if($target->isTransparent() === false and $face !== 0){
			$faces = [
				1 => 5,
				2 => 4,
				3 => 3,
				4 => 2,
				5 => 1,
			];
			$this->meta = $faces[$face];
			$this->getLevel()->setBlock($block, $this, true, true);

			return true;
		}elseif($below->isTransparent() === false){
			$this->meta = 0;
			$this->getLevel()->setBlock($block, $this, true, true);
			$this->BroadcastRedstoneUpdate($this,15);
			$this->BroadcastRedstoneUpdate($this->getSide(1,2),15);//power above
			return true;
		}

		return false;
	}
	
	public function onBreak(Item $item){
		$this->BroadcastRedstoneUpdate($this, -1);
			$this->BroadcastRedstoneUpdate($this->getSide(1,2),-1);//unpower above
		return $this->getLevel()->setBlock($this, new Air(), true, true);
	}
	
	public function getDrops(Item $item){
		return [
			[$this->id, 0, 1],
		];
	}
}