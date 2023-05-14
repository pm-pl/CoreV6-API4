<?php

declare(strict_types=1);

namespace TheNote\core\invmenu\session\network\handler;

use Closure;
use TheNote\core\invmenu\session\network\NetworkStackLatencyEntry;
use pocketmine\network\mcpe\protocol\types\DeviceOS;

final class PlayerNetworkHandlerRegistry{

	private PlayerNetworkHandler $default;

	/** @var PlayerNetworkHandler[] */
	private array $game_os_handlers = [];

	public function __construct(){
		$this->registerDefault(new ClosurePlayerNetworkHandler(static function(Closure $then) : NetworkStackLatencyEntry{
			return new NetworkStackLatencyEntry(mt_rand() * 1000 /* TODO: remove this hack */, $then);
		}));
		$this->register(DeviceOS::PLAYSTATION, new ClosurePlayerNetworkHandler(static function(Closure $then) : NetworkStackLatencyEntry{
			$timestamp = mt_rand();
			return new NetworkStackLatencyEntry($timestamp, $then, $timestamp * 1000);
		}));
	}

	public function registerDefault(PlayerNetworkHandler $handler) : void{
		$this->default = $handler;
	}

	public function register(int $os_id, PlayerNetworkHandler $handler) : void{
		$this->game_os_handlers[$os_id] = $handler;
	}

	public function get(int $os_id) : PlayerNetworkHandler{
		return $this->game_os_handlers[$os_id] ?? $this->default;
	}
}