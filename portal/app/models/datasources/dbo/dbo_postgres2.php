<?php
/**
 * PostgreSQL layer for DBO.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.model.datasources.dbo
 * @since         CakePHP(tm) v 0.9.1.114
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * PostgreSQL layer for DBO.
 *
 * Long description for class
 *
 * @package       cake
 * @subpackage    cake.cake.libs.model.datasources.dbo
 */
App::import('core', 'DboPostgres');
class DboPostgres2 extends DboPostgres {
	function read(&$model, $queryData = array(), $recursive = null) { 
        // only handle queries for the SQL 
        if ( empty($queryData['returnSQL']) ){ 
            return parent::read($model, $queryData, $recursive); 
        } 

        // the following is just copied from the /cake/libs/model/datasources/dbo_source.php read function 
        $queryData = $this->__scrubQueryData($queryData); 
        
        $null = null; 
        $array = array('callbacks' => $queryData['callbacks']);
        $linkedModels = array(); 
        $this->__bypass = false; 
        $this->__booleans = array(); 

        if ($recursive === null && isset($queryData['recursive'])) { 
            $recursive = $queryData['recursive']; 
        } 

        if (!is_null($recursive)) { 
            $_recursive = $model->recursive; 
            $model->recursive = $recursive; 
        } 

        if (!empty($queryData['fields'])) { 
            $this->__bypass = true; 
            $queryData['fields'] = $this->fields($model, null, $queryData['fields']); 
        } else { 
            $queryData['fields'] = $this->fields($model); 
        } 

        $_associations = $model->__associations;

		if ($model->recursive == -1) {
			$_associations = array();
		} else if ($model->recursive == 0) {
			unset($_associations[2], $_associations[3]);
		}

        foreach ($_associations as $type) {
			foreach ($model->{$type} as $assoc => $assocData) {
				$linkModel =& $model->{$assoc};
				$external = isset($assocData['external']);

				if ($model->useDbConfig == $linkModel->useDbConfig) {
					if (true === $this->generateAssociationQuery($model, $linkModel, $type, $assoc, $assocData, $queryData, $external, $null)) {
						$linkedModels[$type . '/' . $assoc] = true;
					}
				}
			}
		}

        $query = $this->generateAssociationQuery($model, $null, null, null, null, $queryData, false, $null); 

        // restore the recursive level 
        if (!is_null($recursive)) { 
            $model->recursive = $_recursive; 
        } 

        // but return this query instead of fetching it 
        return $query; 
    } 

