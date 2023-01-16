<?php
App::import('Component', 'CachedAcl');
App::import('Model', 'TipoPerfil');
App::import('Model', 'UperfilTipoAlerta');

class Uperfil extends AppModel {
    var $name = 'Uperfil';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'uperfis';
    var $primaryKey = 'codigo';
    var $displayField = 'descricao';
    var $actsAs = array('Acl' => array('type' => 'requester'), 'Secure');
    var $validate = array(
        'descricao' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o nome do perfil',
            ),
            'isUnique' => array(
                'rule' => 'nomeUnico',
                'message' => 'Perfil já existente na base',
            ),
            'comparisson' => array(
                'rule' => array('comparison', '!=', 'Admin'),
                'message' => 'Não é possível editar perfil de Adminstração'
            )
        )
    );
    
    # INTERNO
    const ADMIN = 1;
    const GERENTE_FINANCEIRO = 2;
    const GERENTE_OPERACIONAL = 4;
    const GERENTE_SAUDE = 5;
    const DIRETORIA = 8;
    
    # FORNECEDOR / CREDENCIAMENTO
    const PRESTADOR = 3;
    const CREDENCIANDO = 7;
    
    # CLIENTE
    const FUNCIONARIO = 9;
    const CLIENTE = 10;
    const MEDICO_PRESTADOR = 11;
    const RH_CLIENTE = 13;
    const ENGENHARIA_CLIENTE = 14;
    const ENFERMAGEM_PRESTADOR = 15;
    const FONO_PRESTADOR = 16;
    const ENGENHARIA_PRESTADOR = 17;
    const MEDICO_CLIENTE = 19;
    const ENFERMAGEM_CLIENTE = 20;
    const MEDICO_COORDENADOR = 27;

    const JETTA = '172.16.1.106';
    const CORDOBA = '172.16.1.100';


    function nomeUnico() {
        $conditions = array('descricao' => $this->data[$this->name]['descricao'], 'codigo_cliente' => (isset($this->data[$this->name]['codigo_cliente']) ? $this->data[$this->name]['codigo_cliente'] : NULL));
        if (isset($this->data[$this->name]['codigo']) && !empty($this->data[$this->name]['codigo'])) {
            $conditions['not'] = array('codigo' => $this->data[$this->name]['codigo']);
        }
        $jaTem = $this->find('count', compact('conditions'));
        return $jaTem == 0;
    }

    function parentNode() {
        return null;
    }

    function afterSave($created) {
        if (!$created) {
            $parent = $this->parentNode();
            $parent = $this->node($parent);
            $node = $this->node();
            $aro = $node[0];
            $aro['Aro']['parent_id'] = $parent[0]['Aro']['id'];
            $this->Aro->save($aro);
        }
    }

    function moduloInicial($codigo_uperfil) {
        $Modulo = ClassRegistry::init('Modulo');
        $Acl = new AclComponent();
        $modulos = $Modulo->modulos();
        
        foreach ($modulos as $modulo) {
            if ($Acl->check(array('model' => $this->name, 'foreign_key' => $codigo_uperfil), 'buonny/' . $modulo['url']['controller'] . '/' . $modulo['url']['action']))
                return $modulo;
        }
    }

    function moduloInicialSgi($codigo_uperfil) {
        $ModuloSgi = ClassRegistry::init('ModuloSgi');
        $Acl = new AclComponent();
        $modulos = $ModuloSgi->modulos();
        foreach ($modulos as $modulo) {
            if ($Acl->check(array('model' => $this->name, 'foreign_key' => $codigo_uperfil), 'buonny/' . $modulo['url']['controller'] . '/' . $modulo['url']['action']))
                return $modulo;
        }
    }


    function criarAdmin() {
        $admin = $this->find('count', array('conditions' => array('descricao' => 'Admin')));
        if ($admin == 0) {
            $admin = array('Uperfil' =>
                array(
                    'descricao' => 'Admin',
                    'codigo_usuario_inclusao' => 1
                )
            );
            try {
                $this->query('begin transaction');
                if (!$this->incluir($admin, false)) 
                    throw new Exception();
                $this->Acl = new AclComponent();
                $this->Acl->allow(array('model' => $this->name, 'foreign_key' => $this->id), 'buonny');
                $Usuario = ClassRegistry::init('Usuario');
                $master = $Usuario->find('first', array('conditions' => array('apelido' => 'MASTER')));
                $master['Usuario']['codigo_uperfil'] = $this->id;
                if (!$Usuario->atualizar($master))
                    throw new Exception();
                $this->commit();
                return true;
            } catch (Exception $ex) {
                $this->rollback();
            }
            return false;
        }
    }

    function listar($condicoes = null) {
        if ($condicoes == null) {
            $condicoes = array();
        }

        return $this->find('list', array('conditions' => $condicoes));
    }

    function bindAro() {
        $this->bindModel(array(
            'belongsTo' => array(
                'Aro' => array(
                    'class' => 'Aro',
                    'foreignKey' => 'foreign_key'
                )
            )
        ));
        
    }

    function geraPermissao($codigo_perfil, $data){
        $this->Acl = new CachedAclComponent();
        $this->AroAco = ClassRegistry::init('AroAco');
        $aro = array('model' => 'Uperfil', 'foreign_key' => $codigo_perfil);
        $this->Acl->clearCache($aro);
        $aro_node = $this->Acl->Aro->node($aro);
        $this->AroAco->clearByAro($aro_node[0]['Aro']['id']);
        $this->Acl->deny($aro, 'buonny');
        $this->Acl->allow($aro, 'buonny/Usuarios/inicio');
        $this->Acl->allow($aro, 'buonny/Usuarios/trocar_senha');
        $this->Acl->allow($aro, 'buonny/Filtros');
        
        if ($codigo_perfil == Uperfil::ADMIN) {
            $this->Acl->allow($aro, 'buonny/ObjetosAcl');
            $this->Acl->allow($aro, 'buonny/DependenciasObjAcl');
        }
        
        $acos_permitidas = array();
        if (!empty($data['Permissao'])) {
            foreach($data['Permissao'] as $aco_string => $permissao) {
                if($permissao){
                    $aco = "buonny/".str_replace("__", "/", $aco_string);
                    $acos_permitidas[] = $aco;
                    $acos_permitidas = array_merge($acos_permitidas, $this->verificaDependencias($aro, $aco_string));
                }
            }
        }
        
        foreach ($acos_permitidas as $aco)
            $this->Acl->allow($aro, $aco);
    }

    function verificaDependencias($aro, $aco_string, $ja_levantadas = array()) {
        $DependenciaObjAcl = ClassRegistry::init('DependenciaObjAcl');
        $dependencias = $DependenciaObjAcl->listaDependencias($aco_string);
        foreach ($dependencias as $dependencia) {
            if (!in_array($dependencia, $ja_levantadas)) {
                $sub_aco_string = str_replace('/', '__', str_replace('buonny/', '', $dependencia));
                $dependencias = array_merge($dependencias, $this->verificaDependencias($aro, $sub_aco_string, array_merge($dependencias, $ja_levantadas)));
            }
        }
        return $dependencias;
    }


    

    function listaPermissoes($codigo_perfil, $nocache = false, $cliente_admin = false) {
        $ObjetoAcl = ClassRegistry::init('ObjetoAcl');
        if ($nocache) {
            $this->Acl = new AclComponent();
        } else {
            $this->Acl = new CachedAclComponent();
        }
        $aro = array('model' => 'Uperfil', 'foreign_key' => $codigo_perfil);
        $acos = $cliente_admin ? $this->carregaObjetos($cliente_admin): $this->carregaObjetos();
        $permitidos = array();
        
        if(!empty($acos['Permissao'])){
            foreach ($acos['Permissao'] as $key_aco_string => $permissao) {
                $aco_string = "buonny/".str_replace("__", "/", $key_aco_string);
                $aco = $this->Acl->Aco->node($aco_string);
                if (!empty($aco) && $this->Acl->check($aro, $aco_string)) {
                    if ($cliente_admin)
                        $permitidos[] = $key_aco_string;
                    else
                        $permitidos['Permissao'][$key_aco_string] = true;
                } 
            }
        }
        if ($cliente_admin)
            return Set::extract('/ObjetoAcl/id', $ObjetoAcl->find('all', array('fields' => array('id'), 'conditions' => array('aco_string' => $permitidos))));
        return $permitidos;
    }
    
    function carregaObjetos($cliente_admin = null) {
        $ObjetoAcl = ClassRegistry::init('ObjetoAcl');
        $objetos = $ObjetoAcl->opcoesParaSelecao($cliente_admin);
        $opcoes = array('Permissao' => null);
        foreach ($objetos as $objeto) {
            $opcoes['Permissao'][$objeto['ObjetoAcl']['aco_string']] = 0;
        }
        return $opcoes;
    }

    function carregaPerfisCliente(){
        $conditions = array(
		'perfil_cliente' => true
        );
        $fields = array('codigo','descricao');
        $perfis = $this->find('all', compact('conditions','fields'));
        foreach($perfis as $key => $perfil){
            $perfis[$perfil['Uperfil']['codigo']] = $perfil['Uperfil']['descricao'];
            unset($perfis[$key]);
        }
        return $perfis;
    }

    function carregaPerfisSeguradora(){
        $conditions = array(
        'codigo_tipo_perfil' => TipoPerfil::SEGURADORA,
        );
        $fields = array('codigo','descricao');
        $perfis = $this->find('all', compact('conditions','fields'));
        foreach($perfis as $key => $perfil){
            $perfis[$perfil['Uperfil']['codigo']] = $perfil['Uperfil']['descricao'];
            unset($perfis[$key]);
        }
        return $perfis;
    }

    function carregaPerfisFilial(){
        $conditions = array(
        'codigo_tipo_perfil' => TipoPerfil::FILIAL,
        );
        $fields = array('codigo','descricao');
        $perfis = $this->find('all', compact('conditions','fields'));
        foreach($perfis as $key => $perfil){
            $perfis[$perfil['Uperfil']['codigo']] = $perfil['Uperfil']['descricao'];
            unset($perfis[$key]);
        }
        return $perfis;
    }

    function carregaPerfisCorretora(){
        $conditions = array(
        'codigo_tipo_perfil' => TipoPerfil::CORRETORA,
        );
        $fields = array('codigo','descricao');
        $perfis = $this->find('all', compact('conditions','fields'));
        foreach($perfis as $key => $perfil){
            $perfis[$perfil['Uperfil']['codigo']] = $perfil['Uperfil']['descricao'];
            unset($perfis[$key]);
        }
        return $perfis;
    }

    function carregaPerfisFornecedor(){
        $conditions = array(
        'codigo_tipo_perfil' => TipoPerfil::FORNECEDOR,
        );
        $fields = array('codigo','descricao');
        $perfis = $this->find('all', compact('conditions','fields'));
        foreach($perfis as $key => $perfil){
            $perfis[$perfil['Uperfil']['codigo']] = $perfil['Uperfil']['descricao'];
            unset($perfis[$key]);
        }
        return $perfis;
    }

    function carregaPerfisCadastradosPeloCliente( $codigo_cliente ){
        if( !empty($codigo_cliente) ){
            return $this->find('list', array('conditions' => array('codigo_cliente' => $codigo_cliente)));
        }
        return FALSE;
    }

    function codigoTipoPerfil($codigo){
        $conditions['codigo'] = $codigo;       
        $perfil_cliente = $this->find('all',array('conditions' => $conditions));
        return isset($perfil_cliente[0]['Uperfil']['codigo_tipo_perfil']) ? $perfil_cliente[0]['Uperfil']['codigo_tipo_perfil'] : null;
    }

    function carrega_perfis_interno(){
        return $this->find('list', array('conditions' => array('codigo_cliente' => NULL,'codigo_tipo_perfil' => TipoPerfil::INTERNO)));
    }

    function incluir_perfil_alertas($data){
    	
        $this->UperfilTipoAlerta = ClassRegistry::init('UperfilTipoAlerta');
        try{
            $this->query('begin transaction');
            if(!parent::incluir($data)){
                throw new Exception("Erro ao incluir o perfil");
            }
            $codigo_perfil = $this->getInsertId();
            if(!$this->UperfilTipoAlerta->excluir_por_perfil($codigo_perfil)){
                throw new Exception("Erro ao excluir alertas do perfil");
            }
            foreach($data['Uperfil']['codigo_alerta_tipo'] as $alerta){
               $dados['UperfilTipoAlerta']['codigo_alerta_tipo'] = $alerta;
               $dados['UperfilTipoAlerta']['codigo_uperfil'] = $codigo_perfil;
               if(!$this->UperfilTipoAlerta->incluir($dados)){
                    throw new Exception("Erro ao incluir os alertas exclusivos do perfil");
               }
            }
            $this->commit();
            return true;
        }catch(Exception $e){
            $this->rollback();
            return false;
        }            
        
    }

    function atualizar_perfil_alertas($data){
        $this->UperfilTipoAlerta = ClassRegistry::init('UperfilTipoAlerta');
        $codigo_perfil = $data['Uperfil']['codigo'];
        try{
            $this->query('begin transaction');
            if(!parent::atualizar($data)){
                throw new Exception("Erro ao atualizar o perfil");
            }
            if(!$this->UperfilTipoAlerta->excluir_por_perfil($codigo_perfil)){
                throw new Exception("Erro ao excluir alertas do perfil");
            }
            foreach($data['Uperfil']['codigo_alerta_tipo'] as $alerta){
               $dados['UperfilTipoAlerta']['codigo_alerta_tipo'] = $alerta;
               $dados['UperfilTipoAlerta']['codigo_uperfil'] = $codigo_perfil;
               if(!$this->UperfilTipoAlerta->incluir($dados)){
                    throw new Exception("Erro ao atualizar os alertas exclusivos do perfil");
               }
            }
            $this->commit();
            return true;
        }catch(Exception $e){
            $this->rollback();
            return false;
        }            
        
    }

    function retorna_perfil_pai($perfil){
        $perfil_pai = $this->carregar($perfil);
        if(!empty($perfil_pai['Uperfil']['codigo_pai'])){
            $conditions = array('codigo' => $perfil_pai['Uperfil']['codigo_pai'] );
            $order      = 'Uperfil.descricao ASC';
            return $this->find('first', compact('conditions', 'order') );
        }
        return false;            
    }

    function listaPerfilFilho( $codigo_uperfil ){
        $perfis = $this->retornaPerfisFilhos( $codigo_uperfil );
        if($perfis){
            return $this->find('list',array('conditions' => array('codigo' => $perfis )));            
        }
        return false;
    }    

    public function retornaPerfisFilhos( $codigo_uperfil ){
        $query_cte = "WITH tblFilhos AS ( ";
        $query_cte.= " SELECT PerfilPai.codigo, PerfilPai.descricao, PerfilPai.codigo_pai";
        $query_cte.= " FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} AS PerfilPai";        
        $query_cte.= " WHERE PerfilPai.codigo_pai = ". $codigo_uperfil;
        $query_cte.= " UNION ALL ";
        $query_cte.= " SELECT PerfilFilho.codigo, PerfilFilho.descricao,PerfilFilho.codigo_pai";
        $query_cte.= " FROM {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} AS PerfilFilho";
        $query_cte.= " JOIN tblFilhos  ON ( PerfilFilho.codigo_pai = tblFilhos.codigo ) ";
        $query_cte.= ") ";
        $query_cte.= "SELECT codigo FROM tblFilhos ";
        $perfil_filho = $this->query( $query_cte );
        return Set::extract($perfil_filho, '{n}.0.codigo' );        
    }


    function limpa_cache_servidor($codigo_uperfil){
        if(Ambiente::getServidor() !=  Ambiente::SERVIDOR_PRODUCAO){
            return true;
        }
        
        $service_url = Ambiente::URL_SERVIDOR_PORTAL_PRODUCAO.'/portal/sistemas/limpa_cache'; 

        $curl = curl_init($service_url);
        $curl_post_data = array(
            'uperfil' => $codigo_uperfil,
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data );
        $curl_response = curl_exec( $curl );
        curl_close($curl);
    }

    public function loadPerfis(){

        if(isset($this->authUsuario['Usuario']['admin']) && $this->authUsuario['Usuario']['admin'] == 1) {
            $uPerfilCodigo = $this->authUsuario['Usuario']['codigo_uperfil'];
            
            $conditions = array(
                    'OR' => array(
                        'codigo_cliente' => $this->authUsuario['Usuario']['codigo_cliente'],
                        'codigo'         => $uPerfilCodigo
                    )
            );

            $u_perfis = $this->find('list', array('order' => 'descricao', 'conditions' => $conditions));
        } else {
            $u_perfis = array('1' => 'Admin') + $this->find('list', array('order' => 'descricao', 'conditions' => array('codigo_cliente' => NULL )));
        }

        return $u_perfis;
    }

    /**
     * Metodo para buscar se o perfil tem o tipo interno 5
     */
    public function bloquearAnexosAgendamento($codigo_uperfil)
    {
        //so irá retornar o valor se o tipo for interno na tabela tipos_perfils
        $query = "  SELECT uperfis.codigo
                    FROM uperfis 
                        INNER JOIN tipos_perfis tp on uperfis.codigo_tipo_perfil = tp.codigo
                    WHERE tp.codigo IN (5)
                        AND uperfis.codigo = ".$codigo_uperfil;
        $dados = $this->query($query);

        // debug($dados);exit;

        return $dados;
    }//fim bloquearAnexosAgendamento


}
