<?php
declare(strict_types=1);

namespace Afonya\Ip;

use Bitrix\Main\Entity;
use Bitrix\Main\Type;

class Table extends Entity\DataManager
{
		/**
		 * @return string
		 */
		public static function getTableName() : string
		{
				return 'afonya_table';
		}


		/**
		 * @return array
		 * @throws \Bitrix\Main\SystemException
		 */
		public static function getMap() : array
		{
				return array(
						//ID
						new Entity\IntegerField('ID', array(
								'primary'      => true,
								'autocomplete' => true
						)),
						//ORDER_ID
						new Entity\IntegerField('ORDER_ID'),
						//Название
						new Entity\TextField('DATA', array(
								'required' => true,
						)),

				);
		}
}