<?php
class UsuariosHistoricosController extends AppController {
	public $name = 'UsuariosHistoricos';
    public $helpers = array('BForm', 'Ithealth');
	public $uses = array('UsuarioHistorico', 'UsuarioMultiCliente', 'Cliente', 'Usuario', 'Uperfil');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('export_lista_logins_users'));
    }

    function ultimos_acessos($codigo_usuario) {
        $this->pageTitle = "Histórico de Acessos";
        $conditions = array(
            'UsuarioHistorico.codigo_usuario' => $codigo_usuario,
            'UsuarioHistorico.data_inclusao >=' => date('Ymd 00:00:00', strtotime('-7 day'))
        );
        $order = array('UsuarioHistorico.data_inclusao DESC');
        $this->set('usuarios_historicos', $this->UsuarioHistorico->find('all', compact('conditions', 'order')));
    }

    public function relatorio_logins_users(){
        //titulo
        $this->pageTitle = "Histórico de Acessos";
        //filtros da sessao
        $filtros = $this->Filtros->controla_sessao($this->data, 'UsuarioHistorico');
        $this->carregaInfos($filtros);
    }

    public function lista_logins_users($destino, $export = false) {
        //seta que é um layout em ajax
        $this->layout = 'ajax';
        //pega os filtros que estão em sessao
        $filtros = $this->Filtros->controla_sessao($this->data, $this->UsuarioHistorico->name);

        if(!is_null($this->BAuth->user('codigo_cliente'))) {
            $filtros['codigo_cliente'] = $this->BAuth->user('codigo_cliente');
        }

        if (!empty($filtros['data_inicio']) && !empty($filtros['data_inicio'])) {
            $data_final = strtotime(AppModel::dateToDbDate2($filtros['data_fim']));
            $data_inicial = strtotime(AppModel::dateToDbDate2($filtros['data_inicio']));

            $seconds_diff = $data_final - $data_inicial;
            $dias = floor($seconds_diff/3600/24);

            if ($dias <= 31) {

                $conditions = $this->UsuarioHistorico->FiltroEmConditionUH($filtros);

                $dados = $this->UsuarioHistorico->getHistoricoUser($conditions);

                if($export){
                   //monta os indices da query
                   $dados_user = $this->UsuarioHistorico->getHistoricoUser($conditions);
                   //gera a query
                   $query = $this->UsuarioHistorico->find('sql', 
                        array(
                            'conditions' => $dados_user['conditions'], 
                            'joins' => $dados_user['joins'],
                            'fields' => $dados_user['fields'],
                            'order' => $dados_user['order'],
                            'group' => $dados_user['group'] 
                        )
                    );
                    //direciona pro metodo para exportar a planilha
                    $this->export_lista_logins_users($query);
                }

                $this->paginate['UsuarioHistorico'] = array(
                    'conditions' => $dados['conditions'],
                    'fields' => $dados['fields'],
                    'limit' => 50,
                    'joins' => $dados['joins'],
                    'order' => $dados['order'],
                    'group' => $dados['group'],
                    'recursive'  => 1
                );
                // pr($this->UsuarioHistorico->find('sql', $this->paginate['UsuarioHistorico']));

                $dados_historico = $this->paginate('UsuarioHistorico');
                // debug($dados_historico);        
                $this->set(compact('dados_historico'));
           }
        }
    }

    private function carregaInfos($filtros){
        //carrega perfis
        $u_perfis = $this->Uperfil->loadPerfis();
        //tipo de usuario
        $tipo_user = array(
            'I' => 'Interno',
            'E' => 'Externo'
        );

        if(!empty($this->authUsuario['Usuario']['codigo_cliente'])) {            
            $filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
        }

        if(empty($filtros['data_inicio'])){
            $filtros['data_fim'] = date('d/m/Y');
            $filtros['data_inicio'] = '01'.date('m/Y');
        }

        $this->data['UsuarioHistorico'] = $filtros;
        
        $this->set(compact('u_perfis', 'tipo_user'));
    }

    public function export_lista_logins_users($query){
        //instancia o dbo
        $dbo = $this->UsuarioHistorico->getDataSource();
        //pega todos os resultados
        $dbo->results = $dbo->rawQuery($query);
        // $relatorio_padrao_encoding =  'UTF-8';
        $relatorio_padrao_encoding = 'ISO-8859-1';
        //headers
        ob_clean();
        header('Content-Encoding: '.$relatorio_padrao_encoding);
        header("Content-Type: application/force-download;charset=".$relatorio_padrao_encoding);
        header('Content-Disposition: attachment; filename="historico_de_acessos.csv"');
        header('Pragma: no-cache');
        //cabecalho do arquivo
        echo Comum::converterEncodingPara('"Código de Usuário";"Nome de usuário";"Tipo de Usuário";"Login";"Perfil";"Código de Cliente";"Nome de Cliente";"Sistema";"Data Login";"Hora Login";"Tempo Logado";', $relatorio_padrao_encoding)."\n";
        // varre todos os registros da consulta no banco de dados
        while($lista_historico = $dbo->fetchRow()){

            $linha  = '';

            $linha .= $lista_historico['Usuario']['codigo'].';';
            $linha .= Comum::converterEncodingPara($lista_historico['Usuario']['nome'], $relatorio_padrao_encoding).';';
            $linha .= Comum::converterEncodingPara($lista_historico['TipoPerfil']['descricao'], $relatorio_padrao_encoding).';';
            $linha .= Comum::converterEncodingPara($lista_historico['Usuario']['apelido'], $relatorio_padrao_encoding).';';
            $linha .= Comum::converterEncodingPara($lista_historico['Uperfil']['descricao'], $relatorio_padrao_encoding).';';
            $linha .= $lista_historico['ClienteP']['codigo'].';';
            $linha .= Comum::converterEncodingPara($lista_historico['ClienteP']['nome_fantasia'], $relatorio_padrao_encoding).';';
            $linha .= Comum::converterEncodingPara($lista_historico['Sistema']['descricao'], $relatorio_padrao_encoding).';';
            $linha .= Comum::formataData($lista_historico[0]['data_acesso'],'ymd', 'dmy').';';
            $linha .= Comum::formataHora($lista_historico[0]['hora_acesso']).';';
            
            if(empty($lista_historico['UsuarioHistorico']['data_logout'])){
                $linha .= '"'.''.'";';
            } else {
                $linha .= Comum::calculaTempo($lista_historico[0]['hora_acesso'], $lista_historico[0]['hora_logout']).';';       
            }
            
            $linha .= "\n";
            
            echo $linha;
        }//fim while
        
        //mata o metodo
        die();
    }//fim export_lista_logins_users
}
?>