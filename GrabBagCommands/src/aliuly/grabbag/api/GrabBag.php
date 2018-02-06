<?php
namespace aliuly\grabbag\api;

use aliuly\grabbag\Main as GrabBagPlugin;
use pocketmine\Player;
use pocketmine\entity\Human;
use pocketmine\command\CommandSender;

use aliuly\grabbag\common\mc;
use aliuly\grabbag\common\ExpandVars;
use aliuly\grabbag\common\PMScript;

/**
 * GrabBag API
 *
 * Example Usage:
 *
 * Check if GrabBag is installed....
 * <code>
 * $api = null;
 * if (($plugin = $server->getPluginManager()->getPlugin("GrabBag") !== null) && $plugin->isEnabled() && MPMU::apiCheck($plugin->getDescription()->getVersion(),"2.3")) {
 *   if ($plugin->api->getFeature("freeze-thaw")) $api = $plugin->api;
 * }
 * </code>
 *
 * Call an API function:
 *
 * <code>
 *   $api->freeze($player);
 * </code>
 */
class GrabBag{
	protected $plugin;
	protected $vars;
	protected $interp;

	/**
	 * @param GrabBagPlugin $owner - plugin that owns this session
	 */
	public function __construct(GrabBagPlugin $owner){
		$this->plugin = $owner;
		$this->vars = null;
		$this->interp = null;
	}

	/**
	 * Check if module is available...
	 * This will throw an exception if the module is not available
	 * @param string $module - module name
	 * @return mixed|null
	 */
	public function getModule(string $module): null{
		$vp = $this->plugin->getModule($module);
		if($vp === null) throw new \RuntimeException("Missing module: " . $module);
		return $vp;
	}

	/**
	 * Check if feature is supported and has been enabled in the GrabBag
	 * configuration file.
	 * @param string $feature - module name
	 * @return bool
	 */
	public function getFeature(string $feature): bool{
		if(!in_array($feature, [
			"freeze-thaw", "invisible", "after-at", "cmd-alias", "blowup",
			"chat-utils", "followers", "mute-unmute", "opms-rpt", "reop",
			"shield", "skinner", "slay", "spawn", "srvmode", "summon-dismiss",
			"throw", "pushtp-poptp",
			"ServerList",
		])) return false;
		if($this->plugin->getModule($feature) === null) return false;
		return true;
	}

	/**
	 * Currently un-implemented
	 */
	public function getVars(){
		if($this->vars === null){
			$this->vars = new ExpandVars($this->plugin);
		}
		return $this->vars;
	}

	/**
	 * Currently un-implemented
	 */
	public function getInterp(){
		if($this->interp == null){
			$this->interp = new PMScript($this->plugin, $this->getVars());
		}
		return $this->interp;
	}

	//////////////////////////////////////////////////////////////
	// CmdFreeze
	//////////////////////////////////////////////////////////////
	/**
	 * Checks if hard or soft freezing
	 * @return bool
	 */
	public function isHardFreeze(): bool{
		return $this->getModule("freeze-thaw")->isHardFreeze();
	}

	/**
	 * Sets hard or soft freezing
	 * @param bool $hard - if true (default) hard freeze is in effect.
	 */
	public function setHardFreeze(bool $hard = true){
		$this->getModule("freeze-thaw")->setHardFreeze($hard);
	}

	/**
	 * Freeze given player
	 * @param Player $player - player to freeze
	 * @param bool   $freeze - if true (default) freeze, if false, thaw.
	 */
	public function freeze(Player $player, bool $freeze = true){
		$this->getModule("freeze-thaw")->freeze($player, $freeze);
	}

	/**
	 * Return a list of frozen players
	 * @return string[]
	 */
	public function getFrosties(){
		return $this->getModule("freeze-thaw")->getFrosties();
	}
	//////////////////////////////////////////////////////////////
	// CmdInvisible
	//////////////////////////////////////////////////////////////
	/**
	 * Make player invisible
	 * @param Player $player - player to change
	 * @param bool   $invis - if true (default) invisible, if false, visible.
	 */
	public function invisible(Player $player, bool $invis){
		if($invis){
			if(!$this->getModule("invisible")->isInvisible($player))
				$this->getModule("invisible")->activate($player);
		}else{
			if($this->getModule("invisible")->isInvisible($player))
				$this->getModule("invisible")->deactivate($player);
		}
	}

