<?php
App::import('Component', array('StringView', 'Mailer.Scheduler'));
class FornecedoresMedicosController extends AppController {
    public $name = 'FornecedoresMedicos';
    var $uses = array(
        'FornecedorMedico',
        'Medico',
        'Alerta',
        'Cliente',
        'GrupoEconomico',
        'FornecedorMedicoEspecialidade',
        'Especialidade',
        'MedicoCalendario',
        'MedicoCalendarioHorarios',
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('modal_fornecedor_medico_especialidade', 'modal_fornecedor_medico_horarios', 'salvar_especialidade', 'excluir_fme', 'salvar_horarios'));
    }
    
    function listagem($codigo_fornecedor){
        $this->layout = 'ajax';
        
        $this->FornecedorMedico->bindModel(array(
           'belongsTo' => array(
               'Medico' => array(
                   'alias' => 'Medico',
                   'foreignKey' => FALSE,
                   'type' => 'LEFT',
                   'conditions' => 'Medico.codigo = FornecedorMedico.codigo_medico'
               ),
               'ConselhoProfissional' => array(
                   'alias' => 'ConselhoProfissional',
                   'foreignKey' => FALSE,
                   'type' => 'LEFT',
                   'conditions' => 'ConselhoProfissional.codigo = Medico.codigo_conselho_profissional'
               )
           )
        ));
       $medicos = $this->FornecedorMedico->find('all', array(
          'conditions' => array('codigo_fornecedor' => $codigo_fornecedor), 
          'order' => 'LTRIM(Medico.nome)')
          //,'limit' => 10)
        );

        $this->set(compact('medicos','codigo_fornecedor'));
    }

    function incluir() {
        if($this->RequestHandler->isPost()) {
            $dados = array(
                'FornecedorMedico' => array(
                    'codigo_fornecedor' => $_POST['codigo_fornecedor'],
                    'codigo_medico' => $_POST['codigo_medico']
                    )
                );
            if ($this->FornecedorMedico->incluir($dados)) {
                self::envia_alerta_corpo_clinico($this->FornecedorMedico->id);
                // $this->BSession->setFlash('save_success');
                echo 1;
            } 
            else {
                // $this->BSession->setFlash('save_error');
                echo 0;
            }
        }
        exit;
    }

    private function envia_alerta_corpo_clinico($codigo_fornecedor_medico){
        $fm = $this->FornecedorMedico->find('first', array('conditions' => array('codigo' => $codigo_fornecedor_medico)));
        $fields = array(
            'DISTINCT GrupoEconomico.codigo_cliente as codigo_cliente',
            'Cliente.razao_social as razao_social',
        );
        $joins = array(
            array(
                'table' => 'RHHealth.dbo.grupos_economicos_clientes',
                'alias' => 'GrupoEconomicoCliente',
                'type' => 'INNER',
                'conditions' => 'GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo',
            ),
            array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'GrupoEconomico.codigo_cliente = Cliente.codigo',
            ),
            array(
                'table' => 'RHHealth.dbo.clientes_fornecedores',
                'alias' => 'ClienteFornecedor',
                'type' => 'INNER',
                'conditions' => 'ClienteFornecedor.codigo_cliente = GrupoEconomico.codigo_cliente',
            ),
            array(
                'table' => 'RHHealth.dbo.fornecedores',
                'alias' => 'Fornecedor',
                'type' => 'INNER',
                'conditions' => 'ClienteFornecedor.codigo_fornecedor = Fornecedor.codigo',
            ),
        );
        $conditions = array('Fornecedor.codigo' => $fm['FornecedorMedico']['codigo_fornecedor'], 'Cliente.ativo = 1');
        $recursive = -1;
        $matrizes = $this->GrupoEconomico->find('all', compact('fields', 'joins', 'conditions', 'recursive'));
        $this->StringView = new StringViewComponent();
        if(is_array($matrizes)){
            foreach($matrizes as $u){
                $this->StringView->reset();
                $this->StringView->set('data', array('unidade' => $u[0]['razao_social']));
                $content = $this->StringView->renderMail('email_alteracao_corpo_clinico');

                $data_alerta = array(
                    'Alerta' => array(
                        'codigo_cliente'     => $u[0]['codigo_cliente'],
                        'descricao'          => "Alteração do Corpo Clinico",
                        'assunto'            => "Alteração do Corpo Clinico",
                        'descricao_email'    => $content,
                        'codigo_alerta_tipo' => 43,//corpo clinico
                        'model'              => 'Medico',
                        'foreign_key'        => NULL,
                        'email_agendados'    => false,
                        'sms_agendados'      => false
                    ),
                );
                $this->Alerta->incluir($data_alerta);
            }
        }
    }

    public function excluir($codigo) {
        if ($this->FornecedorMedico->excluir($codigo)) {
            //self::envia_alerta_corpo_clinico($codigo);
            $this->BSession->setFlash('save_success');
            echo 1;
        } else {
            $this->BSession->setFlash('save_error');
            echo 0;
        }

        exit;
    }

    private function getMedicosHorarios($codigo_fornecedor, $codigo_medico)
    {
        $horario_medico = $this->getFornecesoresHorarios($codigo_fornecedor, null, $codigo_medico);

        return $horario_medico;
    }

    private function getFornecesoresHorarios($codigo_fornecedor = null, $diaSemanaCod = null, $codigo_medico = null)
    {
        $query = "SELECT
                        m.codigo
                        , m.nome
                        , mch.dia_semana
                        , mc.codigo_especialidade
                        , CASE
                            WHEN mch.dia_semana = 1 THEN 'Segunda'
                            WHEN mch.dia_semana = 2 THEN 'Terça'
                            WHEN mch.dia_semana = 3 THEN 'Quarta'
                            WHEN mch.dia_semana = 4 THEN 'Quinta'
                            WHEN mch.dia_semana = 5 THEN 'Sexta'
                            WHEN mch.dia_semana = 6 THEN 'Sábado'
                            WHEN mch.dia_semana = 7 THEN 'Domingo' END as dia
                        , mch.hora_inicio_manha
                        , mch.hora_fim_manha
                        , mch.hora_inicio_tarde
                        , mch.hora_fim_tarde
                    FROM
                        dbo.medicos m
                        INNER JOIN dbo.fornecedores_medicos fm ON fm.codigo_medico = m.codigo
                        INNER JOIN dbo.medico_calendario mc ON mc.codigo_medico = m.codigo
                        INNER JOIN dbo.medico_calendario_horarios mch ON mch.codigo_medico_calendario = mc.codigo
                    WHERE
                        fm.codigo_fornecedor = " . $codigo_fornecedor . " and mc.codigo_medico = " . $codigo_medico . "
                        ";

        $result = $this->FornecedorMedico->query($query);

        return $result;
    }

    /**
     * [modal_fornecedor_medico_especialidade metodo para montar a modal]
     * @param  [type] $codigo_fornecedor [description]
     * @param  [type] $codigo_medico     [description]
     * @return [type]                    [description]
     */
    public function modal_fornecedor_medico_especialidade($codigo_fornecedor, $codigo_medico)
    {

        $this->layout = 'ajax';
        
        $especialidade = $this->Especialidade->find('list',array('fields'=>array('codigo','descricao'),'order'=>array('Especialidade.descricao')));

        $profissional = $this->Medico->find('first', array('conditions' => array('Medico.codigo' => $codigo_medico), 'recursive' => -1));

        // debug($profissional);

        $this->FornecedorMedicoEspecialidade->bindModel(array(
           'belongsTo' => array(
               'Especialidade' => array(
                   'alias' => 'Especialidade',
                   'foreignKey' => FALSE,
                   'type' => 'LEFT',
                   'conditions' => 'Especialidade.codigo = FornecedorMedicoEspecialidade.codigo_especialidade'
               )
           )
        ));
        $medico_especialidade = $this->FornecedorMedicoEspecialidade->find('all',array(
            'fields' => array(
                'FornecedorMedicoEspecialidade.codigo as codigo_forn_med_espec',
                'Especialidade.descricao'
            ),
            'conditions' => array(
                'codigo_medico' => $codigo_medico, 
                'codigo_fornecedor' => $codigo_fornecedor
            )
        ));

        // debug($medico_especialidade);

        $this->set(compact('codigo_medico','codigo_fornecedor','especialidade','medico_especialidade','profissional'));

    }//fim modal_fornecedor_medico_especialidade

    /**
     * [salvar_especialidade metodo par relacionar a especialidade ao fornecedor e ao medico]
     * @return [type] [description]
     */
    public function salvar_especialidade()
    {
        //para nao solicitar um ctp
        $this->autoRender = false;

        $codigo_fornecedor = $this->params['form']['codigo_fornecedor'];
        $codigo_medico = $this->params['form']['codigo_medico'];
        $codigo_especialidade = $this->params['form']['codigo_especialidade'];

        $data = array(
            'FornecedorMedicoEspecialidade' => array(
                'codigo_medico' => $codigo_medico,
                'codigo_fornecedor' => $codigo_fornecedor,
                'codigo_especialidade' => $codigo_especialidade
            )
        );
        $dados['retorno'] = false;
        if($this->FornecedorMedicoEspecialidade->incluir($data)) {
            $dados['retorno'] = $data;
        }

        //retorna os dados com json de sucesso ou falha
        echo json_encode($dados);
        exit;
    }//fim salvar_especialidade

    /**
     * [excluir_fme deleta a especialidade para este medico]
     * @return [type] [description]
     */
    public function excluir_fme($codigo)
    {

        if ($this->FornecedorMedicoEspecialidade->excluir($codigo)) {
            //self::envia_alerta_corpo_clinico($codigo);
            $this->BSession->setFlash('save_success');
            echo 1;
        } else {
            $this->BSession->setFlash('save_error');
            echo 0;
        }

        exit;

    }//fim excluir_fme

     public function modal_fornecedor_medico_horarios($codigo_fornecedor, $codigo_medico)
    {
        $this->layout = 'ajax';

        $horarios = $this->getMedicosHorarios($codigo_fornecedor, $codigo_medico);

        $profissional = $this->Medico->find('first', array('conditions' => array('Medico.codigo' => $codigo_medico), 'recursive' => -1));

        $this->set(compact('horarios', 'codigo_fornecedor', 'codigo_medico', 'profissional'));
    }

    /**
     * [salvar_horarios metodo com os verbos post]
     */
    public function salvar_horarios()
    {
        //para nao solicitar um ctp
        $this->autoRender = false;
        try {

            //verifica qual metodo esta passando a chamada
            if ($this->RequestHandler->isPost()) {

                //pega os dados que veio do post
                $dados = $this->params['form']['form'];

                $user = $this->BAuth->user();

                if (empty($user['Usuario']['codigo'])) {
                    $error =  array(
                        "error" => "Usuário não authenticado."
                    );
                    echo json_encode($error);
                    exit;
                }

                //separa o array
                $codigo_fornecedor = $dados['codigo_fornecedor'];
                $codigo_medico = $dados['codigo_medico'];
                $codigo_especialidade = (isset($dados['codigo_especialidade']) && !empty($dados['codigo_especialidade'])) ? $dados['codigo_especialidade'] : null;

                //dados do calendario de configuracao
                $calendario = $dados['calendario'];

                //seta os dados para gravar na base os dados do medico que esta configurando
                $dados_medico_fornecedor = array(
                    "codigo_medico" => $codigo_medico,
                    "codigo_fornecedor" => $codigo_fornecedor,
                    "codigo_especialidade" => $codigo_especialidade,
                    'ativo' => 1,
                );

                //Verifica se existe dados na tabela
                $medicos_calendario = $this->MedicoCalendario->find('first', array('conditions' => array('codigo_medico' => $codigo_medico, 'codigo_fornecedor' => $codigo_fornecedor)));

                if (empty($medicos_calendario)) {

                    $dados_medico_fornecedor['codigo_usuario_inclusao'] = $user['Usuario']['codigo'];
                    $dados_medico_fornecedor['data_inclusao'] = date('Y-m-d H:i:s');
                    $data = $this->inserirMedico($dados_medico_fornecedor, $calendario);
                } else {
                    $medicos_calendario['codigo_usuario_alteracao'] = $user['Usuario']['codigo'];
                    $medicos_calendario['data_alteracao'] = date('Y-m-d H:i:s');
                    $data = $this->updateMedico($medicos_calendario, $calendario);
                }
            } //fim metodo post

            echo json_encode($data);
            exit;
        } catch (\Exception $e) {

            $error[] = $e->getMessage();
            echo json_encode($data);
            exit;
        }
    } //fim salvar_horarios

    public function inserirMedico($data, $calendario)
    {


        if (!$this->MedicoCalendario->incluir($data)) {
            $error = array(
                'message' => "Não foi possivel inserir um novo calendário!"
            );
            return json_encode($error);
            exit;
        }



        //pega o codigo_medico
        $codigo_medico_calendario = $this->MedicoCalendario->find('first', array('conditions' => array('MedicoCalendario.codigo_fornecedor' => $data['codigo_fornecedor'], 'MedicoCalendario.codigo_medico' => $data['codigo_medico']), 'recursive' => -1));

        //deleta todos os codigos para inserir os novos
        if (!$this->MedicoCalendarioHorarios->deleteAll(array('codigo_medico_calendario' => $codigo_medico_calendario['MedicoCalendario']['codigo']))) {
            $error = array(
                'message' => "Erro ao deletar!"
            );
            return json_encode($error);
            exit;
        }

        // return $codigo_medico_calendario['MedicoCalendario']['codigo'];

        //varre os dados de config do calendario
        foreach ($calendario as $cal) {

            if (empty($cal['dia_semana'])) {
                continue;
            }

            $hora_inicio_manha = trim($cal['hora_inicio_manha']);
            $hora_fim_manha = trim($cal['hora_fim_manha']);
            $hora_inicio_tarde = trim($cal['hora_inicio_tarde']);
            $hora_fim_tarde = trim($cal['hora_fim_tarde']);

            //Preenche o array com os dados para a tabela
            $dadosCalendario = array(
                'codigo_medico_calendario' => $codigo_medico_calendario['MedicoCalendario']['codigo'],
                'dia_semana' => $cal['dia_semana'],
                'hora_inicio_manha' => !empty($hora_inicio_manha) ? $hora_inicio_manha : '',
                'hora_fim_manha'    => !empty($hora_fim_manha) ? $hora_fim_manha : '',
                'hora_inicio_tarde' => !empty($hora_inicio_tarde) ? $hora_inicio_tarde : '',
                'hora_fim_tarde'    => !empty($hora_fim_tarde) ? $hora_fim_tarde : '',
                'codigo_usuario_inclusao' => $codigo_medico_calendario['MedicoCalendario']['codigo_usuario_inclusao'],
                'data_inclusao' => date('Y-m-d H:i:s'),
                'ativo' => 1
            );

            //instancia para um novo registro
            if (!$this->MedicoCalendarioHorarios->incluir($dadosCalendario)) {
                $data[]  = "Erro ao inserir na tabela de MedicoCalendarioHorarios";
                echo json_encode($data);
                exit;
            }
            //exclui linha que não tem nenhum horario
            $this->MedicoCalendarioHorarios->deleteAll(
                array(
                    'codigo_medico_calendario' => $codigo_medico_calendario['MedicoCalendario']['codigo'],
                    'hora_inicio_manha' => '',
                    'hora_fim_manha' => '',
                    'hora_inicio_tarde' => '',
                    'hora_fim_tarde' => '',
                )
            );
        } //fim foreach

        return $data;
    }

    public function updateMedico($data, $calendario)
    {

        //pega o novo codigo do calendario
        $codigo_medico_calendario = $data['MedicoCalendario']['codigo'];

        //deleta todos os codigos para inserir os novos
        if (!$this->MedicoCalendarioHorarios->deleteAll(array('codigo_medico_calendario' => $codigo_medico_calendario))) {
            $error = array(
                'message' => "Erro ao deletar!"
            );
            echo json_encode($error);
            exit;
        }

        //varre os dados de config do calendario
        foreach ($calendario as $cal) {

            if (empty($cal['dia_semana'])) {
                continue;
            }

            $hora_inicio_manha = trim($cal['hora_inicio_manha']);
            $hora_fim_manha = trim($cal['hora_fim_manha']);
            $hora_inicio_tarde = trim($cal['hora_inicio_tarde']);
            $hora_fim_tarde = trim($cal['hora_fim_tarde']);

            //Preenche o array com os dados para a tabela
            $dadosCalendario = array(
                'codigo_medico_calendario' => $codigo_medico_calendario,
                'dia_semana' => $cal['dia_semana'],
                'hora_inicio_manha' => !empty($hora_inicio_manha) ? $hora_inicio_manha : '',
                'hora_fim_manha'    => !empty($hora_fim_manha) ? $hora_fim_manha : '',
                'hora_inicio_tarde' => !empty($hora_inicio_tarde) ? $hora_inicio_tarde : '',
                'hora_fim_tarde'    => !empty($hora_fim_tarde) ? $hora_fim_tarde : '',
                'codigo_usuario_inclusao' => $data['MedicoCalendario']['codigo_usuario_alteracao'],
                'data_inclusao' => date('Y-m-d H:i:s'),
                'ativo' => 1
            );

            //instancia para um novo registro
            if (!$this->MedicoCalendarioHorarios->incluir($dadosCalendario)) {
                $data[]  = "Erro ao inserir na tabela de MedicoCalendarioHorarios";
                echo json_encode($data);
                exit;
            }
            //exclui linha que não tem nenhum horario
            $this->MedicoCalendarioHorarios->deleteAll(
                array(
                    'codigo_medico_calendario' => $codigo_medico_calendario,
                    'hora_inicio_manha' => '',
                    'hora_fim_manha' => '',
                    'hora_inicio_tarde' => '',
                    'hora_fim_tarde' => '',
                )
            );
        } //fim foreach

        return $data;
    }

}