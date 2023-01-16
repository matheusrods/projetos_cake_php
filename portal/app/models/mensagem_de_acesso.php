<?php

class MensagemDeAcesso extends AppModel {

    var $name 			= 'MensagemDeAcesso';
    var $tableSchema 	= 'dbo';
    var $databaseTable 	= 'RHHealth';
    var $useTable 		= 'mensagens_de_acesso';
    var $primaryKey 	= 'codigo';
    
    var $actsAs         = array('Secure');
    var $validate = array(
		'data_inicial' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a data inicial!'              
            ),              
        ),
        'data_final' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a data final!'              
            ),              
        ),
        'titulo' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o título!'
            ),               
        ),
        'mensagem' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a mensagem!'
            )
        ),                
    );     

    public function bindPerfilModulo(){
        $this->bindModel(
            array(
                'belongsTo' => array(        
                    'MensagemDeAcessoPerfil' => array(
                        'class'      => 'MensagemDeAcessoPerfil',
                        'foreignKey' => false,
                        'conditions' => array('MensagemDeAcesso.codigo = MensagemDeAcessoPerfil.codigo_mensagens_de_acesso'),
                        'type'       => 'LEFT'
                    ),
                    'MensagemDeAcessoModulo' => array(
                        'class'      => 'MensagemDeAcessoModulo',
                        'foreignKey' => false,
                        'conditions' => array('MensagemDeAcesso.codigo = MensagemDeAcessoModulo.codigo_mensagens_de_acesso'),
                        'type'       => 'LEFT'
                    ),            
                )
            )
        );
    }


    public function incluir($dados){
        $this->MensagemDeAcessoPerfil = ClassRegistry::init('MensagemDeAcessoPerfil');
        $this->MensagemDeAcessoModulo = ClassRegistry::init('MensagemDeAcessoModulo');
        $this->query('begin transaction');
        try{
            
            $perfis = empty($dados['MensagemDeAcessoPerfil']['codigo_tipos_perfis']) ? array() : $dados['MensagemDeAcessoPerfil']['codigo_tipos_perfis'];
            $modulos = empty($dados['MensagemDeAcessoModulo']['codigo_modulo']) ? array() : $dados['MensagemDeAcessoModulo']['codigo_modulo'];

            unset($dados['MensagemDeAcessoPerfil']);
            unset($dados['MensagemDeAcessoModulo']);

            if(parent::incluir($dados)){                  
                if(!$this->MensagemDeAcessoPerfil->incluirMultiplo($this->id, $perfis, true )){
                    throw new Exception("Error Processing Request", 1);
                }
                if(!$this->MensagemDeAcessoModulo->incluirMultiplo($this->id, $modulos, true )){
                    throw new Exception("Error Processing Request", 1);
                }
            }else{
                throw new Exception("Error Processing Request", 1);
            }
            $this->commit();
            return true;
        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }
    public function atualizar($dados){
        $this->MensagemDeAcessoPerfil = ClassRegistry::init('MensagemDeAcessoPerfil');
        $this->MensagemDeAcessoModulo = ClassRegistry::init('MensagemDeAcessoModulo');
        $this->query('begin transaction');
        try{        
            $perfis = empty($dados['MensagemDeAcessoPerfil']['codigo_tipos_perfis']) ? array() : $dados['MensagemDeAcessoPerfil']['codigo_tipos_perfis'];
            $modulos = empty($dados['MensagemDeAcessoModulo']['codigo_modulo']) ? array() : $dados['MensagemDeAcessoModulo']['codigo_modulo'];

            unset($dados['MensagemDeAcessoPerfil']);
            unset($dados['MensagemDeAcessoModulo']);

            if(parent::atualizar($dados)){                  
                if(!$this->MensagemDeAcessoPerfil->atualizarMultiplo($this->id, $perfis, true )){
                    throw new Exception("Error Processing Request", 1);
                }
                if(!$this->MensagemDeAcessoModulo->atualizarMultiplo($this->id, $modulos, true )){
                    throw new Exception("Error Processing Request", 1);
                }
            }else{                
                throw new Exception("Error Processing Request", 1);
            }
            $this->commit();
            return true;
        } catch (Exception $ex) { 

            $this->rollback();
            return false;
        }
    }
    public function excluir($codigo_mensagem){
        $this->MensagemDeAcessoPerfil = ClassRegistry::init('MensagemDeAcessoPerfil');
        $this->MensagemDeAcessoModulo = ClassRegistry::init('MensagemDeAcessoModulo');
        $this->query('begin transaction');
        try{
            if(!$this->MensagemDeAcessoPerfil->deletarPorMensagem($codigo_mensagem))
                throw new Exception("Error Processing Request", 1);
            if(!$this->MensagemDeAcessoModulo->deletarPorMensagem($codigo_mensagem))
                throw new Exception("Error Processing Request", 1);
            if(!parent::excluir($codigo_mensagem)){
                throw new Exception("Error Processing Request", 1);
            }
            $this->commit();
            return true;
        } catch (Exception $ex) {
            $this->rollback();
            return false;
        }
    }
    public function converterFiltrosEmConditions($filtro){

        $conditions = array();

        if( isset($filtro['MensagemDeAcesso']['data_inicial']) && !empty($filtro['MensagemDeAcesso']['data_inicial']) &&
            isset($filtro['MensagemDeAcesso']['data_final']) && !empty($filtro['MensagemDeAcesso']['data_final']) 
            ){
            $conditions['MensagemDeAcesso.data_final BETWEEN ? AND ?'] = array(
                AppModel::dateToDbDate($filtro['MensagemDeAcesso']['data_inicial']).' 00:00:00',
                AppModel::dateToDbDate($filtro['MensagemDeAcesso']['data_final']).' 23:59:59',
            );
        }

        if( isset($filtro['MensagemDeAcesso']['titulo']) && !empty($filtro['MensagemDeAcesso']['titulo']) )
            $conditions['MensagemDeAcesso.titulo LIKE'] = "%" . $filtro['MensagemDeAcesso']['titulo'] . "%";

        return $conditions;
    }

    public function findByCodigo($codigo){
        $this->MensagemDeAcessoPerfil = ClassRegistry::init('MensagemDeAcessoPerfil');
        $this->MensagemDeAcessoModulo = ClassRegistry::init('MensagemDeAcessoModulo');
        $mensagem_acesso = parent::findByCodigo($codigo);
        if($mensagem_acesso){
            $mensagem_acesso['MensagemDeAcessoPerfil']['codigo_tipos_perfis'] = $this->MensagemDeAcessoPerfil->findByMensagem($codigo);
            $mensagem_acesso['MensagemDeAcessoModulo']['codigo_modulo'] = $this->MensagemDeAcessoModulo->findByMensagem($codigo);
        }
        return $mensagem_acesso;
    }

    public function listagem( $conditions=array() ){
        $fields = array(
                'MensagemDeAcesso.codigo',
                'MensagemDeAcesso.codigo_usuario_inclusao',
                'MensagemDeAcesso.data_inicial',
                'MensagemDeAcesso.data_final',
                'MensagemDeAcesso.data_inclusao',
                'MensagemDeAcesso.titulo',
                'MensagemDeAcesso.mensagem'
            );
        return $this->find('all', array( 
                'conditions' => $conditions, 
                'order' => 'MensagemDeAcesso.data_inclusao DESC', 
                'fields' => $fields 
            )
        );        
    }

    public function listarMensagensNoPeriodo( $modulo, $perfil, $data = null ){
        if( is_null($data) )
            $data = date('d/m/Y');
        $data = AppModel::dateToDbDate($data).' 00:00:00';

        $this->bindPerfilModulo();
        $fields = array(
            'MensagemDeAcesso.codigo',
            'MensagemDeAcesso.codigo_usuario_inclusao',
            'MensagemDeAcesso.data_inicial',
            'MensagemDeAcesso.data_final',
            'MensagemDeAcesso.data_inclusao',
            'MensagemDeAcesso.titulo',
            'MensagemDeAcesso.mensagem'
        );
        $conditions = array(
            'MensagemDeAcesso.data_inicial <= ' => $data,
            'MensagemDeAcesso.data_final   >= ' => $data,
            '(MensagemDeAcessoPerfil.codigo IS NULL OR MensagemDeAcessoPerfil.codigo_tipos_perfis = '.$perfil.')',
            '(MensagemDeAcessoModulo.codigo IS NULL OR MensagemDeAcessoModulo.codigo_modulo = '.$modulo.')'
        );
        //die(debug($this->find('sql', array( 'conditions' => $conditions, 'fields' => $fields ))));
        $retorno = $this->find('all', array( 'conditions' => $conditions, 'fields' => $fields ));
        //die(debug($retorno[1]['MensagemDeAcesso']['mensagem']));
        return $retorno;
    }

    function vefifica_imagem($dados,$alterar = false){
        $erros = false;
        $erros = $this->valida_erros($dados);

        if(!$erros){
            $nome = $this->renomeia_arquivo($dados['MensagemDeAcesso']['titulo']);
            $extencao = explode('.', $dados['arquivo']['name']);
            $ext = end($extencao);
            if(!$alterar){
                if($this->verifica_titulo_existente($nome)){
                    return 'Nome da imagem já existe';
                }
            }
            $nome_renomeado = $nome.'.'.$ext;

            move_uploaded_file ($dados['arquivo']['tmp_name'] ,  'img'.DS.'mensagens'.DS.$nome_renomeado );
        }    
        if(!$erros){
            return true;
        }
        return $erros;
    }
    function valida_erros($dados){
        $tiposPermitidos = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png');
        if(!isset($dados['arquivo']['name']) || empty($dados['arquivo']['name'])){
            return 'Favor Informe o arquivo';
        }
        if(!in_array($dados['arquivo']['type'],$tiposPermitidos)){
            return 'Tipo de arquivo invalido';
        }
        if(!isset($dados['MensagemDeAcesso']['titulo']) || empty($dados['MensagemDeAcesso']['titulo'])){
            return 'Favor Informe o titulo';
        }
        if($dados['arquivo']['error'] != 0){
            return 'Erro';
        }
        return false;
    }

    function verifica_titulo_existente($titulo) {
        $path = 'img'.DS.'mensagens';
        $diretorio = dir($path);
        while($arquivo = $diretorio -> read()){
            $nome = explode('.', $arquivo);
            if($nome[0] == $titulo){
                return true;
            }
        }
        return false;
    }

    function renomeia_arquivo($titulo){
        $novo_nome = COMUM::trata_nome($titulo);
        $novo_nome = str_replace(array('.','/','\\'),'',$titulo);
        $novo_nome = str_replace(' ', '_', $novo_nome);
        return $novo_nome;
    }

    function permite_excluir($imagem){
        return $this->find('all',array(
            'conditions' => array(
                'mensagem LIKE' => '%'.$imagem.'%'
            )    
        ));
    }
    
}