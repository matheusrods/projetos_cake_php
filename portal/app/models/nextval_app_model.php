<?php
/* SVN FILE: $Id: app_model.php 7945 2008-12-19 02:16:01Z gwoo $ */
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.model
 * @since         CakePHP(tm) v 0.2.9
 * @version       $Revision: 7945 $
 * @modifiedby    $LastChangedBy: gwoo $
 * @lastmodified  $Date: 2008-12-18 20:16:01 -0600 (Thu, 18 Dec 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Application model for Cake.
 *
 * This is a placeholder class.
 * Create the same file in app/app_model.php
 * Add your application-wide methods to the class, your models will inherit them.
 *
 * @package       cake
 * @subpackage    cake.cake.libs.model
 */
class NextvalAppModel extends AppModel {

	public function novo_codigo_direto(){
		if ($this->useDbConfig != 'test_suite') {
			$dbo = $this->getDatasource();
			$conn = @pg_connect("host=".$dbo->config['host']." user=".$dbo->config['login']." password=".$dbo->config['password']." dbname=".$dbo->config['database']) or die('Falha na conexao!');
			$sql = "SELECT nextval('public.s_{$this->useTable}') as ID;";

			$result = pg_query($sql) or die('Erro! '.pg_last_error());
			$row = pg_fetch_object($result);
			@pg_close($conn);
			return $row->id;
		} else {
			$fields 	= array("MAX($this->primaryKey)+1 AS 'nextval'");
			$retorno 	= $this->find('first',compact('fields'));
			return $retorno[0]['nextval'];
		}
		
	}

	public function incluir($data) {
		if (isset($data[$this->name])) {
			if (!isset($data[$this->name][$this->primaryKey]) || empty($data[$this->name][$this->primaryKey])) {
				$data[$this->name][$this->primaryKey] = $this->novo_codigo_direto();
				$data[$this->name][substr($this->primaryKey,0,5).'data_cadastro'] = Date('Y-m-d H:i:s');
				if (isset($_SESSION['Auth']['Usuario']['apelido'])) {
					$data[$this->name][substr($this->primaryKey,0,5).'usuario_adicionou'] = trim(substr($_SESSION['Auth']['Usuario']['apelido'],0,20));
				}
				$this->create();
				return $this->save($data);
			} else {
				$this->invalidate($this->primaryKey, "{$this->primaryKey} não deve ser informado");
				return false;
			}
		}
		return false;
	}

	public function incluirMultiplo($datas, $in_another_transaction = false) {
		$validData = true;
		$validationErrors = array();
		if (!$in_another_transaction) $this->query('begin transaction');
		foreach ($datas as $key => $data) {
			if (isset($data[$this->name])) {
				if (!isset($data[$this->name][$this->primaryKey]) || empty($data[$this->name][$this->primaryKey])) {
					$data[$this->name][$this->primaryKey] = $this->novo_codigo_direto();
					$data[$this->name][substr($this->primaryKey,0,5).'data_cadastro'] = Date('Y-m-d H:i:s');
					if (isset($_SESSION['Auth']['Usuario']['apelido'])) {
						$data[$this->name][substr($this->primaryKey,0,5).'usuario_adicionou'] = substr($_SESSION['Auth']['Usuario']['apelido'],0,20);
					}

					$this->create();
					if (!$this->save($data)) {
						$validData = false;
						$validationErrors[$key] = $this->invalidFields();
					}
				} else {
					$this->invalidate($this->primaryKey, "{$this->primaryKey} não deve ser informado");
					$validationErrors[$key] = $this->invalidFields();
					$validData = false;
				}
			}
		}
		if (!$validData) {
			$this->validationErrors = $validationErrors;
			if (!$in_another_transaction) $this->rollback();
		} else {
			if (!$in_another_transaction) $this->commit();
		}
		return $validData;
	}
}
?>