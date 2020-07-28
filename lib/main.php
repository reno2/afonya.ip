<?php
declare(strict_types=1);

namespace Afonya\Ip;

use Bitrix\Main\Error;
use \Bitrix\Sale;
use \Afonya\Ip\Table;
use \Bitrix\Main\Web\HttpClient;
use \Bitrix\Main\Diag\Debug;


class Main{
		/** @var string адресс пользователя */
		protected  static $ip;
		/** @var string адресс  */
		protected  static $url = 'https://rest.db.ripe.net/search.json?query-string=';


		/**
		 * Возвращает сериализованную строку
		 * @return string
		 */
		public static function getData() : string {

				$result = self::$url.self::$ip;
				$httpClient = new HttpClient();
				$response = $httpClient->get($result);
				if($httpClient->getStatus() == 200){
						$r = json_decode($response);
						return serialize($r->objects->object);
				}


		}


		/**
		 * @param $orderId
		 *
		 * @return bool
		 * @throws \Exception
		 */
		public static function addRow($orderId) : bool {
				$data = self::getData();

				$result = Table::add(array(
						'ORDER_ID' => $orderId,
						'DATA' =>  $data,
						'IP'   => self::$ip
				));

				$result = ($result->isSuccess() ? true : false);
				if($result->isSuccess()){
						return true;
				}else{
						$error = $result->getErrorMessages();
						Debug::dumpToFile($error);
						return false;
				}

		}

		/**
		 * @param \Bitrix\Main\Event $event
		 *
		 * @throws \Exception
		 */
		public function onSaleOrderSaved(\Bitrix\Main\Event $event)
		{
				if(!$event->getParameter('IS_NEW'))
						return;
				$parameters = $event->getParameters();
				$order = $event->getParameter('ENTITY');
				self::$ip = $_SERVER['REMOTE_ADDR'];
				if($order instanceof Sale\Order)
				{
						$orderId = $order->getId();
						self::addRow($orderId);
				}
		}


}