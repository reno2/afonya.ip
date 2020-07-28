<?php


use \Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Application;

class afonya_ip extends CModule{
		/**
		 * afonya_ip constructor.
		 */
		function __construct()
		{
				$this->MODULE_ID = 'afonya.ip';
				$this->MODULE_NAME = "Афоня ip module";
				$this->MODULE_DESCRIPTION = 'create ORM and add ip';
				$this->PARTNER_NAME        ="Афоня";
				$this->PARTNER_URI         = "";
				$arModuleVersion = array();
				include(__DIR__."/version.php");
				if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
				{
						$this->MODULE_VERSION = $arModuleVersion["VERSION"];
						$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
				}
		}

		/**
		 * @return bool
		 */
		public function isVersionD7()
		{
				return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
		}

		/**
		 * @throws \Bitrix\Main\LoaderException
		 */
		public function DoInstall()
		{
				global $APPLICATION;
				if ($this->isVersionD7())
				{
						\Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
						Loader::includeModule("afonya.ip");
						$this->InstallEvents();
						$this->InstallDB();
				}
				else
				{
						$APPLICATION->ThrowException("Не поддерживается ядро D7");
				}
				$APPLICATION->IncludeAdminFile("Установка модуля", $_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID.  "/install/step.php");
		}

		function DoUninstall()
		{
				global $APPLICATION;

				$context = Application::getInstance()->getContext();
				$request = $context->getRequest();

				if($request["step"]<2)
				{
						$APPLICATION->IncludeAdminFile("Удаление модуля", $_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/unstep1.php");
				}
				elseif($request["step"]==2)
				{
						$this->UnInstallFiles();
						$this->UnInstallEvents();
						if ($request["savedata"] != "Y")
								$this->UnInstallDB();

						\Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);



						$APPLICATION->IncludeAdminFile("Удаление модуля", $_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/unstep2.php");
				}
		}

		/**
		 * @return bool|void
		 */
		function InstallEvents()
		{

				// Вызывается после создания и расчета обьекта заказа.
				EventManager::getInstance()->registerEventHandler(
						'sale',
						'OnSaleOrderSaved',
						$this->MODULE_ID,
						"Afonya\Ip\Main",
						'onSaleOrderSaved'
				);
				return true;
		}

		function InstallDB()
		{
				Loader::includeModule($this->MODULE_ID);
				if (!Application::getConnection(\Afonya\Ip\Table::getConnectionName())->isTableExists(
						Base::getInstance('\Afonya\Ip\Table')->getDBTableName()
				)
				)
				{
						Base::getInstance('\Afonya\Ip\Table')->createDbTable();
				}

				return true;
		}


		public function UnInstallDB(){

				Loader::includeModule($this->MODULE_ID);
				// Проверяет от текущего подключения

				Application::getConnection(\Afonya\Ip\Table::getConnectionName())->
				queryExecute('drop table if exists ' . Base::getInstance('\Afonya\Ip\Table')->getDBTableName());

				Option::delete($this->MODULE_ID);

		}

		function UnInstallEvents()
		{


				EventManager::getInstance()->unRegisterEventHandler(
						'sale',
						'OnSaleComponentOrderCreated',
						$this->MODULE_ID,
						"Afonya\Ip\Main",
						'onSaleOrderSaved'
				);

				return false;
		}

}