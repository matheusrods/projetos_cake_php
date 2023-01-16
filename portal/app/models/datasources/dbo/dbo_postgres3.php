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
                $this->value($model->tableSchema)."  ORDER BY position",
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

 /**
 * The "C" in CRUD
 *
 * Creates new records in the database.
 *
 * @param Model $model Model object that the record is for.
 * @param array $fields An array of field names to insert. If null, $model->data will be
 *   used to generate field names.
 * @param array $values An array of values with keys matching the fields. If null, $model->data will
 *   be used to generate values.
 * @return boolean Success
 * @access public
 */
    function create(&$model, $fields = null, $values = null) {
        $id = null;

        if ($fields == null) {
            unset($fields, $values);
            $fields = array_keys($model->data);
            $values = array_values($model->data);
        }
        $count = count($fields);

        for ($i = 0; $i < $count; $i++) {
            $valueInsert[] = $this->value($values[$i], $model->getColumnType($fields[$i]), false);
            $fieldInsert[] = $this->name($fields[$i]);
            if ($fields[$i] == $model->primaryKey) {
                $id = $values[$i];
            }
        }
        $query = array(
            'table' => $this->fullTableName($model),
            'fields' => implode(', ', $fieldInsert),
            'values' => implode(', ', $valueInsert),
            'tableSchema' => isset($model->tableSchema) ? $model->tableSchema : null
        );

        if ($this->execute($this->renderStatement('create', $query))) {
            if (empty($id)) {
                $id = $this->lastInsertId($this->fullTableName($model, false), $model->primaryKey);
            }
            $model->setInsertID($id);
            $model->id = $id;
            return true;
        } else {
            $model->onError();
            return false;
        }
    }

 /**
 * Generates and executes an SQL UPDATE statement for given model, fields, and values.
 * For databases that do not support aliases in UPDATE queries.
 *
 * @param Model $model
 * @param array $fields
 * @param array $values
 * @param mixed $conditions
 * @return boolean Success
 * @access public
 */
    function update(&$model, $fields = array(), $values = null, $conditions = null) {
        if ($values == null) {
            $combined = $fields;
        } else {
            $combined = array_combine($fields, $values);
        }

        $fields = implode(', ', $this->_prepareUpdateFields($model, $combined, empty($conditions)));

        $alias = $joins = null;
        $table = $this->fullTableName($model);
        $conditions = $this->_matchRecords($model, $conditions);
        $tableSchema = isset($model->tableSchema) ? $model->tableSchema : null;

        if ($conditions === false) {
            return false;
        }
        $query = compact('table', 'alias', 'joins', 'fields', 'conditions', 'tableSchema');

        if (!$this->execute($this->renderStatement('update', $query))) {
            $model->onError();
            return false;
        }
        return true;
    }

 /**
 * Overrides Postgres::renderStatement to handle schema generation with Postgres-style indexes
 *
 * @param string $type
 * @param array $data
 * @return string
 */
    function renderStatement($type, $data) {
        extract($data);
        $aliases = null;

        switch (strtolower($type)) {
            case 'select':
                return "SELECT {$fields} FROM ".(isset($tableSchema) ? "{$tableSchema}." : "")."{$table} {$alias} {$joins} {$conditions} {$group} {$order} {$limit}";
            break;
            case 'create':
                return "INSERT INTO ".(isset($tableSchema) ? "{$tableSchema}." : "")."{$table} ({$fields}) VALUES ({$values})";
            break;
            case 'update':
                if (!empty($alias)) {
                    $aliases = "{$this->alias}{$alias} {$joins} ";
                }
                return "UPDATE ".(isset($tableSchema) ? "{$tableSchema}." : "")."{$table} {$aliases}SET {$fields} {$conditions}";
            break;
            case 'delete':
                if (!empty($alias)) {
                    $aliases = "{$this->alias}{$alias} {$joins} ";
                }
                return "DELETE {$alias} FROM ".(isset($tableSchema) ? "{$tableSchema}." : "")."{$table} {$aliases}{$conditions}";
            break;
            default:
                return parent::renderStatement($type, $data);
            break;
        }
    }

 /**
 * Generates and executes an SQL DELETE statement.
 * For databases that do not support aliases in UPDATE queries.
 *
 * @param Model $model
 * @param mixed $conditions
 * @return boolean Success
 * @access public
 */
    function delete(&$model, $conditions = null) {
        $alias = $joins = null;
        $table = $this->fullTableName($model);
        $conditions = $this->_matchRecords($model, $conditions);
        $tableSchema = isset($model->tableSchema) ? $model->tableSchema : null;

        if ($conditions === false) {
            return false;
        }

        if ($this->execute($this->renderStatement('delete', compact('alias', 'table', 'joins', 'conditions', 'tableSchema'))) === false) {
            $model->onError();
            return false;
        }
        return true;
    }