	/**
	 * Check if player is invisible...
	 * @param Player $player - player to check
	 * @return bool
	 */
	public function isInvisible(Player $player) : bool{
		return $this->getModule("invisible")->isInvisible($player);
	}
	//////////////////////////////////////////////////////////////
	// CmdAfterAt
	//////////////////////////////////////////////////////////////
	/**
	 * Schedule a command to be run
	 * @param int    $secs - execute after this number of seconds
	 * @param string $cmdline - command line to execute
	 */
	public function after(string $cmdline, int $secs){
		$this->getModule("after-at")->schedule($secs, $cmdline);
	}
	//////////////////////////////////////////////////////////////
	// CmdAlias
	//////////////////////////////////////////////////////////////
	/**
	 * Define a command alias
	 * @param string $alias - alias name
	 * @param string $cmdline - command line to execute
	 * @param bool   $force - overwrite existing commands
	 * @return bool - true on succes, false on failure
	 */
	public function alias(string $alias, string $cmdline, bool $force = false): bool{
		return $this->getModule("cmd-alias")->addAlias($alias, $cmdline, $force);
	}
	//////////////////////////////////////////////////////////////
	// CmdBlowUp
	//////////////////////////////////////////////////////////////
	/**
	 * Blow player up
	 * @param Player $player - victim
	 * @param int    $yield - explosion power
	 * @param bool   $magic - don't affect blocks
	 * @return bool - true on succes, false on failure
	 */
	public function blowPlayer(Player $player, int $yield, bool $magic = false): bool{
		return $this->getModule("blowup")->blowPlayer($player, $yield, $magic);
	}
	//////////////////////////////////////////////////////////////
	// CmdChatMgr
	//////////////////////////////////////////////////////////////
	/**
	 * Enable/Disable Chat globally
	 * @param bool $mode - true, chat is active, false, chat is disabled
	 */
	public function setGlobalChat(bool $mode){
		$this->getModule("chat-utils")->setGlobalChat($mode);
	}

	/**
	 * Returns global chat status
	 * @return bool
	 */
	public function getGlobalChat(): bool{
		return $this->getModule("chat-utils")->getGlobalChat();
	}

	/**
	 * Enable/Disable player's chat
	 * @param Player $player
	 * @param bool   $mode - true, chat is active, false, chat is disabled
	 */
	public function setPlayerChat(Player $player, bool $mode){
		$this->getModule("chat-utils")->setPlayerChat($player, $mode);
	}

	/**
	 * Returns player's chat status
	 * @param Player $player
	 * @return bool
	 */
	public function getPlayerChat(Player $player): bool{
		return $this->getModule("chat-utils")->getPlayerChat($player);
	}
	//////////////////////////////////////////////////////////////
	// CmdFollowMgr
	//////////////////////////////////////////////////////////////
	/**
	 * Returns players that are leading others
	 * @return string[]
	 */
	public function getLeaders(){
		return $this->getModule("followers")->getLeaders();
	}

	/**
	 * Returns followers of a certain leader
	 * @param Player $leader
	 * @return string[]
	 */
	public function getFollowers(Player $leader){
		return $this->getModule("followers")->getFollowers($leader);
	}

	/**
	 * Make a player follow another
	 * @param Player $follower
	 * @param Player $leader
	 */
	public function follow(Player $follower, Player $leader){
		$this->getModule("followers")->follow($follower, $leader);
	}

	/**
	 * Stop a player from following
	 * @param Player $follower
	 */
	public function stopFollowing(Player $follower){
		$this->getModule("followers")->stopFollowing($follower);
	}

	/**
	 * Remove all folowers from a leader
	 * @param Player $leader
	 */
	public function stopLeading(Player $leader){
		$this->getModule("followers")->stopLeading($leader);
	}
	//////////////////////////////////////////////////////////////
	// CmdMuteMgr
	//////////////////////////////////////////////////////////////
	/**
	 * Returns the list of muted players
	 * @return string[]
	 */
	public function getMutes(){
		return $this->getModule("mute-unmute")->getMutes();
	}

	/**
	 * Mute/UnMute a player
	 * @param Player $player
	 * @param bool   $mode - true is muted, false is unmuted
	 */
	public function setMute(Player $player, bool $mode){
		$this->getModule("mute-unmute")->setMute($player, $mode);
	}

	/**
	 * Returns a player mute status
	 * @param Player $player
	 * @return bool
	 */
	public function getMute(Player $player): bool{
		return $this->getModule("mute-unmute")->getMute($player);
	}
	//////////////////////////////////////////////////////////////
	// CmdOpMsg
	//////////////////////////////////////////////////////////////
	/**
	 * File a report
	 * @param CommandSender $c
	 * @param string        $report
	 */
	public function fileReport(CommandSender $c, string $report){
		$this->getModule("opms-rpt")->rptCmd($player, [">", $report]);
	}
	//////////////////////////////////////////////////////////////
	// CmdReOp
	//////////////////////////////////////////////////////////////
	/**
	 * Return player's reop status
	 * @param Player $target
	 * @return bool
	 */
	public function isReOp(Player $target): bool{
		return $this->getModule("reop")->isReOp($target);
	}

	/**
	 * Toggle player's reop
	 * @param Player $target
	 */
	public function reopPlayer(Player $target){
		$this->getModule("reop")->reopPlayer($target);
	}
	//////////////////////////////////////////////////////////////
	// CmdShieldMgr
	//////////////////////////////////////////////////////////////
	/**
	 * Return player's shield status
	 * @param Player $target
	 * @return bool
	 */
	public function isShielded(Player $target): bool{
		return $this->getModule("shield")->isShielded($target);
	}