    /**
 * Executes given SQL statement.
 *
 * @param string $sql SQL statement
 * @return resource Result resource identifier
 */
    function _execute($sql) {
        $return = pg_query($this->connection, $sql);
        if ($return === false) {
            $debugs = debug_backtrace();
            foreach($debugs AS $debug) {
                if (isset($debug['file']) && isset($debug['line'])) {
                    $this->log($debug['file'].'('.$debug['line'].')');
                }
            }
        }
        return $return;
    }

/**
 * Converts database-layer column types to basic types
 * Função extendida para realizar o tratamento de campos do tipo BIT
 * @param string $real Real database-layer column type (i.e. "varchar(255)")
 * @return string Abstract column type (i.e. "string")
 */
    function column($real) {
        if (is_array($real)) {
            $col = $real['name'];
            if (isset($real['limit'])) {
                $col .= '(' . $real['limit'] . ')';
            }
            return $col;
        }

        $col = str_replace(')', '', $real);
        $limit = null;

        if (strpos($col, '(') !== false) {
            list($col, $limit) = explode('(', $col);
        }

        $floats = array(
            'float', 'float4', 'float8', 'double', 'double precision', 'decimal', 'real', 'numeric'
        );

        switch (true) {
            case (in_array($col, array('date', 'time', 'inet', 'boolean','bit'))):
                return $col;
            case (strpos($col, 'timestamp') !== false):
                return 'datetime';
            case (strpos($col, 'time') === 0):
                return 'time';
            case (strpos($col, 'int') !== false && $col != 'interval'):
                return 'integer';
            case (strpos($col, 'char') !== false || $col == 'uuid'):
                return 'string';
            case (strpos($col, 'text') !== false):
                return 'text';
            case (strpos($col, 'bytea') !== false):
                return 'binary';
            case (in_array($col, $floats)):
                return 'float';
            default:
                return 'text';
            break;
        }
    }
/**
 * Returns an array of the fields in given table name.
 * Função extendida para realizar o tratamento dos campos do tipo BIT
 * @param string $tableName Name of database table to inspect
 * @return array Fields in table. Keys are name and type
 */
    function &describe(&$model) {
        $fields = DboSource::describe($model);
        $table = $this->fullTableName($model, false);
        $this->_sequenceMap[$table] = array();

        if ($fields === null) {
            $cols = $this->fetchAll(
                "SELECT DISTINCT column_name AS name, data_type AS type, is_nullable AS null,
                    column_default AS default, ordinal_position AS position, character_maximum_length AS char_length,
                    character_octet_length AS oct_length FROM information_schema.columns
                WHERE table_name = " . $this->value($table) . " AND table_schema = " .
                $this->value($this->config['schema'])."  ORDER BY position",
                false
            );

            foreach ($cols as $column) {
                $colKey = array_keys($column);

                if (isset($column[$colKey[0]]) && !isset($column[0])) {
                    $column[0] = $column[$colKey[0]];
                }

                if (isset($column[0])) {
                    $c = $column[0];

                    if (!empty($c['char_length'])) {
                        $length = intval($c['char_length']);
                    } elseif (!empty($c['oct_length'])) {
                        if ($c['type'] == 'character varying') {
                            $length = null;
                            $c['type'] = 'text';
                        } else {
                            $length = intval($c['oct_length']);
                        }
                    } else {
                        $length = $this->length($c['type']);
                    }
                    $fields[$c['name']] = array(
                        'type'    => $this->column($c['type']),
                        'null'    => ($c['null'] == 'NO' ? false : true),
                        'default' => preg_replace(
                            "/^'(.*)'$/",
                            "$1",
                            preg_replace('/::.*/', '', $c['default'])
                        ),
                        'length'  => $length
                    );
                    if ($c['name'] == $model->primaryKey) {
                        $fields[$c['name']]['key'] = 'primary';
                        if ($fields[$c['name']]['type'] !== 'string') {
                            $fields[$c['name']]['length'] = 11;
                        }
                    }
                    if (
                        $fields[$c['name']]['default'] == 'NULL' ||
                        preg_match('/nextval\([\'"]?([\w.]+)/', $c['default'], $seq)
                    ) {
                        $fields[$c['name']]['default'] = null;
                        if (!empty($seq) && isset($seq[1])) {
                            $this->_sequenceMap[$table][$c['name']] = $seq[1];
                        }
                    }
                    if ($fields[$c['name']]['type'] == 'bit' && !empty($fields[$c['name']]['default'])) {
                        //$fields[$c['name']]['default'] = constant($fields[$c['name']]['default']);
                        $fields[$c['name']]['default'] = str_replace('B','',$fields[$c['name']]['default']);
                        $fields[$c['name']]['default'] = str_replace("'",'',$fields[$c['name']]['default']);
                    }
                    if ($fields[$c['name']]['type'] == 'boolean' && !empty($fields[$c['name']]['default'])) {
                        $fields[$c['name']]['default'] = constant($fields[$c['name']]['default']);
                    }
                }
            }
            //debug($fields);
            $this->__cacheDescription($table, $fields);
        }
        if (isset($model->sequence)) {
            $this->_sequenceMap[$table][$model->primaryKey] = $model->sequence;
        }
        return $fields;
    }    
}
