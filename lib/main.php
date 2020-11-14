<?php
declare(strict_types=1);

namespace Afonya\Ip;

use Afonya\Ip\Table;
use Bitrix\Main\Error;
use \Bitrix\Sale;

use \Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Diag\Debug;


class Main{

	/** @var string ������  */
	const URL = 'https://rest.db.ripe.net/search.json?query-string=';


		/**
		 * ���������� ��������������� ������
		 *
		 * @param $ip
		 *
		 * @return string
		 */
		public static function getData($ip) : string {

			$result = self::URL.$ip;
			$httpClient = new HttpClient();
			$response = $httpClient->get($result);
				// ��� ������� ����� ��������� �������, �������� ��� ������ ������, ������� �� ��������� � ���������� ��������
			if($httpClient->getStatus() == 200){
				$r = json_decode($response);
				return serialize($r->objects->object);
			}else{
				return 'no result';
			}

		}


		/**
		 * @param $row
		 *
		 * @return bool
		 * @throws \Exception
		 */
		public static function addRow($row) : bool {

			$result = Table::add($row);
			if($result->isSuccess()){
				AddMessage2Log($result->getId() . 'row_add', 'afonya.ip');
				return true;
			}else{
				AddMessage2Log($result->getErrorMessages(). 'error', 'afonya.ip');
				return false;
			}
		}


		public static function getEmptyData() : void {


			$result = Table::getList(array(
				'filter' => array('=DATA' => 'null')
			));
			$row = $result->fetch();
			if($row){
				$res = self::getData($row['IP']);

				if($res == 'no result'){

						// ��� ���� ������ ������ ���� ���������� �� ������, ����� �� �������� ������
					$result = Table::delete($row['ID']);
				}else{

					$result = Table::update($row['ID'], array(
						'DATA' => $res
					));
				}
				if($result->isSuccess()){
					AddMessage2Log($row['ID'] . '���������� ������', 'afonya.ip');
				}else{
					AddMessage2Log($row['ID'] . '������� ������', 'afonya.ip');
				}

			}


		}
		/**
		 * @param \Bitrix\Main\Event $event
		 *
		 * @throws \Exception
		 */
		public function onSaleOrderSaved(\Bitrix\Main\Event $event) : void
		{
			if(!$event->getParameter('IS_NEW'))
				return;

			$order = $event->getParameter('ENTITY');
			$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
			$ip = $request->getRemoteAddress();
			if($order instanceof Sale\Order)
			{
				$orderId = $order->getId();
				self::addRow(array(
					'ORDER_ID' => $orderId,
					'DATA' =>  'null',
					'IP'   => $ip
				));
			}
		}


	}