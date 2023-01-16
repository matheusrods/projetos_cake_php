<?php
class TipoContato extends AppModel {
	var $name = 'TipoContato';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
	var $useTable = 'tipo_contato';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $displayField = 'descricao';

        /**
         * Id do TipoContato Comercial
         * Tivemos que implementar o TipoContato Comercial como padrão
         * no cadastro de Clientes.
         * 
         * Como não é possível mudar a estrutura do banco, tivemos que
         * fixar o valor como constante dentro desta Model.
         * 
         * Qualquer sugestão futura é bem vinda. :) 
         */
        const TIPO_CONTATO_RESIDENCIAL = 1;
        const TIPO_CONTATO_COMERCIAL = 2;
        const TIPO_CONTATO_FINANCEIRO = 3;
        const TIPO_CONTATO_NFE = 5;
        const TIPO_CONTATO_ENTREGA = 4;
        const TIPO_CONTATO_VALIDA_VIDAS = 9;
        
        
        /**
         * Lista todos os tipos enderecos com excessão do Comercial
         * 
         * @return Array
         */               
        public function listarExcetoComercial() {
            $result = $this->find('list', array(
                'order' => 'descricao',
                'conditions' => array(
                    'codigo !=' => TipoContato::TIPO_CONTATO_COMERCIAL
                    )));

            return $result;
        }
        function listar(){
            $order      = array('descricao');

            return $this->find('list',compact('order'));
        }
        
        public function listarParFichaScorecard(){
        	return $this->find('list', array('fields'=>array('codigo', 'descricao'), 'conditions'=>array('codigo'=>array(1, 2, 7))));
        }

        /**
         * Lista todos os tipos enderecos
         * 
         * @return Array
         */               
        public function listarOrderByCod() {
            $result = $this->find('list', array(
                'order' => 'codigo'
            )
        );

            return $result;
        }
    }
    ?>