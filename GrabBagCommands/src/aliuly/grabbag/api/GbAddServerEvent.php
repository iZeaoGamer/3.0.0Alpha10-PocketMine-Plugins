<?php
namespace aliuly\grabbag\api;

use aliuly\grabbag\Main as GrabBagPlugin;
use aliuly\grabbag\api\GrabBagEvent;
use pocketmine\event\Cancellable;

/**
 * Triggered when a new server is being added to the server list
 */
class GbAddServerEvent extends GrabBagEvent implements Cancellable{
	public static $handlerList = null;
	private $serverId;
	private $serverAttrs;

	/**
	 * @param GrabBagPlugin $plugin - plugin owner
	 * @param string        $id - server id
	 * @param array         $attrs - server attributes
	 */
	public function __construct(GrabBagPlugin $plugin, string $id, array $attrs){
		parent::__construct($plugin);
		$this->serverId = $id;
		$this->serverAttrs = $attrs;
	}

	/**
	 * Returns the server id
	 * @return string
	 */
	public function getId(): string{
		return $this->serverId;
	}

	/**
	 * Sets the server id
	 * @param string $id
	 */
	public function setId(string $id){
		$this->serverId = $id;
	}

	/**
	 * Gets the server attributes
	 * @return array
	 */
	public function getAttrs(): array{
		return $this->serverAttrs;
	}

	/**
	 * Sets the server attributes
	 * @param array $attrs
	 */
	public function setAttrs(array $attrs){
		$this->serverAttrs = $attrs;
	}
}
