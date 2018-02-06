<?php
use pocketmine\schedule\PluginTask;

class countdownTimer extends PluginTask {

    public function __construct(PluginBase $owner, Player $player, int $secsTotal) {
        parent::__construct($owner);
        $this->player = $player;
        $this->secsTotal = $secsTotal;
    }

    public function onRun(int $currentTick): void{
        $this->endingtime = $this->getOwner()->seconds + $this->secsTotal;
        $this->secondsLeft = $this->endingtime - time();
        if($this->getOwner()->getConfig()->get("debug-message"))
            $this->player->sendTip($this->getOwner()->translateColors("&e" . $this->secondsLeft . "&r"));
        if($this->secondsLeft <= 0){
            if($this->getOwner()->isPlayerAuthenticated($this->player)) {
                $playerdata = $this->getOwner()->getPlayerData($this->player->getName());        
                if($this->getOwner()->getConfig()->get("IPLogin") == true) {
                   if($playerdata["lastip"] == $this->player->getAddress()) {
                        //
                    } else {
                        $this->getOwner()->deauthenticatePlayer($this->player);
                    }
                } else {
                    $this->getOwner()->deauthenticatePlayer($this->player);
                }     
            }
            if(!$this->getOwner()->isPlayerRegistered($this->player->getName())) {
                $this->getOwner()->createForm(0, $this->player);
            } else {
                if(!$this->getOwner()->isPlayerAuthenticated($this->player)) {
                    $this->getOwner()->createForm(1, $this->player);
                }
            }
            $this->getOwner()->getServer()->getScheduler()->cancelTasks($this->getOwner());
        }
    }