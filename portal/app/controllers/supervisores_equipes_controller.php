<?php
class SupervisoresEquipesController extends AppController {
	var $name = 'SupervisoresEquipes';
	var $uses = array( 'Usuario', 'Uperfil');
    var $components = array('StringView', 'mailer.Scheduler');
    var $helpers = array('Paginator');

    function index() {
        $this->pageTitle = 'Gestão de Equipes';
        $this->data['Uperfil'] = $this->Filtros->controla_sessao( $this->data, $this->Uperfil->name );
        $this->carregaCombos();
    }

    private function carregaCombos(){
        $this->loadModel('Uperfil');
        $usuario        = $this->BAuth->user();
        $codigo_uperfil = (!empty($usuario['Usuario']['codigo_uperfil']) ? $usuario['Usuario']['codigo_uperfil'] : NULL );
        $perfil_usuario = NULL;
        if( $codigo_uperfil ){
            $perfil_usuario = $this->Uperfil->carregar($codigo_uperfil);            
            $perfis         = $this->Uperfil->listaPerfilFilho( $perfil_usuario['Uperfil']['codigo'] );
        }
        $this->set(compact('perfil_usuario', 'perfis'));
    }

    public function listagem(){
        $filtros    = $this->Filtros->controla_sessao( $this->data, $this->Uperfil->name );
        $listagem   = array();
        $usuarios_pais      = array();
        $lista_geral_filho  = array();
        $lista_filhos       = array();
        if( !empty($filtros['codigo'] )){
            $codigo_uperfil = $filtros['codigo'];
            $usuarios_pais  = $this->Usuario->listaUsuariosPorPerfl( $codigo_uperfil );
            $perfil_filho   = $this->Uperfil->find('first',array('conditions' => array('codigo_pai' => $codigo_uperfil )));            
            $codigos_perfil_filho   = set::extract('/Uperfil/codigo', $perfil_filho );
            if( $codigos_perfil_filho ){
                $conditions     = array('codigo_uperfil' => $codigos_perfil_filho );
                $fields         = array('codigo_usuario_pai', 'codigo_usuario_pai_real', 'apelido','codigo' );
                $order          = 'apelido ASC';
                $lista_filhos   = $this->Usuario->find('all', compact('conditions', 'fields', 'order') );                
            }
        }
        $this->set(compact('usuarios_pais', 'lista_filhos', 'codigo_uperfil'));
    }

    public function remanejar_equipe( ){
        if ( !empty($this->data['Usuario'])) {
            if( $this->Usuario->remanejarEquipe( $this->data['Usuario'] )){
                $this->BSession->setFlash('save_success');                
            } else{
                $this->BSession->setFlash('save_error');
            }
            $this->redirect(array('action' => 'index'));
        }
    }

    public function remanejamento_geral( $codigo_usuario_pai, $codigo_uperfil ){
        $usuarios_pais = NULL;
        if( !empty($this->data['Usuario']) ){
            if( !empty($this->data['Usuario']['codigo_usuario_pai']) ){
                if( $this->Usuario->remanejarTodaEquipe( $this->data['Usuario']['codigo_usuario_pai'], $codigo_usuario_pai  )){
                    $this->BSession->setFlash('save_success');
                } else {
                    $this->BSession->setFlash('save_error');
                }                
            } else{
                $this->Usuario->invalidate('codigo_usuario_pai', 'Selecione um responsável');
            }
        }
        $lista_usuarios_pais     = $this->Usuario->find('list',array('conditions' => array('codigo_uperfil' => $codigo_uperfil, 'codigo <>'=>$codigo_usuario_pai )));
        $dados_responsavel_atual = $this->Usuario->carregar($codigo_usuario_pai);
        $this->set(compact( 'codigo_usuario_pai', 'lista_usuarios_pais', 'codigo_uperfil', 'dados_responsavel_atual'));
    }

    public function remanejamento( $codigo_usuario_pai, $codigo_uperfil ){
        $usuarios_pais = NULL;
        $dados_responsavel_atual = $this->Usuario->carregar($codigo_usuario_pai);
        $usuarios_pais  = $this->Usuario->listaUsuariosPorPerfl( $codigo_uperfil );
        $codigos_pai    = set::extract('/Usuario/codigo', $usuarios_pais );
        $conditions     = array('codigo' => $codigos_pai, 'codigo <>'=>$codigo_usuario_pai );
        $joins = array(
            array(
                'table' => $this->Usuario->databaseTable.'.'. $this->Usuario->tableSchema.'.'.$this->Usuario->useTable,
                'alias' => 'UsuarioPaiReal',
                'conditions' => 'UsuarioPaiReal.codigo = Usuario.codigo_usuario_pai_real',
                'type' => 'left'
            )
        );
        $lista_usuarios_pais = $this->Usuario->find('list', compact('conditions') );
        $conditions     = array('Usuario.codigo_usuario_pai' => $codigo_usuario_pai, 'Usuario.codigo_usuario_pai_real <>' => $codigo_usuario_pai );
        $fields         = array(
            'Usuario.apelido', 'Usuario.codigo', 'Usuario.nome', 'Usuario.codigo_usuario_pai', 
            'Usuario.codigo_usuario_pai_real','UsuarioPaiReal.apelido'
        );
        $lista_filhos   = $this->Usuario->find('all', compact('conditions', 'fields', 'order', 'joins') );
        if( !empty($this->data['Usuario']) ){
            if( !empty($this->data['Usuario']['codigo_usuario_pai']) ){
                $codigos_usuarios  = set::extract('/Usuario/codigo', $lista_filhos );
                if( $this->Usuario->alteraResponsavelEquipe( $codigos_usuarios, $this->data['Usuario']['codigo_usuario_pai'] )){
                    $this->BSession->setFlash('save_success');
                } else {
                    $this->BSession->setFlash('save_error');
                }
            } else{
                $this->Usuario->invalidate('codigo_usuario_pai', 'Selecione um responsável');
            }
        }
        $this->data['Usuario']['codigo_usuario_pai'] = $codigo_usuario_pai;
        $this->set(compact('codigo_usuario_pai', 'codigo_uperfil', 'lista_usuarios_pais', 'lista_filhos', 'dados_responsavel_atual'));
    }
}
?>