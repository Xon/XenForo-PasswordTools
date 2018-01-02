<?php

/**
 * Class: KL_Install
 *
 * General addon installer. Fetches ID of currently installed version and version to
 * install and passes them to the specific addon installer.
 *
 * Created:			31/03/2016
 * Last changed:	04/04/2016
 * Change Counter:	3
 */

class KL_Install {
	/**
	 * Get XenForo Filepath for (Un-)Install File.
	 * Catch Windows Pathsystem.
	 *
	 * @param string $fileName
	 *
	 * @return string
	 */
	private static function _getDataFilePath($fileName) {
		$dataPath = XenForo_Application::getInstance()->getRootDir(); 

		if (strpos($dataPath, '\\') !== FALSE) {
			$dataPath .= '\\library\\KL\\!DATA\\';	
		}
		else {
			$dataPath .= '/library/KL/!DATA/';
		}

		return $dataPath . $fileName;
	}

	/**
	 * Load Install/Uninstall Data for addon.
	 * 
	 * @param string $addonId
	 * @param string $type
	 *
	 * @return array
	 */
	private static function _getData($addonId, $type = '-Install') {
		/* Remove 'KL_' from AddonId */
		$strippedId = substr($addonId, 3);
		$strippedId .= $type . '.dat';

		$file = self::_getDataFilePath($strippedId);
		$object = json_decode(file_get_contents($file));
		if(is_object($object)) {
			return get_object_vars($object);
		}
		else return null;
	}

	/**
	 * Get XenForo Filepath for random file.
	 * Catch Windows pathsystem
	 *
	 * @param string $filename
	 * @return string
	 */
	private static function _getFilePath($fileName) {
		$dataPath = XenForo_Application::getInstance()->getRootDir(); 

		if (strpos($dataPath, '\\') !== FALSE) {
			$filePath = $dataPath . str_replace('_','\\','_' . $fileName);
		}
		else {
			$filePath = $dataPath . str_replace('_','/','_' . $fileName);
		}

		return $filePath;
	}

	/**
	 * Process all standard installation data.
	 *
	 * @param array $data
	 * @param integer $addonVersionPrev
	 */
	private static function _processData(array $data, $addonVersionPrev = 0) {
		/* Get Model and Datawriter */
		$model		= XenForo_Model::create("XenForo_Model_AddOn");

		/* Remove deprecated addons */
		if(isset($data['addonsToUninstall'])) {
			foreach($data['addonsToUninstall'] as $addonId) {
				$addon = $model->getAddonById($addonId);

				if(!empty($addon)) {
					$datawriter	= XenForo_DataWriter::create('XenForo_DataWriter_AddOn');
					$datawriter->setExistingData($addon);
					$datawriter->delete();
				}
			}
		}

		/* Do Install & Update Queries */
		if(isset($data['queries'])) {
			/* Get Database connection */
			$db	= XenForo_Application::get('db');

			foreach($data['queries'] as $query) {
				if(isset($query->type)) {
					$string = '';
					switch($query->type) {
						case 'CREATE TABLE':
							$string = 'CREATE TABLE IF NOT EXISTS `' . $query->table_name . "` (\n";
							foreach($query->fields as $field) {
								$field[0] = '`' . $field[0] . '`';
								$string .= "\t" . implode(' ', $field) . ", \n";
							}
							if(isset($query->key))
								$string .= "\tPRIMARY KEY (`" . $query->key . "`) \n";

							$string .= ") \n";

							if(isset($query->charset))
								$string .= "DEFAULT CHARSET=`" . $query->charset . "` ";

							if(isset($query->comment))
								$string .= "COMMENT='" . $query->comment . "'";

							$string .= "; \n";
							break;
						case 'REPLACE INTO':
							$string = 'REPLACE INTO `' . $query->table_name . "`\n";

							$string .= "\t(`" . implode('`, `', $query->fields) . "`) VALUES \n";

							foreach($query->values as $value) {
								$string .= "\t('" . implode("', '", $value) . "'),\n";
							}

							$string = trim($string);
							$string = rtrim($string, ',');

							$string .= ";\n";
							break;
						case 'ADD COLUMN':
							$string = "SHOW TABLES LIKE '" . $query->table_name . "'";
							$result = $db->fetchRow($string);
							if($result) {
								$string = 'DESCRIBE `' . $query->table_name . '`';
								$result = $db->fetchAll($string);
								$fields = array();
								foreach($result as $row) {
									$fields[] = $row['Field'];
								}
								if(!in_array($query->field[0], $fields)) {
									$string = 'ALTER TABLE `' . $query->table_name . "` ADD\n";
									$query->field[0] = '`' . $query->field[0] . '`';
									$string .= "\t" . implode(' ', $query->field) . "\n";
								}
								$string .= ';';
							} else {
								$string = NULL;
							}
							break;
						default:
							break;
					}
					if(!is_null($string))
						$db->query($string);
				} else { // OLD VERSION
					foreach($data['queries'] as $versionNumber => $queries) {
						/* Check for version number */
						if($addonVersionPrev <= $versionNumber) {
							/* Execute Queries */
							foreach($queries as $query) {
								if($query) {
									$db->query($query);
								}
							}
						}
					}
				}
			}
		}

		/* Delete specified files */
		if(isset($data['filesToDelete'])) {
			foreach($data['filesToDelete'] as $file) {
				$fileName = self::_getFilePath($file);
				if(file_exists($fileName) && is_writeable($fileName)) {
					unlink($fileName);
				}
			}
		}

		/* Delete specified folders */
		if(isset($data['foldersToRemove'])) {
			foreach($data['foldersToDelete'] as $folder) {
				$folderName = self::_getFilePath($folder);
				rmdir($folderName);
			}
		}
	}

	/**
	 * Install an addon.
	 */
	public static function install($existingAddon, $addonData) {
		$addonId = $addonData['addon_id'];

		/* Fetch current and new addon version id */
		$addonVersion = $addonData['version_id'];
		$addonVersionPrev = $existingAddon['version_id'];

		/* Fetch installation data */
		$installData = self::_getData($addonId);

		/* Do general installation */
		if(is_array($installData)) {
			self::_processData($installData, $addonVersionPrev);
		}

		/* Execute specific installer */
		if(class_exists($addonId.'_Install')) {
			if(method_exists($addonId.'_Install', 'install')) {
				call_user_func($addonId.'_Install::install', $addonVersion, $addonVersionPrev);
			}
		}
	}


	/**
	 * Uninstall an addon.
	 */
	public static function uninstall() {
		/* Get the addon to uninstall */
		$paramKeys = array_keys($_GET);
		$addonId = substr(substr(array_keys($_GET)[0],8),0,-7);

		/* Fetch installation data */
		$uninstallData = self::_getData($addonId, '-Uninstall');

		/* Uninstall */
		if(is_array($uninstallData)) {
			self::_processData($uninstallData);
		}

		/* Execute specific uninstaller */
		if(class_exists($addonId.'_Install')) {
			if(method_exists($addonId.'_Install', 'uninstall')) {
				call_user_func($addonId.'_Install::uninstall');
			}
		}
	}

}