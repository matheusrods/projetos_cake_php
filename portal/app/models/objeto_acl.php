<?php
class ObjetoAcl extends AppModel {
    var $name = 'ObjetoAcl';
    var $useDbConfig = 'dbProducao';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'objetos_acl';
    var $displayField = 'descricao';
    var $actsAs = array('Secure');
    var $validate = array(
        'descricao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a descrição do objeto',
        ),
        /*
        'codigo_tarefa_desenvolvimento' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe uma tarefa',
        ),
        */
    );
        
    CONST MODULO_ADMIN_CLIENTE = 590;
    CONST BLOQUEAR_ALTERACAO_ALVO_ORIGEM = 641;
    CONST CANCELAR_SM = 380;

    function opcoesParaSelecao($cliente_admin = null) {
        $conditions = array('aco_string !=' => '');
        if ($cliente_admin)
            //Não seleciona os perfis ADMIN, Perfil, Usuários e Administrativo
            $conditions = array(
          /*      "OR" => array(
                    "PATINDEX('obj_%',aco_string) <= " => 0,
                    'id' => self::CANCELAR_SM
                ),  */              
                "PATINDEX('obj_%',aco_string) <= " => 0,
                'aco_string !=' => '',
                'NOT' => array('id' => array(2,14,16,1128))
            );
        if(Ambiente::getServidor() ==  Ambiente::SERVIDOR_PRODUCAO){
            $conditions['homologado'] = 1;
        }

        return $this->find('all', compact('conditions'));
    }

    function listaObjetos($codigo_tipo_perfil,$permitidos = null, $perfil = null, $admin = null) {
        $this->ObjetoAclTipoPerfil = classRegistry::init('ObjetoAclTipoPerfil');
        $conditions = array();
        if($permitidos) {
             $dados = $this->recursiveParent($permitidos);
             $conditions = array('ObjetoAcl.id' => $dados);
        }
        if($perfil && Ambiente::getServidor() ==  Ambiente::SERVIDOR_PRODUCAO){
            $conditions['homologado'] = 1;
            
        }
       	if(!$admin){       
	       $this->bindModel(array(
	            'belongsTo' => array(
	                'ObjetoAclTipoPerfil' => array(
	                    'foreignKey' => false,
	                    'conditions' => "ObjetoAcl.id = ObjetoAclTipoPerfil.objeto_id"
	                )
	            )
	        ));

	        $conditions[] = array('OR' => array(
	            array('ObjetoAclTipoPerfil.codigo_tipo_perfil' => $codigo_tipo_perfil),
	            array('ObjetoAclTipoPerfil.codigo_tipo_perfil' => NULL),
	            )
	        );
	    }
        return $this->find('threaded', array('order' => $this->name.'.descricao', 'conditions' => $conditions));
    }

    function recursiveParent($permitidos = 1) {
        $outro = array();

        foreach($permitidos as $permitido) {
            $id = array();
            while(true) {
                if(!empty($id))
                    $permitido = end($id);
                else 
                    $id[] = $permitido;

                if($permitido < 1) {
                    break;
                }

                $sql = "select parent_id from {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} where id = '{$permitido}'";

                $dados = $this->query($sql);
                if (!empty($dados)) {
                    $id[] = $dados[0][0]['parent_id'];
                }
            }
            $outro = array_unique(array_merge($outro,$id));
        }
        return $outro;
    }

    function getParents($objeto, &$parents = array()){
        if($objeto['ObjetoAcl']['parent_id'] != NULL && $objeto['ObjetoAcl']['parent_id'] != $objeto['ObjetoAcl']['id']){
            $parent = $this->carregar($objeto['ObjetoAcl']['parent_id']);
            $parents[] = $parent;
            return $this->getParents($parent,$parents);
        }
        return $parents;
    }

    function getChildrens($objeto, &$childrens = array()){
        $childrens2 = $this->find('all', array('conditions' => array('parent_id' => $objeto['ObjetoAcl']['id'])));
        $childrens = array_merge($childrens,$childrens2);
        if(!empty($childrens2)){
            foreach($childrens2 as $child){
                $this->getChildrens($child,$childrens);
            }
        }
        return $childrens;
    }

    function bindObjetoAclTiposPerfis(){
        $this->bindModel(array(
            'hasMany' => array(
                'ObjetoAclTiposPerfis'  => array(
                    'foreignKey' => 'objeto_id',
                    'type' => 'LEFT'
                ),
            ),
        ));

        $consulta = $this->find('all');
        return $consulta;
    }

    function incluir($dados){
        $this->ObjetoAclTipoPerfil = ClassRegistry::init('ObjetoAclTipoPerfil');
        $this->TipoPerfil = ClassRegistry::init('TipoPerfil');
        try {
            $this->query('begin transaction');
            if (!parent::incluir($dados)){
                throw new Exception();
            } 
            $objetoId = $this->getLastInsertId(); 
            if(!empty($dados['ObjetoAcl']['codigo_tipo_perfil'])){
                foreach ($dados['ObjetoAcl']['codigo_tipo_perfil'] as $objetoTipoPerfil['codigo_tipo_perfil']) {                
                    $objetoTipoPerfil['objeto_id'] = $objetoId;
                    $objetoTipoPerfil['codigo_tipo_perfil'];
                    if(!$this->ObjetoAclTipoPerfil->incluir($objetoTipoPerfil)){
                        throw new Exception();
                    } 
                }
            }
            $this->commit();
            return TRUE;
        } catch (Exception $e) {
            $this->rollback();
            return FALSE;
        }
    }

    function excluir($id){
        $this->ObjetoAclTipoPerfil = ClassRegistry::init('ObjetoAclTipoPerfil');
        try {
            $this->query('begin transaction');
            $listaFilhos = $this->ObjetoAclTipoPerfil->listaPerfilPermitido($id);
            foreach ($listaFilhos as $filho) {
                if(!$this->ObjetoAclTipoPerfil->excluir($filho['ObjetoAclTipoPerfil']['id'])){
                    throw new Exception();
                }
            }
            if(!parent::excluir($id)){
                throw new Exception();
            }
            $this->commit();
            return TRUE;
        }catch (Exception $e) {
            $this->rollback();
            return FALSE;
        }    
    }

    function atualizar($dados){
        $this->ObjetoAclTipoPerfil = ClassRegistry::init('ObjetoAclTipoPerfil');
        try {
            $this->query('begin transaction');
            $id = $dados['ObjetoAcl']['id'];
            $perfilPermitidoAntigo = array();
            $listaPerfilPermitido = $this->ObjetoAclTipoPerfil->listaPerfilPermitido($id);
            foreach ($dados as $perfilNovo) {
                $perfilPermitidoNovo = $perfilNovo['codigo_tipo_perfil'];
            }

            foreach ($listaPerfilPermitido as $PerfilAntigo) {
                $perfilPermitidoAntigo[] = $PerfilAntigo['ObjetoAclTipoPerfil']['codigo_tipo_perfil'];
            }
            
            if(!parent::atualizar($dados)){
                throw new Exception();
            }

            if(empty($perfilPermitidoNovo)){
                $listaPerfil = $this->ObjetoAclTipoPerfil->listaPerfilPermitido($id);
                foreach ($listaPerfil as $lista) {
                    if(!$this->ObjetoAclTipoPerfil->excluir($lista['ObjetoAclTipoPerfil']['id'])){
                        throw new Exception();
                    }
                }
            }else{
                $ExcluirPerfilAntigo = array_diff($perfilPermitidoAntigo, $perfilPermitidoNovo);
                $incluirPerfilNovo = array_diff($perfilPermitidoNovo, $perfilPermitidoAntigo);
                foreach ($ExcluirPerfilAntigo as $perfil) {
                    $listaPerfilPermitido = $this->ObjetoAclTipoPerfil->listaPerfilPermitido($dados['ObjetoAcl']['id'],$perfil);
                    $idPerfilExclusao = $listaPerfilPermitido[0]['ObjetoAclTipoPerfil']['id'];
                    if(!$this->ObjetoAclTipoPerfil->excluir($idPerfilExclusao)){
                        throw new Exception();
                    }
                }
                foreach ($incluirPerfilNovo as $perfil) {
                    $dados['ObjetoAcl']['codigo_tipo_perfil'] = $perfil;
                    $dados['ObjetoAcl']['objeto_id'] = $id;
                    unset($dados['ObjetoAcl']['id']);
                    if(!$this->ObjetoAclTipoPerfil->incluir($dados['ObjetoAcl'])){
                        throw new Exception();
                    }
                }
            }    
            $this->commit();
            return TRUE;
        } catch (Exception $e) {
            $this->rollback();
            return FALSE;
        }
    }
}
