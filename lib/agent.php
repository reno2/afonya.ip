<?php
declare(strict_types=1);

namespace Afonya\Ip;

use Bitrix\Main\Error;
use \Bitrix\Sale;
use \Afonya\Ip\Table;
use \Bitrix\Main\Web\HttpClient;
use \Bitrix\Main\Diag\Debug;
use Afonya\Ip\Main;

class Agent{
	function updateData(){
		Main::getEmptyData();
		return __CLASS__ . "::updateData();";
	}
}