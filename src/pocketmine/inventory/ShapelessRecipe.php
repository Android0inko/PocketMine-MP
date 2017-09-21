<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\inventory;

use pocketmine\item\Item;
use pocketmine\utils\UUID;

class ShapelessRecipe implements CraftingRecipe{
	/** @var Item */
	private $output;

	/** @var UUID|null */
	private $id = null;

	/** @var Item[] */
	private $ingredients = [];

	public function __construct(Item $result){
		$this->output = clone $result;
	}

	/**
	 * @return UUID|null
	 */
	public function getId() : ?UUID{
		return $this->id;
	}

	/**
	 * @param UUID $id
	 */
	public function setId(UUID $id){
		if($this->id !== null){
			throw new \InvalidStateException("Id is already set");
		}

		$this->id = $id;
	}

	public function getResult() : Item{
		return clone $this->output;
	}

	public function getExtraResults() : array{
		return []; //TODO
	}

	public function getAllResults() : array{
		return [$this->getResult()]; //TODO
	}

	/**
	 * @param Item $item
	 *
	 * @return ShapelessRecipe
	 *
	 * @throws \InvalidArgumentException
	 */
	public function addIngredient(Item $item) : ShapelessRecipe{
		if(count($this->ingredients) >= 9){
			throw new \InvalidArgumentException("Shapeless recipes cannot have more than 9 ingredients");
		}

		$it = clone $item;
		$it->setCount(1);

		while($item->getCount() > 0){
			$this->ingredients[] = clone $it;
			$item->setCount($item->getCount() - 1);
		}

		return $this;
	}

	/**
	 * @param Item $item
	 *
	 * @return $this
	 */
	public function removeIngredient(Item $item){
		foreach($this->ingredients as $index => $ingredient){
			if($item->getCount() <= 0){
				break;
			}
			if($ingredient->equals($item, !$item->hasAnyDamageValue(), $item->hasCompoundTag())){
				unset($this->ingredients[$index]);
				$item->setCount($item->getCount() - 1);
			}
		}

		return $this;
	}

	/**
	 * @return Item[]
	 */
	public function getIngredientList() : array{
		$ingredients = [];
		foreach($this->ingredients as $ingredient){
			$ingredients[] = clone $ingredient;
		}

		return $ingredients;
	}

	/**
	 * @return int
	 */
	public function getIngredientCount() : int{
		$count = 0;
		foreach($this->ingredients as $ingredient){
			$count += $ingredient->getCount();
		}

		return $count;
	}

	public function registerToCraftingManager(CraftingManager $manager) : void{
		$manager->registerShapelessRecipe($this);
	}

	public function requiresCraftingTable() : bool{
		return count($this->ingredients) > 4;
	}
}