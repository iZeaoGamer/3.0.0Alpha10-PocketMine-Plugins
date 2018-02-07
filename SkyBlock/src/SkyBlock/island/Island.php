<?php

namespace SkyBlock\island;


use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\Config;
use SkyBlock\Utils;

class Island {

    /** @var Config */
    private $config;

    /** @var string */
    private $ownerName;

    /** @var string */
    private $identifier;

    /** @var Player[] */
    private $playersOnline = [];

    /** @var string[] */
    private $members;

    /** @var bool */
    private $locked;

    /** @var string */
    private $home;

    /** @var string */
    private $generator;

    /**
     * Island constructor.
     *
     * @param Config $config
     * @param string $ownerName
     * @param string $identifier
     * @param array $members
     * @param bool $locked
     * @param string $home
     * @param string $generator
     */
    public function __construct(Config $config, string $ownerName, string $identifier, array $members, bool $locked, string $home, string $generator) {
        $this->config = $config;
        $this->ownerName = $ownerName;
        $this->identifier = $identifier;
        $this->members = $members;
        $this->locked = $locked;
        $this->home = $home;
        $this->generator = $generator;
    }

    /**
     * Return island config
     *
     * @return Config
     */
    public function getConfig(): Config {
        return $this->config;
    }

    /**
     * Return owner name
     *
     * @return string
     */
    public function getOwnerName(): string {
        return $this->ownerName;
    }

    /**
     * Return identifier
     *
     * @return string
     */
    public function getIdentifier(): string {
        return $this->identifier;
    }

    /**
     * Return island online players
     *
     * @return Player[]
     */
    public function getPlayersOnline(): Player {
        return $this->playersOnline;
    }

    /**
     * Return island members
     *
     * @return string[]
     */
    public function getMembers(): array {
        return $this->members;
    }

    /**
     * Return if the island is locked
     *
     * @return boolean
     */
    public function isLocked(): boolean {
        return $this->locked;
    }

    /**
     * Return island home not parsed
     *
     * @return string
     */
    public function getHome(): string {
        return $this->home;
    }

    /**
     * Return home position
     *
     * @return Position
     */
    public function getHomePosition(): Position {
        return Utils::parsePosition($this->home);
    }

    /**
     * Return if the island has a home
     *
     * @return bool
     */
    public function hasHome(): bool {
        return $this->getHomePosition() instanceof Position;
    }

    /**
     * Return all members (also the owner)
     *
     * @return array|\string[]
     */
    public function getAllMembers(): array {
        $members = $this->members;
        $members[] = $this->ownerName;
        return $members;
    }

    /**
     * Return island generator
     *
     * @return string
     */
    public function getGenerator(): string {
        return $this->generator;
    }

    /**
     * Add a player to the island
     *
     * @param Player $player
     */
    public function addPlayer(Player $player) {
        $this->playersOnline[] = $player;
    }

    /**
     * Set owner name
     *
     * @param $ownerName
     */
    public function setOwnerName($ownerName) {
        $this->ownerName = $ownerName;
    }

    /**
     * Set island identifier
     *
     * @param string $identifier
     */
    public function setIdentifier(string $identifier) {
        $this->identifier = $identifier;
    }

    /**
     * Set island players online
     *
     * @param Player[] $playersOnline
     */
    public function setPlayersOnline(Player $playersOnline) {
        $this->playersOnline = $playersOnline;
    }

    /**
     * Set island members
     *
     * @param string[] $members
     */
    public function setMembers(array $members) {
        $this->members = $members;
    }

    /**
     * Add a member to the team
     *
     * @param Player $player
     */
    public function addMember(Player $player) {
        $this->members[] = strtolower($player->getName());
    }

    /**
     * Set the island locked
     *
     * @param boolean $locked
     */
    public function setLocked(boolean $locked = true) {
        $this->locked = $locked;
    }

    /**
     * Set not parsed home
     *
     * @param string $home
     */
    public function setHome(string $home) {
        $this->home = $home;
    }

    /**
     * Set home position
     *
     * @param Position $position
     */
    public function setHomePosition(Position $position) {
        $this->home = Utils::createPositionString($position);
    }

    /**
     * Set island config
     *
     * @param Config $config
     */
    public function setConfig(Config $config) {
        $this->config = $config;
    }

    /**
     * Set island generator
     *
     * @param string $generator
     */
    public function setGenerator(string $generator) {
        $this->generator = $generator;
    }

    /**
     * Tries to remove a player
     *
     * @param Player $player
     */
    public function tryRemovePlayer(Player $player) {
        if(in_array($player, $this->playersOnline)) {
            unset($this->playersOnline[array_search($player, $this->playersOnline)]);
        }
    }

    /**
     * Remove member
     *
     * @param string $string
     */
    public function removeMember(string $string) {
        if(in_array($string, $this->members)) {
            unset($this->members[array_search($string, $this->members)]);
        }
    }

    public function update() {
        $this->config->set("owner", $this->getOwnerName());
        $this->config->set("home", $this->getHome());
        $this->config->set("locked", $this->isLocked());
        $this->config->set("members", $this->getMembers());
        $this->config->save();
    }

}