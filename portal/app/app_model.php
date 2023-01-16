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
class AppModel extends Model {
	var $actsAs = array('DataFormatter');

	function __construct($id = false, $table = null, $ds = null) {
        $this->_findMethods['sql'] = true; 
        parent::__construct($id, $table, $ds); 
    } 

    function _findSql($state, $query, $results = array()) { 
        if ($state == 'before') { 
            $query['returnSQL'] = true; 
            return $query; 

        } elseif ($state == 'after') { 
            return $results; 
        } 
    } 
	
	public static function dbDateToDate($dbDate){
        if (substr($dbDate,4,1) == '-')
            return preg_replace("/(\d{2,4})\-(\d{2})\-(\d{2})(\w*)/", "$3/$2/$1$4", $dbDate);
        return preg_replace("/(\d{4})(\d{2})(\d{2})(\w*)/", "$3/$2/$1$4", $dbDate);
	}
	public static function dateToDbDate($date){
		return preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})/", "$3$2$1", $date);
	}
	function dateTimeToDbDateTime($date){
		return preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})(\w*)/", "$3$2$1$4", $date);
	}
	public static function dateToDbDate2($date){
		return preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})/", "$3-$2-$1", $date);
	}
	function dateTimeToDbDateTime2($date){
		return preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})(\w*)/", "$3-$2-$1$4", $date);
	}
	
    public static function formataCpf($cpf) {
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", substr(str_pad($cpf, 11, '0', STR_PAD_LEFT), -11));
    }
	
	public static function range($check, $lower = null, $upper = null, $equal = false) {
		while(is_array($check)) $check = current($check);
		return Comum::range($check,$lower,$upper,$equal);
	}

	function validaDateTime($datetime = "", $format = 'dmy', $separador = ' ') {
		while(is_array($datetime)) $check = current($datetime);
		return Comum::validaDateTime($datetime,$format,$separador);
	}	
	
	/**
	 * Método sobreescrito para retornar true caso o exclusão tenha sido efetuada com sucesso
	 * @param int $id
	 * @return bool
	 */
	function delete($id){
		if (!isset($id) || $id == null) return false;
		parent::delete($id);
		$primaryKey = (isset($this->primaryKey) ? $this->primaryKey : 'id');
		return !$this->find('count', array('conditions'=>array($primaryKey => $id), 'recursive'=>-1));	
	}
	
	function excluir($id){
		if ($id == null) return false;
		parent::delete($id);
		$primaryKey = (isset($this->primaryKey) ? $this->primaryKey : 'id');
		return !$this->find('count', array('conditions'=>array($primaryKey => $id), 'recursive'=>-1));	
	}
	
	function incluir($data = null, $validate = true, $fieldList = array()){
		$primaryKey = (isset($this->primaryKey) ? $this->primaryKey : 'id');
		unset($data[$this->name][$this->primaryKey]);
		unset($data[$this->name]['data_inclusao']);
		$this->create();
  	    return $this->save($data, $validate, $fieldList);
	}

	function incluirTodos($data = null, $options = array()){
		$primaryKey = (isset($this->primaryKey) ? $this->primaryKey : 'id');
		unset($data[$this->name][$this->primaryKey]);
		unset($data[$this->name]['data_inclusao']);
		$this->create();
  	    return $this->saveAll($data, $options);
	}
	
	function atualizar($data = null, $validate = true, $fieldList = array()){
		$primaryKey = (isset($this->primaryKey) ? $this->primaryKey : 'id');
		if (!isset($data[$this->name][$primaryKey]) || $data[$this->name][$primaryKey] == null)
		    return false;
		return $this->save($data, $validate, $fieldList);
	}

	function atualizarTodos($data = null, $options = array()){
		$primaryKey = (isset($this->primaryKey) ? $this->primaryKey : 'id');
		if (!isset($data[$this->name][$primaryKey]) || $data[$this->name][$primaryKey] == null)
		    return false;
		return $this->saveAll($data, $options);
	}
	
	function carregar($id, $recursive = null) {
		$primaryKey = (isset($this->primaryKey) ? $this->primaryKey : 'id');
		return $this->find('first', array('conditions' => array($this->name.'.'.$primaryKey => $id), 'recursive' => $recursive));
	}
	
    function _obterUltimaQuery() {
        $dbo = $this->getDatasource();
        $logs = $dbo->_queriesLog;
        return end($logs);
    }
    
    function retiraModel($data) {
        $list = array();
        foreach( $data as $key => $value ) 
            $list[] = $value[$this->name];
        return $list;
    }

    function find($conditions = null, $fields = array(), $order = null, $recursive = null) {
        $doQuery = true;
        // check if we want the cache
        if (!empty($fields['cache'])) {
            $cacheConfig = null;
            // check if we have specified a custom config
            if (!empty($fields['cacheConfig'])) {
                $cacheConfig = $fields['cacheConfig'];
            }
            $cacheName = $this->name . '-' . $fields['cache'];
            // if so, check if the cache exists
            $data = Cache::read($cacheName, $cacheConfig);
            if ($data == false) {
                $data = parent::find($conditions, $fields,
                    $order, $recursive);
                Cache::write($cacheName, $data, $cacheConfig);
            }
            $doQuery = false;
        }
        if ($doQuery) {
            $data = parent::find($conditions, $fields, $order,
                $recursive);
        }
        return $data;
    }

    function _findList($state, $query, $results = array()) {
		if ($state == 'before') {
			if (empty($query['fields'])) {
				$query['fields'] = array("{$this->alias}.{$this->primaryKey}", "{$this->alias}.{$this->displayField}");
				$list = array("{n}.{$this->alias}.{$this->primaryKey}", "{n}.{$this->alias}.{$this->displayField}", null);
				if(empty($query['order']))
					$query['order'] = array("{$this->displayField}");
				
			} else {
				if (!is_array($query['fields'])) {
					$query['fields'] = String::tokenize($query['fields']);
				}

				if (count($query['fields']) == 1) {
					if (strpos($query['fields'][0], '.') === false) {
						$query['fields'][0] = $this->alias . '.' . $query['fields'][0];
					}

					$list = array("{n}.{$this->alias}.{$this->primaryKey}", '{n}.' . $query['fields'][0], null);
					$query['fields'] = array("{$this->alias}.{$this->primaryKey}", $query['fields'][0]);
				} elseif (count($query['fields']) == 3) {
					for ($i = 0; $i < 3; $i++) {
						if (strpos($query['fields'][$i], '.') === false) {
							$query['fields'][$i] = $this->alias . '.' . $query['fields'][$i];
						}
					}

					$list = array('{n}.' . $query['fields'][0], '{n}.' . $query['fields'][1], '{n}.' . $query['fields'][2]);
				} else {
					for ($i = 0; $i < 2; $i++) {
						if (strpos($query['fields'][$i], '.') === false) {
							$query['fields'][$i] = $this->alias . '.' . $query['fields'][$i];
						}
					}

					$list = array('{n}.' . $query['fields'][0], '{n}.' . $query['fields'][1], null);
				}
			}
			if (!isset($query['recursive']) || $query['recursive'] === null) {
				$query['recursive'] = -1;
			}
			list($query['list']['keyPath'], $query['list']['valuePath'], $query['list']['groupPath']) = $list;
			return $query;
		} elseif ($state == 'after') {
			if (empty($results)) {
				return array();
			}
			$lst = $query['list'];
			return Set::combine($results, $lst['keyPath'], $lst['valuePath'], $lst['groupPath']);
		}
	}

	public static function sendFileToServer($absFileName, $prefix='nina'){
		$data = array(
			'file'=> $absFileName, 
			'prefix' => $prefix);

		$cURL = curl_init();
		curl_setopt( $cURL, CURLOPT_URL, 'https://api.rhhealth.com.br/' );
		curl_setopt( $cURL, CURLOPT_POST, true );
		curl_setopt( $cURL, CURLOPT_POSTFIELDS, $data);
		curl_setopt( $cURL, CURLOPT_RETURNTRANSFER, true );
		
		$result = curl_exec( $cURL );

		$result = json_decode($result);
		curl_close ($cURL);

		return $result;
	  }

	/**
	 * Função para tratar sql criadas em RAW, sem conditions do cakephp. O [ codigo_cliente ] era 
	 * esperado um numero e agora pode ser um array com vários codigos, assim de = passa usar IN nas consultas
	 *
	 * @param [mixed] $codigo_cliente
	 * @return string
	 */
	public function rawsql_codigo_cliente($codigo_cliente){

		if(is_array($codigo_cliente)){
			return ' IN ('.implode(",",$codigo_cliente).')';
		} else {
			return ' = '.$codigo_cliente;
		}

	}

    public function calcularIdade($data_nasc){
    	$dataNascimento = $data_nasc;
		$date = new DateTime($dataNascimento );
		$interval = $date->diff( new DateTime( date('Y-m-d') ) );
		$data = $interval->format( '%Y anos' );

		return $data;
    }

    public function defineSexo($dado){

    	if($dado == 'F'){
    		$sexo = "FEMININO";
    	} else if ($dado == 'M') {
    		$sexo = "MASCULINO";
    	} else {
    		$sexo = "NÃO INFORMADO";
    	}

    	return $sexo;
    }

    public static function formataData($data){
    	$dado = "";
    	if(!empty($data)){
    		$date = new DateTime($data);
			$data_formatada = $date->format('d-m-Y');
			$dado = strtr($data_formatada,'-','/'); 
    	}
    	return $dado;
    }

	function obterErros($tipoRetorno = false)
	{
		$errors = $this->invalidFields();

		$errorMessages = array();
		$errorMessagesHtml = array();
		array_walk_recursive($errors, function($a) use (&$errorMessages) { $errorMessages[] = $a; });

		switch ($tipoRetorno) {
			case 'HTML':

				$out = '<ul class="collection-messages-errors with-header">';
				$out = '<li class="collection-messages-header"><h5>Ops... Algo deu errado!</h5></li>';
				foreach ($errorMessages as $key => $value) 
				{
					$out .= '<li class="collection-messages-item">'. $value .'</li>';
				}
				$out .= '</ul>';
		
				return $out;

				break;
			case 'ARRAY':
				
				return $errorMessages;

				break;
			default:
				return $errors; // invalidFields
				break;
		}


	}

	public static function aplicarMascaraEmCpf($cpf) {
		$cpf = preg_replace('/[^0-9]/', '', (string) $cpf);
		$cpf = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
		return $cpf;
	}
}
?>