/**
 * Generates an array representing a query or part of a query from a single model or two associated models
 *
 * @param Model $model
 * @param Model $linkModel
 * @param string $type
 * @param string $association
 * @param array $assocData
 * @param array $queryData
 * @param boolean $external
 * @param array $resultSet
 * @return mixed
 * @access public
 */
    function generateAssociationQuery(&$model, &$linkModel, $type, $association = null, $assocData = array(), &$queryData, $external = false, &$resultSet) {
        $queryData = $this->__scrubQueryData($queryData);
        $assocData = $this->__scrubQueryData($assocData);

        if (empty($queryData['fields'])) {
            $queryData['fields'] = $this->fields($model, $model->alias);
        } elseif (!empty($model->hasMany) && $model->recursive > -1) {
            $assocFields = $this->fields($model, $model->alias, array("{$model->alias}.{$model->primaryKey}"));
            $passedFields = $this->fields($model, $model->alias, $queryData['fields']);
            if (count($passedFields) === 1) {
                $match = strpos($passedFields[0], $assocFields[0]);
                $match1 = (bool)preg_match('/^[a-z]+\(/i', $passedFields[0]);

                if ($match === false && $match1 === false) {
                    $queryData['fields'] = array_merge($passedFields, $assocFields);
                } else {
                    $queryData['fields'] = $passedFields;
                }
            } else {
                $queryData['fields'] = array_merge($passedFields, $assocFields);
            }
            unset($assocFields, $passedFields);
        }

        if ($linkModel == null) {
            return $this->buildStatement(
                array(
                    'fields' => array_unique($queryData['fields']),
                    'table' => $this->fullTableName($model),
                    'alias' => $model->alias,
                    'limit' => $queryData['limit'],
                    'offset' => $queryData['offset'],
                    'joins' => $queryData['joins'],
                    'conditions' => $queryData['conditions'],
                    'order' => $queryData['order'],
                    'group' => $queryData['group'],
                    'tableSchema' => (isset($model->tableSchema) ? $model->tableSchema : null)
                ),
                $model
            );
        }
        if ($external && !empty($assocData['finderQuery'])) {
            return $assocData['finderQuery'];
        }

        $alias = $association;
        $self = ($model->name == $linkModel->name);
        $fields = array();

        if ((!$external && in_array($type, array('hasOne', 'belongsTo')) && $this->__bypass === false) || $external) {
            $fields = $this->fields($linkModel, $alias, $assocData['fields']);
        }
        if (empty($assocData['offset']) && !empty($assocData['page'])) {
            $assocData['offset'] = ($assocData['page'] - 1) * $assocData['limit'];
        }
        $assocData['limit'] = $this->limit($assocData['limit'], $assocData['offset']);

        switch ($type) {
            case 'hasOne':
            case 'belongsTo':
                $conditions = $this->__mergeConditions(
                    $assocData['conditions'],
                    $this->getConstraint($type, $model, $linkModel, $alias, array_merge($assocData, compact('external', 'self')))
                );

                if (!$self && $external) {
                    foreach ($conditions as $key => $condition) {
                        if (is_numeric($key) && strpos($condition, $model->alias . '.') !== false) {
                            unset($conditions[$key]);
                        }
                    }
                }

                if ($external) {
                    $query = array_merge($assocData, array(
                        'conditions' => $conditions,
                        'table' => $this->fullTableName($linkModel),
                        'fields' => $fields,
                        'alias' => $alias,
                        'group' => null,
                        'tableSchema' => isset($linkModel->tableSchema) ? $linkModel->tableSchema : null
                    ));
                    $query = array_merge(array('order' => $assocData['order'], 'limit' => $assocData['limit']), $query);
                } else {
                    $join = array(
                        'table' => $this->fullTableName($linkModel),
                        'alias' => $alias,
                        'type' => isset($assocData['type']) ? $assocData['type'] : 'LEFT',
                        'conditions' => trim($this->conditions($conditions, true, false, $model)),
                        'tableSchema' => isset($linkModel->tableSchema) ? $linkModel->tableSchema : null,
                    );
                    $queryData['fields'] = array_merge($queryData['fields'], $fields);

                    if (!empty($assocData['order'])) {
                        $queryData['order'][] = $assocData['order'];
                    }
                    if (!in_array($join, $queryData['joins'])) {
                        $queryData['joins'][] = $join;
                    }
                    return true;
                }
            break;
            case 'hasMany':
                $assocData['fields'] = $this->fields($linkModel, $alias, $assocData['fields']);
                if (!empty($assocData['foreignKey'])) {
                    $assocData['fields'] = array_merge($assocData['fields'], $this->fields($linkModel, $alias, array("{$alias}.{$assocData['foreignKey']}")));
                }
                $query = array(
                    'conditions' => $this->__mergeConditions($this->getConstraint('hasMany', $model, $linkModel, $alias, $assocData), $assocData['conditions']),
                    'fields' => array_unique($assocData['fields']),
                    'table' => $this->fullTableName($linkModel),
                    'alias' => $alias,
                    'order' => $assocData['order'],
                    'limit' => $assocData['limit'],
                    'group' => null,
                    'tableSchema' => isset($linkModel->tableSchema) ? $linkModel->tableSchema : null
                );
            break;
            case 'hasAndBelongsToMany':
                $joinFields = array();
                $joinAssoc = null;

                if (isset($assocData['with']) && !empty($assocData['with'])) {
                    $joinKeys = array($assocData['foreignKey'], $assocData['associationForeignKey']);
                    list($with, $joinFields) = $model->joinModel($assocData['with'], $joinKeys);

                    $joinTbl = $this->fullTableName($model->{$with});
                    $joinTblSchema = isset($model->{$with}->tableSchema) ? $model->{$with}->tableSchema : null;
                    $joinAlias = $joinTbl;

                    if (is_array($joinFields) && !empty($joinFields)) {
                        $joinFields = $this->fields($model->{$with}, $model->{$with}->alias, $joinFields);
                        $joinAssoc = $joinAlias = $model->{$with}->alias;
                    } else {
                        $joinFields = array();
                    }
                } else {
                    $joinTbl = $this->fullTableName($assocData['joinTable']);
                    $joinAlias = $joinTbl;
                }
                $query = array(
                    'conditions' => $assocData['conditions'],
                    'limit' => $assocData['limit'],
                    'table' => $this->fullTableName($linkModel),
                    'alias' => $alias,
                    'fields' => array_merge($this->fields($linkModel, $alias, $assocData['fields']), $joinFields),
                    'order' => $assocData['order'],
                    'group' => null,
                    'tableSchema' => isset($linkModel->tableSchema) ? $linkModel->tableSchema : null,
                    'joins' => array(array(
                        'table' => $joinTbl,
                        'tableSchema' => $joinTblSchema,
                        'alias' => $joinAssoc,
                        'conditions' => $this->getConstraint('hasAndBelongsToMany', $model, $linkModel, $joinAlias, $assocData, $alias)
                    ))
                );
            break;
        }
        if (isset($query)) {
            return $this->buildStatement($query, $model);
        }
        return null;
    }

 /**
 * Renders a final SQL JOIN statement
 *
 * @param array $data
 * @return string
 * @access public
 */
    function renderJoinStatement($data) {
        extract($data);
        if ( strpos($this->config['database'], 'test') > 0) {
            return trim("{$type} JOIN ".
                    "{$table} ".
                    "{$alias} ON ({$conditions})");
        } else {
            return trim("{$type} JOIN ".
                    (isset($tableSchema) ? "{$tableSchema}." : "").
                    "{$table} ".
                    "{$alias} ON ({$conditions})");
        }
    }

 /**
 * Builds and generates an SQL statement from an array.  Handles final clean-up before conversion.
 *
 * @param array $query An array defining an SQL query
 * @param object $model The model object which initiated the query
 * @return string An executable SQL statement
 * @access public
 * @see DboSource::renderStatement()
 */
    function buildStatement($query, &$model) {
        $query = array_merge(array('offset' => null, 'joins' => array()), $query);
        if (!empty($query['joins'])) {
            $count = count($query['joins']);
            for ($i = 0; $i < $count; $i++) {
                if (is_array($query['joins'][$i])) {
                    $query['joins'][$i] = $this->buildJoinStatement($query['joins'][$i]);
                }
            }
        }
        if (!isset($query['conditions'])) $query['conditions'] = null;
        if (!isset($query['order'])) $query['order'] = null;
        if (!isset($query['limit'])) $query['limit'] = null;
        if (!isset($query['offset'])) $query['offset'] = null;
        if (!isset($query['joins'])) $query['joins'] = null;
        if (!isset($query['group'])) $query['group'] = null;
        return $this->renderStatement('select', array(
            'conditions' => $this->conditions($query['conditions'], true, true, $model),
            'fields' => implode(', ', $query['fields']),
            'table' => $query['table'],
            'alias' => $this->alias . $this->name($query['alias']),
            'order' => $this->order($query['order'], 'ASC', $model),
            'limit' => $this->limit($query['limit'], $query['offset']),
            'joins' => (!empty($query['joins']) ? implode(' ', $query['joins']) : null),
            'group' => $this->group($query['group'], $model),
            'tableSchema' => isset($query['tableSchema']) ? $query['tableSchema'] : null
        ));
    }
}