	/**
	 * Turn on/off shields
	 * @param Player $target
	 * @param bool   $mode - true is shielded, false is not
	 */
	public function setShield(Player $target, bool $mode){
		$this->getModule("shield")->setShield($target, $mode);
	}
	//////////////////////////////////////////////////////////////
	// CmdSkinner
	//////////////////////////////////////////////////////////////
	/**
	 * Returns a list of skins
	 * @param string $folder - folder to search
	 * @return string[]
	 */
	public function getSkins(string $folder = null){
		return $this->getModule("skinner")->getSkins($folder);
	}

	/**
	 * Save a player's skin
	 * @param Human  $human - character to save
	 * @param string $fn - file name
	 * @param string $folder - folder to search
	 * @return int - bytes written
	 *
	 */
	public function saveSkin(Human $human, string $fn, string $folder = null): int{
		return $this->getModule("skinner")->saveSkin($human, $fn, $folder);
	}

	/**
	 * Load a player's skin
	 * @param Human  $human - character to load
	 * @param string $fn - file name
	 * @param string $folder - folder to search
	 * @return bool - true on succes, false on failure
	 *
	 */
	public function loadSkin(Human $human, string $fn, string $folder = null): bool{
		return $this->getModule("skinner")->loadSkin($human, $fn, $folder);
	}
	//////////////////////////////////////////////////////////////
	// CmdSlay
	//////////////////////////////////////////////////////////////
	/**
	 * Kills a player with optional message
	 * @param Player $victim
	 * @param string $msg
	 */
	public function slay(Player $victim, string $msg = ""){
		$this->getModule("slay")->slay($victim, $msg);
	}
	//////////////////////////////////////////////////////////////
	// CmdSpawn
	//////////////////////////////////////////////////////////////
	/**
	 * Teleport a player to world spawn
	 * @param Player $player
	 */
	public function tpSpawn(Player $player){
		$this->getModule("spawn")->tpSpawn($player);
	}
	//////////////////////////////////////////////////////////////
	// CmdSrvMoDeMgr
	//////////////////////////////////////////////////////////////
	/**
	 * Return the current service mode status
	 * @return false|string
	 */
	public function getServiceMode(): string{
		return $this->getModule("srvmode")->getServiceMode();
	}

	/**
	 * Change the service mode
	 * @param string $msg
	 */
	public function setServiceMode(string $msg){
		$this->getModule("srvmode")->setServiceMode($msg);
	}

	/**
	 * Exists service mode
	 */
	public function unsetServiceMode(){
		$this->getModule("srvmode")->unsetServiceMode();
	}
	//////////////////////////////////////////////////////////////
	// CmdSummon
	//////////////////////////////////////////////////////////////
	/**
	 * Teleport a player to the summoner's vicinity
	 * @param Player $summoner
	 * @param Player $victim
	 */
	public function summonPlayer(Player $summoner, Player $victim){
		$this->getModule("summon-dismiss")->cmdSummon($summoner, [$victim->getName()]);
	}

	/**
	 * Dismiss a previously summoned player
	 * @param Player $summoner
	 * @param Player $victim
	 */
	public function dismissPlayer(Player $summoner, Player $victim){
		$this->getModule("summon-dismiss")->cmdDismiss($summoner, [$victim->getName()]);
	}

	/**
	 * Dismiss all summoned players
	 * @param Player $summoner
	 */
	public function dismissAll(Player $summoner){
		$this->getModule("summon-dismiss")->cmdDismiss($summoner, ["--all"]);
	}
	//////////////////////////////////////////////////////////////
	// CmdThrow
	//////////////////////////////////////////////////////////////
	/**
	 * Throw player up in the air.
	 * @param Player $victim
	 */
	public function throwPlayer(Player $victim){
		$this->getModule("throw")->throwPlayer($victim);
	}
	//////////////////////////////////////////////////////////////
	// CmdTpStack
	//////////////////////////////////////////////////////////////
	/**
	 * Save position to stack
	 * @param Player $player
	 */
	public function pushTp(Player $player){
		$this->getModule("pushtp-poptp")->cmdPushTp($player, []);
	}

	/**
	 * Restore position from stack
	 * @param Player $player
	 */
	public function popTp(Player $player){
		$this->getModule("pushtp-poptp")->cmdPopTp($player, []);
	}

	//////////////////////////////////////////////////////////////
	// ServerList
	//////////////////////////////////////////////////////////////
	/**
	 * Get server ids
	 */
	public function getServerIds(){
		return $this->getModule("ServerList")->getIds();
	}

	/**
	 * Add Server
	 * @param string $id - Server Id
	 * @param array  $attrs - Server attributes
	 * @return bool - true on success, false on error
	 */
	public function addServer(string $id, array $attrs): bool{
		return $this->getModule("ServerList")->addServer($id, $attrs);
	}

	/**
	 * Remove Server
	 * @param string $id - Server Id
	 * @return bool - true on success, false on error
	 */
	public function removeServer(string $id): bool{
		return $this->getModule("ServerList")->rmServer($id);
	}

	/**
	 * Get Server attributes
	 * @param string $id - Server Id
	 * @return array - attributes
	 */
	public function getServer(string $id): array{
		return $this->getModule("ServerList")->getServer($id);
	}
	//
}
