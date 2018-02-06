<?php

/**
 * CombatLogger plugin for PocketMine-MP
 * Copyright (C) 2017 JackNoordhuis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

namespace jacknoordhuis\combatlogger;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class CombatLogger extends PluginBase {

	/** @var Config */
	private $settings;

	/** @var MessageManager */
	private $messageManager = null;

	/** @var EventListener */
	private $listener = null;

	/** @var int[] */
	public $taggedPlayers = [];

	/** Config files */
	const SETTINGS_FILE = "Settings.yml";

	public function onEnable(): void{
		$this->loadConfigs();
		$this->setMessageManager();
		$this->setListener();
		$this->startHeartbeat();
		$this->getLogger()->info(TF::AQUA . "CombatLogger v1.0.0" . TF::GREEN . " by " . TF::YELLOW . "JackNoordhuis" . TF::GREEN . ", Loaded successfully!");
	}

	public function loadConfigs(): void {
		$this->saveResource(self::SETTINGS_FILE);
		$this->settings = new Config($this->getDataFolder() . self::SETTINGS_FILE, Config::YAML);
	}

	public function onDisable(): void {
		$this->taggedPlayers = [];
		$this->getLogger()->info(TF::AQUA . "CombatLogger v0.0.2" . TF::GOLD . " by " . TF::YELLOW . "JackNoordhuis" . TF::GOLD . ", has been disabled!");
	}

	/**
	 * Set the message manager
	 */
	public function setMessageManager() {
		$this->messageManager = new MessageManager($this, $this->getSettingsProperty("messages", []));
	}

	/**
	 * Set the event listener
	 */
	public function setListener() {
		$this->listener = new EventListener($this);
	}

	/**
	 * @return MessageManager
	 */
	public function getMessageManager(): MessageManager {
		return $this->messageManager;
	}

	/**
	 * @return EventListener
	 */
	public function getListener(): EventListener {
		return $this->listener;
	}

	/**
	 * Start the heartbeat task
	 */
	public function startHeartbeat() {
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new TaggedHeartbeatTask($this), 20);
	}

	/**
	 * @param string $nested
	 * @param array $default
	 *
	 * @return mixed
	 */
	public function getSettingsProperty(string $nested, array $default = []) {
		return $this->settings->getNested($nested, $default);
	}

	/**
	 * @param Player|string $player
	 * @param bool $value
	 * @param int $time
	 */
	public function setTagged(Player $player, bool $value = true, int $time = 10) {
		if($player instanceof Player) $player = $player->getName();
		if($value) {
			$this->taggedPlayers[$player] = $time;
		} else {
			unset($this->taggedPlayers[$player]);
		}
	}

	/**
	 * @param Player|string $player
	 *
	 * @return bool
	 */
	public function isTagged(Player $player): bool {
		if($player instanceof Player) $player = $player->getName();
		return isset($this->taggedPlayers[$player]);
	}

	/**
	 * @param Player|string $player
	 *
	 * @return int
	 */
	public function getTagDuration(Player $player): int {
		if($player instanceof Player) $player = $player->getName();
		return ($this->isTagged($player) ? $this->taggedPlayers[$player] : 0);
	}

}