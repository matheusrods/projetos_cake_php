<?php
class ImportarController extends AppController
{
    public $name = 'Importar';
    var $uses = array(
        'Importar',
        'Exame',
        'Servico',
        'Esocial',
        'GrupoEconomicoCliente',
        'Atestados',
        'PedidoExame',
        'Cliente',
        'RegistroImportacao',
        'GrupoEconomico',
        'ImportacaoEstrutura',
        'ImportacaoAtestadosRegistros'
    );
    var $helpers = array('Html', 'Form', 'Js');

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow(array(
            'tabela1', 'tabela2', 'tabela3', 'tabela5', 'tabela6', 'tabela7', 'tabela8', 'tabela9', 'tabela10', 'tabela13',
            'tabela14', 'tabela15', 'tabela16', 'tabela17', 'tabela18', 'tabela19', 'tabela20', 'tabela21', 'tabela22', 'tabela23',
            'exame', 'importar_usuario_unidade', 'importar_usuario',
        ));

        ini_set('max_execution_time', '300');
        ini_set('memory_limit', '512M');
    }


    function tabela1()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela1.csv';
        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                $linha = $file[$i];
                $dados = explode(';', $linha);

                foreach ($dados as $key => $value) {
                    if ($key == 0) {
                        $busca = $this->Esocial->find('first', array('conditions' => array('descricao' => trim($dados[0]), 'tabela' => 1)));
                        if (empty($busca)) {
                            echo "nao existe pai<br>";
                            $pai = trim($value);
                            $dados_pai = array('Esocial' => array('tabela' => 1, 'descricao' => $pai, 'nivel' => 1));
                            $this->Esocial->incluir($dados_pai);
                        } else {
                            echo "existe pai: " . $busca['Esocial']['descricao'] . "<br>";
                        }
                    }

                    if ($key == 1) {
                        $busca_pai = $this->Esocial->find('first', array('conditions' => array('descricao' => trim($dados[0]), 'tabela' => 1)));
                        $codigo_pai = $busca_pai['Esocial']['codigo'];

                        $busca_filho = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[1]), 'codigo_pai' => $codigo_pai)));

                        if (empty($busca_filho)) {
                            echo "nao existe filho<br>";
                            $dados_filho = array('Esocial' => array('tabela' => 1, 'codigo_pai' => $codigo_pai, 'codigo_descricao' => trim($dados[1]),  'descricao' => trim($dados[2]), 'nivel' => 2));
                            $this->Esocial->incluir($dados_filho);
                        } else {
                            echo "existe filho: " . $busca_filho['Esocial']['descricao'] . "<br>";
                        }
                    }
                }
            }
        }
    }

    function tabela2()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela2.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                $linha = $file[$i];
                $dados = explode(';', $linha);
                $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 2)));

                if (empty($busca)) {
                    echo "Novo: " . trim($dados[1]) . "<br>";
                    $dados_pai = array('Esocial' => array('tabela' => 2, 'codigo_descricao' => trim($dados[0]), 'descricao' => trim($dados[1]), 'nivel' => 1));
                    $this->Esocial->incluir($dados_pai);
                } else {
                    echo "Existe: " . $busca['Esocial']['descricao'] . "<br>";
                }
            }
        }
    }

    function tabela3()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela3.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                $linha = $file[$i];
                $dados = explode(';', $linha);

                $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 3)));

                if (empty($busca)) {
                    echo "Novo: " . trim($dados[1]) . "<br>";
                    $dados_pai = array('Esocial' => array('tabela' => 3, 'codigo_descricao' => trim($dados[0]), 'descricao' => trim($dados[1]), 'coluna_adicional' => trim($dados[2]), 'nivel' => 1));
                    $this->Esocial->incluir($dados_pai);
                } else {
                    echo "Existe: " . $busca['Esocial']['descricao'] . "<br>";
                }
            }
        }
    }

    function tabela5()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela5.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                $linha = $file[$i];
                $dados = explode(';', $linha);

                $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 5)));

                if (empty($busca)) {
                    echo "Novo: " . trim($dados[1]) . "<br>";
                    $dados_pai = array('Esocial' => array('tabela' => 5, 'codigo_descricao' => trim($dados[0]), 'descricao' => trim($dados[1]), 'nivel' => 1));
                    $this->Esocial->incluir($dados_pai);
                } else {
                    echo "Existe: " . $busca['Esocial']['descricao'] . "<br>";
                }
            }
        }
    }

    function tabela6()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela6.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                $linha = $file[$i];
                $dados = explode(';', $linha);

                $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 6)));

                if (empty($busca)) {
                    echo "Novo: " . trim($dados[1]) . "<br>";
                    if (trim($dados[2]) == "-") {
                        $coluna_adicional = NULL;
                    } else {
                        $coluna_adicional = trim($dados[2]);
                    }

                    if (trim($dados[3]) == "-") {
                        echo "entrou";
                        $coluna_adicional2 = NULL;
                    } else {
                        $coluna_adicional2 = trim($dados[3]);
                    }


                    $dados_pai = array('Esocial' => array(
                        'tabela' => 6,
                        'codigo_descricao' => trim($dados[0]),
                        'descricao' => trim($dados[1]),
                        'nivel' => 1,
                        'coluna_adicional' => $coluna_adicional,
                        'coluna_adicional2' => $coluna_adicional2
                    ));

                    $this->Esocial->incluir($dados_pai);
                } else {
                    echo "Existe: " . $busca['Esocial']['descricao'] . "<br>";
                }
            }
        }
    }

    function tabela8()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela8.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                $linha = $file[$i];
                $dados = explode(';', $linha);

                $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 8)));

                if (empty($busca)) {
                    echo "Novo: " . trim($dados[1]) . "<br>";
                    $dados_pai = array('Esocial' => array('tabela' => 8, 'codigo_descricao' => trim($dados[0]), 'descricao' => trim($dados[1]), 'nivel' => 1));
                    $this->Esocial->incluir($dados_pai);
                } else {
                    echo "Existe: " . $busca['Esocial']['descricao'] . "<br>";
                }
            }
        }
    }

    function tabela9()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela9.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                $linha = $file[$i];
                $dados = explode(';', $linha);

                $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 9)));

                if (empty($busca)) {
                    echo "Novo: " . trim($dados[1]) . "<br>";
                    $dados_pai = array('Esocial' => array('tabela' => 9, 'codigo_descricao' => trim($dados[0]), 'descricao' => trim($dados[1]), 'nivel' => 1));
                    $this->Esocial->incluir($dados_pai);
                } else {
                    echo "Existe: " . $busca['Esocial']['descricao'] . "<br>";
                }
            }
        }
    }

    function tabela13()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela13.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                $linha = $file[$i];
                $dados = explode(';', $linha);

                $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 13)));

                if (empty($busca)) {
                    echo "Novo: " . trim($dados[1]) . "<br>";
                    $dados_pai = array('Esocial' => array('tabela' => 13, 'codigo_descricao' => trim($dados[0]), 'descricao' => trim($dados[1]), 'nivel' => 1));
                    $this->Esocial->incluir($dados_pai);
                } else {
                    echo "Existe: " . $busca['Esocial']['descricao'] . "<br>";
                }
            }
        }
    }

    function tabela14()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela14.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                $linha = $file[$i];
                $dados = explode(';', $linha);

                $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 14)));

                if (empty($busca)) {
                    echo "Novo: " . trim($dados[1]) . "<br>";
                    $dados_pai = array('Esocial' => array('tabela' => 14, 'codigo_descricao' => trim($dados[0]), 'descricao' => trim($dados[1]), 'nivel' => 1));
                    $this->Esocial->incluir($dados_pai);
                } else {
                    echo "Existe: " . $busca['Esocial']['descricao'] . "<br>";
                }
            }
        }
    }
    function tabela15()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela15.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                $linha = $file[$i];
                $dados = explode(';', $linha);

                $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 15)));

                if (empty($busca)) {
                    echo "Novo: " . trim($dados[1]) . "<br>";
                    $dados_pai = array('Esocial' => array('tabela' => 15, 'codigo_descricao' => trim($dados[0]), 'descricao' => trim($dados[1]), 'nivel' => 1));
                    $this->Esocial->incluir($dados_pai);
                } else {
                    echo "Existe: " . $busca['Esocial']['descricao'] . "<br>";
                }
            }
        }
    }

    function tabela16()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela16.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                $linha = $file[$i];
                $dados = explode(';', $linha);

                $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 16)));

                if (empty($busca)) {
                    echo "Novo: " . trim($dados[1]) . "<br>";
                    $dados_pai = array('Esocial' => array('tabela' => 16, 'codigo_descricao' => trim($dados[0]), 'descricao' => trim($dados[1]), 'nivel' => 1));
                    $this->Esocial->incluir($dados_pai);
                } else {
                    echo "Existe: " . $busca['Esocial']['descricao'] . "<br>";
                }
            }
        }
    }

    function tabela17()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela17.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                $linha = $file[$i];
                $dados = explode(';', $linha);

                $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 17)));

                if (empty($busca)) {
                    echo "Novo: " . trim($dados[1]) . "<br>";
                    $dados_pai = array('Esocial' => array('tabela' => 17, 'codigo_descricao' => trim($dados[0]), 'descricao' => trim($dados[1]), 'nivel' => 1));
                    $this->Esocial->incluir($dados_pai);
                } else {
                    echo "Existe: " . $busca['Esocial']['descricao'] . "<br>";
                }
            }
        }
    }

    function tabela18()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela18.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                $linha = $file[$i];
                $dados = explode(';', $linha);

                $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 18)));

                if (empty($busca)) {
                    echo "Novo: " . trim($dados[1]) . "<br>";
                    $dados_pai = array('Esocial' => array('tabela' => 18, 'codigo_descricao' => trim($dados[0]), 'descricao' => trim($dados[1]), 'nivel' => 1));
                    $this->Esocial->incluir($dados_pai);
                } else {
                    echo "Existe: " . $busca['Esocial']['descricao'] . "<br>";
                }
            }
        }
    }

    function tabela19()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela19.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                $linha = $file[$i];
                $dados = explode(';', $linha);

                $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 19)));

                if (empty($busca)) {
                    echo "Novo: " . trim($dados[1]) . "<br>";
                    $dados_pai = array('Esocial' => array('tabela' => 19, 'codigo_descricao' => trim($dados[0]), 'descricao' => trim($dados[1]), 'nivel' => 1));
                    $this->Esocial->incluir($dados_pai);
                } else {
                    echo "Existe: " . $busca['Esocial']['descricao'] . "<br>";
                }
            }
        }
    }

    function tabela20()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela20.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                $linha = $file[$i];
                $dados = explode(';', $linha);

                $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 20)));

                if (empty($busca)) {
                    echo "Novo: " . trim($dados[1]) . "<br>";
                    $dados_pai = array('Esocial' => array('tabela' => 20, 'codigo_descricao' => trim($dados[0]), 'descricao' => trim($dados[1]), 'nivel' => 1));
                    $this->Esocial->incluir($dados_pai);
                } else {
                    echo "Existe: " . $busca['Esocial']['descricao'] . "<br>";
                }
            }
        }
    }

    function tabela7()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela7.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                $linha = $file[$i];
                $dados = explode(';', $linha);

                foreach ($dados as $key => $value) {
                    if ($key == 0) {
                        $busca = $this->Esocial->find('first', array('conditions' => array('descricao' => trim($dados[1]), 'tabela' => 7)));

                        if (empty($busca)) {
                            echo "Novo: " . trim($dados[1]) . "<br>";
                            $pai = trim($value);
                            $dados_pai = array('Esocial' => array(
                                'tabela' => 7,
                                'codigo_descricao' => trim($dados[0]),
                                'descricao' => trim($dados[1]),
                                'coluna_adicional' => trim($dados[2]),
                                'nivel' => 1
                            ));
                            $this->Esocial->incluir($dados_pai);
                        } else {
                            echo "Existe: " . $busca['Esocial']['descricao'] . "<br>";
                        }
                    }

                    if ($key == 1) {
                        $busca_pai = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 7)));
                        $codigo_pai = $busca_pai['Esocial']['codigo'];

                        $busca_filho = $this->Esocial->find('first', array('conditions' => array('descricao' => trim($dados[4]), 'codigo_pai' => $codigo_pai)));


                        if (empty($busca_filho)) {
                            echo "Novo: " . trim($dados[4]) . "<br>";
                            $dados_filho = array('Esocial' => array(
                                'tabela' => 7,
                                'codigo_pai' => $codigo_pai,
                                'codigo_descricao' => trim($dados[3]),
                                'descricao' => trim($dados[4]),
                                'nivel' => 2
                            ));

                            $this->Esocial->incluir($dados_filho);
                        } else {
                            echo "Existe filho: " . $busca_filho['Esocial']['descricao'] . "<br>";
                        }
                    }
                }
            }
        }
    }

    function tabela10()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela10.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                $linha = $file[$i];
                $dados = explode(';', $linha);

                $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 10)));

                if (empty($busca)) {
                    echo "Novo: " . trim($dados[1]) . "<br>";
                    $dados_pai = array('Esocial' => array(
                        'tabela' => 10,
                        'codigo_descricao' => trim($dados[0]),
                        'descricao' => trim($dados[1]),
                        'coluna_adicional' => trim($dados[2]),
                        'nivel' => 1
                    ));

                    $this->Esocial->incluir($dados_pai);
                } else {
                    echo "Existe: " . $busca['Esocial']['descricao'] . "<br>";
                }
            }
        }
    }

    function tabela21()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela21.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                echo $i . "-";

                $linha = $file[$i];
                $dados = explode(';', $linha);

                if (trim($dados[1]) == "") {
                    //NIVEL 1
                    $busca = $this->Esocial->find('first', array('conditions' => array('descricao' => trim($dados[0]), 'tabela' => 21)));

                    if (empty($busca)) {

                        $insere = array('Esocial' => array(
                            'tabela' => 21,
                            'descricao' => trim($dados[0]),
                            'nivel' => 1
                        ));
                        if ($this->Esocial->incluir($insere)) {
                            echo "Novo NIVEL 1: " . trim($dados[0]) . "<br>";
                        } else {
                            echo "Erro NIVEL 1: " . trim($dados[0]) . "<br>";
                        }
                    } else {
                        echo "Existe NIVEL 1: " . $busca['Esocial']['descricao'] . "<br>";
                    }
                } elseif (trim($dados[2]) == "") {
                    // NIVEL 2

                    $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 21, 'nivel' => 2)));
                    if (empty($busca)) {

                        if (trim($dados[0]) == "999") {
                            $pai = $this->Esocial->find('first', array('conditions' => array('descricao' =>  utf8_decode('MECÂNICO/ACIDENTES'), 'tabela' => 21, 'nivel' => 1)));
                        } else {
                            $pai = $this->Esocial->find('first', array('conditions' => array('descricao like' =>  substr(trim($dados[0]), 0, 1) . '%', 'tabela' => 21, 'nivel' => 1)));
                        }

                        $insere = array('Esocial' => array(
                            'tabela' => 21,
                            'codigo_pai' => $pai['Esocial']['codigo'],
                            'codigo_descricao' => trim($dados[0]),
                            'descricao' => trim($dados[1]),
                            'nivel' => 2
                        ));
                        if ($this->Esocial->incluir($insere)) {
                            echo "Novo NIVEL 2: " . trim($dados[1]) . "<br>";
                        } else {
                            echo "Erro Novo NIVEL 2: " . trim($dados[1]) . "<br>";
                        }
                    } else {
                        echo "Existe NIVEL 2: " . $busca['Esocial']['descricao'] . "<br>";
                    }
                } elseif (trim($dados[3]) == "") {
                    // NIVEL 3

                    $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[1]), 'tabela' => 21, 'nivel' => 3)));
                    if (empty($busca)) {

                        $pai = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' =>  trim($dados[0]), 'tabela' => 21, 'nivel' => 2)));

                        $insere = array('Esocial' => array(
                            'tabela' => 21,
                            'codigo_pai' => $pai['Esocial']['codigo'],
                            'codigo_descricao' => trim($dados[1]),
                            'descricao' => trim($dados[2]),
                            'nivel' => 3
                        ));
                        if ($this->Esocial->incluir($insere)) {
                            echo "Novo NIVEL 3: " . trim($dados[1]) . "-" . trim($dados[2]) . "<br>";
                        } else {
                            echo "Erro NIVEL 3: " . trim($dados[1]) . "-" . trim($dados[2]) . "<br>";
                        }
                    } else {
                        echo "Existe NIVEL 3: " . $busca['Esocial']['descricao'] . "<br>";
                    }
                } else {
                    // NIVEL 4

                    $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[2]), 'tabela' => 21, 'nivel' => 4)));
                    if (empty($busca)) {

                        $pai = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' =>  trim($dados[1]), 'tabela' => 21, 'nivel' => 3)));

                        $insere = array('Esocial' => array(
                            'tabela' => 21,
                            'codigo_pai' => $pai['Esocial']['codigo'],
                            'codigo_descricao' => trim($dados[2]),
                            'descricao' => trim($dados[3]),
                            'nivel' => 4
                        ));
                        if ($this->Esocial->incluir($insere)) {
                            echo "Novo  NIVEL 4: " . trim($dados[2]) . "-" . trim($dados[3]) . "<br>";
                        } else {
                            echo "Erro NIVEL 4: " . trim($dados[2]) . "-" . trim($dados[3]) . "<br>";
                        }
                    } else {
                        echo "Existe  NIVEL 4: " . $busca['Esocial']['descricao'] . "<br>";
                    }
                }
            }
        }
    }

    function tabela22()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela22.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                echo $i . "-";

                $linha = $file[$i];
                $dados = explode(';', $linha);

                $busca = $this->Esocial->find('first', array('conditions' => array('descricao' => trim($dados[0]), 'tabela' => 22)));

                if (empty($busca)) {

                    if (trim($dados[2]) == "-") {
                        $coluna_adicional = NULL;
                    } else {
                        $coluna_adicional = trim($dados[2]);
                    }

                    if (trim($dados[3]) == "-") {
                        $coluna_adicional2 = NULL;
                    } else {
                        $coluna_adicional2 = trim($dados[3]);
                    }



                    $insere = array('Esocial' => array(
                        'tabela' => 22,
                        'codigo_descricao' => trim($dados[0]),
                        'descricao' => trim($dados[1]),
                        'coluna_adicional' => $coluna_adicional,
                        'coluna_adicional2' => $coluna_adicional2,
                        'nivel' => 1
                    ));

                    if ($this->Esocial->incluir($insere)) {
                        echo "Novo NIVEL 1: " . trim($dados[0]) . "<br>";
                    } else {
                        echo "Erro NIVEL 1: " . trim($dados[0]) . "<br>";
                    }
                } else {
                    echo "Existe NIVEL 1: " . $busca['Esocial']['descricao'] . "<br>";
                }
            }
        }
    }

    function tabela23()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'tabela23.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {
                echo $i . "-";

                $linha = $file[$i];
                $dados = explode(';', $linha);

                if (trim($dados[2]) == "" && trim($dados[3]) == "") {
                    //NIVEL 1
                    $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 23)));

                    if (empty($busca)) {

                        $insere = array('Esocial' => array(
                            'tabela' => 23,
                            'codigo_descricao' => trim($dados[0]),
                            'descricao' => trim($dados[1]),
                            'nivel' => 1
                        ));

                        if ($this->Esocial->incluir($insere)) {
                            echo "Novo NIVEL 1: " . trim($dados[0]) . "<br>";
                        } else {
                            echo "Erro NIVEL 1: " . trim($dados[0]) . "<br>";
                        }
                    } else {
                        echo "Existe NIVEL 1: " . $busca['Esocial']['descricao'] . "<br>";
                    }
                } else {
                    // NIVEL 2

                    $busca = $this->Esocial->find('first', array('conditions' => array('codigo_descricao' => trim($dados[0]), 'tabela' => 23)));

                    if (empty($busca)) {

                        $insere = array('Esocial' => array(
                            'tabela' => 23,
                            'codigo_descricao' => trim($dados[0]),
                            'descricao' => trim($dados[1]),
                            'coluna_adicional' => trim($dados[2]),
                            'coluna_adicional2' => trim($dados[3]),
                            'nivel' => 2
                        ));


                        if ($this->Esocial->incluir($insere)) {
                            echo "Novo NIVEL 1: " . trim($dados[0]) . "<br>";
                        } else {
                            echo "Erro NIVEL 1: " . trim($dados[0]) . "<br>";
                        }
                    } else {
                        echo "Existe NIVEL 1: " . $busca['Esocial']['descricao'] . "<br>";
                    }
                }
            }
        }
    }

    function exame()
    {
        $this->render(false, false);
        $arquivo = APP . 'tmp' . DS . 'exames.csv';

        $file = file($arquivo);

        if ($file) {

            for ($i = 0; $i < count($file); $i++) {


                $linha = $file[$i];
                $dados = explode(';', $linha);

                $descricao = Comum::trata_nome(utf8_encode($dados[2]));
                $busca = $this->Exame->find('first', array('conditions' => array('descricao' => $descricao)));
                if (empty($busca)) {
                    echo $i . "-";
                    $busca_servico = $this->Servico->find('first', array('conditions' => array('descricao' => $descricao)));
                    if (!empty($busca_servico)) {
                        $servico = $busca['Servico']['codigo'];

                        $this->data = array(
                            'codigo_servico' =>  $servico,
                            'codigo_rh' => $dados[1],
                            'descricao' => strtoupper($descricao),
                            'periodo_meses' => $dados[3],
                            'codigo_tabela_amb' => $dados[4],
                            'codigo_tuss' => $dados[5],
                            'empresa_cliente' => ($dados[6] == "SIM" ? 1 : 0),
                            'exame_auto' => ($dados[7] == "SIM" ? 1 : 0),
                            'codigo_ch' => $dados[8],
                            'laboral' => ($dados[9] == "SIM" ? 1 : 0),
                            'tela_resultado' => $dados[10],
                            'referencia' => $dados[11],
                            'unidade_medida' => $dados[12],
                            'recomendacoes' => $dados[13],
                            'sexo' => ($dados[15] == "Ambos" ? 'A' : 'M'),
                            'exame_excluido_convocacao' => ($dados[17] == "SIM" ? 1 : 0),
                            'exame_excluido_ppp' => ($dados[18] == "SIM" ? 1 : 0),
                            'exame_excluido_aso' => ($dados[19] == "SIM" ? 1 : 0),
                            'exame_excluido_pcmso'  => ($dados[20] == "SIM" ? 1 : 0),
                            'exame_excluido_anual'  => ($dados[21] == "SIM" ? 1 : 0),
                            'exame_admissional' => ($dados[22] == "SIM" ? 1 : 0),
                            'exame_periodico ' => ($dados[23] == "SIM" ? 1 : 0),
                            'exame_demissional ' => ($dados[24] == "SIM" ? 1 : 0),
                            'exame_retorno'   => ($dados[25] == "SIM" ? 1 : 0),
                            'exame_mudanca' => ($dados[26] == "SIM" ? 1 : 0),
                            'codigo_usuario_inclusao ' => 61648,
                            'ativo' => 1
                        );

                        if ($this->Exame->incluir($this->data))
                            echo "OK - " . $descricao . "<br>";
                        else
                            echo "ERRO - " . $descricao . "<br>";
                    } else {
                        echo "NAO EXISTE SERVICO CADASTRADO - " . $descricao . "<br>";
                    }
                }
            }
        }
    }

    function exportar_importacao_processada($codigo_importacao_estrutura)
    {
        App::import('model', 'StatusImportacao');
        $this->loadModel('RegistroImportacao');

        // ini_set('memory_limit', '536870912');
        // ini_set('max_execution_time', '999999');
        // set_time_limit(0);

        $conditions = array(
            'codigo_importacao_estrutura' => $codigo_importacao_estrutura,
            'OR' => array(
                'codigo_status_importacao <>' => StatusImportacao::PROCESSADO,
                'codigo_status_importacao' => null
            )
        );
        $query = $this->RegistroImportacao->find('sql', compact('conditions'));
        $dbo = $this->RegistroImportacao->getDataSource();
        $dbo->results = $dbo->rawQuery($query);
        // se houver buffer entao limpe
        if (ob_get_length() > 0) {
            ob_clean();
        }
        $nome_arquivo = date('YmdHis') . 'er.csv';
        header('Content-Encoding: ISO-8859-1');
        header('Content-type: text/csv; charset=ISO-8859-1');
        header(sprintf('Content-Disposition: attachment; filename="%s"', $nome_arquivo));
        header('Pragma: no-cache');

        echo utf8_decode('"Código Unidade";"Nome da Unidade";"Nome do Setor";"Nome do Cargo";"Código Matrícula";"Matricula do Funcionario";"Nome do Funcionario";"Data de Nascimento(dd/mm/aaaa)";"Sexo(F:Feminino, M:Masculino)";"Situacao Cadastral(S:Ativo, F:Ferias, A:Afastado, I:Inativo)";"Data de Admissao(dd/mm/aaaa)";"Data de Demissao(dd/mm/aaaa)";"Data Início Cargo(dd/mm/aaaa)";"Estado Civil(1:Solteiro, 2:Casado, 3:Separado, 4:Divorciado, 5:Viuvo, 6:Outros)";"Pis/Pasep";"Rg";"Estado RG";"CPF";"CTPS";"Serie CTPS";"UF CTPS";"Endereco";"Numero";"Complemento";"Bairro";"Cidade";"Estado";"Cep";"Possui Deficiencia(S:Sim, N:Não)";"Codigo CBO";"Codigo GFIP";"Centro Custo";"Turno";"Descricao de atividades do cargo";"Celular do Funcionario((ddd)+numero telefone)";"Autoriza envio de SMS ao funcionario";"E-mail do Funcionario";"Autoriza envio de e-mail ao funcionario";"Contato do responsavel da Unidade";"Telefone do responsavel da Unidade((ddd)+numero telefone)";"E-maildo responsavel da Unidade";"Endereco da Unidade";"Numero da Unidade";"Complemento da Unidade";"Bairro da Unidade";"Cidade da Unidade";"Estado da Unidade";"Cep da Unidade";"CNPJ da Unidade";"Inscricao Estadual";"Inscricao Municipal";"Cnae";"Grau de Risco";"Razao Social Unidade";"Unidade de Negocio";"Regime Tributario(1:Simples Nacional, 2:Simples Nacional, excesso sublimite de receita bruta, 3:Regime Normal)";"Codigo Externo";"Tipo Unidade(F: Fiscal, O: Operacional)";"Conselho Profissional";"Número do Conselho";"Conselho Estado(UF)";"Chave Externa";"Código Cargo Externo";"Observação"' . "\n");
        while ($dado = $dbo->fetchRow()) {
            $linha = $dado['RegistroImportacao']['codigo_alocacao'] . ';';
            $linha .= '"' . $dado['RegistroImportacao']['nome_alocacao'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['nome_setor'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['nome_cargo'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['codigo_matricula'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['matricula_funcionario'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['nome_funcionario'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['data_nascimento'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['sexo'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['situacao_cadastral'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['data_admissao'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['data_demissao'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['data_inicio_cargo'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['estado_civil'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['pis_pasep'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['rg'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['estado_rg'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['cpf'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['ctps'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['serie_ctps'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['uf_ctps'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['endereco_funcionario'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['numero_funcionario'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['complemento_funcionario'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['bairro_funcionario'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['cidade_funcionario'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['estado_funcionario'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['cep_funcionario'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['possui_deficiencia'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['codigo_cbo'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['codigo_gfip'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['centro_custo'] . '";';
            
            #ESSAS DUAS COLUNAS NAO ESTAO SENDO NEM PEDIDAS NA PLANILHA MODELO#
            // $linha .= '"' . $dado['RegistroImportacao']['data_ultimo_aso'] . '";';
            // $linha .= '"' . $dado['RegistroImportacao']['aptidao'] . '";';
            
            $linha .= '"' . $dado['RegistroImportacao']['turno'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['descricao_detalhada_cargo'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['celular_funcionario'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['autoriza_envio_sms_funcionario'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['email_funcionario'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['autoriza_envio_email_funcionario'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['contato_responsavel_alocacao'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['telefone_responsavel_alocacao'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['email_responsavel_alocacao'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['endereco_alocacao'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['numero_alocacao'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['complemento_alocacao'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['bairro_alocacao'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['cidade_alocacao'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['estado_alocacao'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['cep_alocacao'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['cnpj_alocacao'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['inscricao_estadual'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['inscricao_municipal'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['cnae'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['grau_risco'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['razao_social_alocacao'] . '";';
            $linha .= '"' . '";';
            $linha .= '"' . $dado['RegistroImportacao']['regime_tributario'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['codigo_externo_alocacao'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['tipo_alocacao'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['conselho_profissional'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['numero_conselho'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['conselho_uf'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['chave_externa'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['codigo_cargo_externo'] . '";';
            $linha .= '"' . $dado['RegistroImportacao']['observacao'] . '";';
            $linha .= "\n";
            echo utf8_decode($linha);
        }
        exit;
    }

    function importar_funcionario($codigo_cliente, $referencia = 'sistema', $terceiros_implantacao = 'interno')
    { //codigo_cliente -> GrupoEconomico.) & terceiros -> UsuarioCliente
        $this->loadModel('GrupoEconomico');
        $this->loadModel('ImportacaoEstrutura');
        $this->pageTitle = 'Importação Dados';

        ini_set('memory_limit', '1024M');

        if (!isset($codigo_cliente) && empty($codigo_cliente)) {
            $this->redirect('/');
        }
        if (!empty($this->data)) {
            if (preg_match('@\.(csv)$@i', $this->data['ImportacaoEstrutura']['nome_arquivo']['name'])) {
                $path_destino = APP . 'tmp' . DS;
                $arquivo_destino = $this->data['ImportacaoEstrutura']['nome_arquivo']['name'];
                if (move_uploaded_file($this->data['ImportacaoEstrutura']['nome_arquivo']['tmp_name'], $path_destino . $arquivo_destino)) {
                    if ($this->ImportacaoEstrutura->incluir($path_destino, $arquivo_destino, $codigo_cliente)) {
                        $this->BSession->setFlash('save_success');
                    } else {
                        $error = $this->ImportacaoEstrutura->invalidFields();
                        $this->BSession->setFlash(array(MSGT_ERROR, $error['codigo']));
                    }
                } else {
                    $this->BSession->setFlash('save_error');
                }
            } else {
                $this->Importar->invalidate('nome_arquivo', 'Extensão inválida!');
                $this->BSession->setFlash('save_error');
            }

            $this->redirect(array('controller' => 'importar', 'action' => 'importar_funcionario', $codigo_cliente, $referencia, $terceiros_implantacao));
        }

        $this->GrupoEconomico->bindModel(array('belongsTo' => array('Cliente' => array('foreignKey' => 'codigo_cliente'))));

        $grupo_economico = $this->GrupoEconomico->findByCodigoCliente($codigo_cliente);

        $arquivos_importados = $this->ImportacaoEstrutura->getImportacaoEstrutura($grupo_economico);

        $this->set(compact('arquivos_importados', 'grupo_economico', 'terceiros_implantacao', 'referencia'));
    }

    function eliminar_importacao_funcionario($codigo_cliente, $codigo_importacao_estrutura, $referencia = 'sistema', $terceiros_implantacao = 'interno')
    {
        $this->pageTitle = 'Exclusão de arquivo para importação';
        $this->loadModel('ImportacaoEstrutura');
        $this->ImportacaoEstrutura->bindModel(array('hasOne' => array(
            'RegistroImportacao' => array('foreignKey' => 'codigo_importacao_estrutura')
        )));
        if ($this->RequestHandler->isPost()) {
            $estrutura = $this->ImportacaoEstrutura->carregar($codigo_importacao_estrutura);
            if ($estrutura['ImportacaoEstrutura']['codigo_status_importacao'] == StatusImportacao::SEM_PROCESSAR) {
                if ($this->ImportacaoEstrutura->excluir($codigo_importacao_estrutura)) {
                    $this->BSession->setFlash('delete_success');
                    $this->redirect(array('action' => 'importar_funcionario', $codigo_cliente, $referencia, $terceiros_implantacao));
                } else {
                    $this->BSession->setFlash('delete_error');
                }
            } else {
                $this->BSession->setFlash('delete_error_imp_em_proc');
                $this->redirect(array('action' => 'importar_funcionario', $codigo_cliente, $referencia, $terceiros_implantacao));
            }
        }
        $conditions = array('ImportacaoEstrutura.codigo' => $codigo_importacao_estrutura);
        $fields = array('nome_arquivo', 'data_inclusao', 'RegistroImportacao.nome_alocacao', 'COUNT(*) AS qtd_funcionarios');
        $group = array('nome_arquivo', 'data_inclusao', 'RegistroImportacao.nome_alocacao');
        $estrutura = $this->ImportacaoEstrutura->find('all', compact('fields', 'conditions', 'group'));
        $this->set(compact('estrutura', 'referencia', 'terceiros_implantacao'));
    }

    function gerenciar_importacao_estrutura($codigo_cliente, $codigo_importacao_estrutura, $referencia = 'sistema', $terceiros_implantacao = 'interno')
    {
        $this->pageTitle = 'Gerenciar arquivo para importação';
        $this->loadModel('ImportacaoEstrutura');
        if ($this->RequestHandler->isPost()) {
            Comum::execInBackground(ROOT . '/cake/console/cake -app ' . ROOT . DS . 'app importacao estrutura ' . "{$_SESSION['Auth']['Usuario']['codigo_empresa']} {$_SESSION['Auth']['Usuario']['codigo']} {$codigo_importacao_estrutura}");

            //print (ROOT . '/cake/console/cake -app '. ROOT . DS . 'app importacao estrutura '."{$_SESSION['Auth']['Usuario']['codigo_empresa']} {$_SESSION['Auth']['Usuario']['codigo']} {$codigo_importacao_estrutura}");exit;
            $this->redirect(array('action' => 'importar_funcionario', $codigo_cliente, $referencia, $terceiros_implantacao));
        }
        $estrutura = $this->ImportacaoEstrutura->carregar($codigo_importacao_estrutura);
        $this->set(compact('estrutura', 'referencia', 'terceiros_implantacao'));
    }

    function importacao_estrutura_listagem($codigo_importacao_estrutura)
    {
        $this->loadModel('ImportacaoEstrutura');
        $this->loadModel('RegistroImportacao');

        $conditions = array(
            'codigo_importacao_estrutura' => $codigo_importacao_estrutura,
        );

        $this->paginate['RegistroImportacao'] = array(
            'conditions' => $conditions,
            'limit' => 100,
            'order' => 'nome_funcionario',
            'extra' => array('importacao' => true)
        );

        $registros = $this->paginate('RegistroImportacao');

        //$registros = array_unique($registros);

        $importacao_estrutura = $this->ImportacaoEstrutura->carregar($codigo_importacao_estrutura);
        $codigo_status_importacao = $importacao_estrutura['ImportacaoEstrutura']['codigo_status_importacao'];
        $registros = $this->trataEncode($registros);
        $alertas = $this->comparaRegistros($registros, $codigo_status_importacao);
        $depara = $this->RegistroImportacao->depara();
        $titulos = $this->RegistroImportacao->titulos();

        //Valida os registros somente se a importação não foi processada
        if ($codigo_status_importacao == StatusImportacao::SEM_PROCESSAR) {
            $validacoes = $this->RegistroImportacao->validaRegistros($registros);
        } else {
            $validacoes = array();
        }

        $this->set(compact('registros', 'alertas', 'depara', 'titulos', 'validacoes', 'codigo_status_importacao'));
    }

    private function trataEncode($registros)
    {
        foreach ($registros as $key => $registro) {
            $registros[$key][0]['alocacao_endereco'] = utf8_encode($registros[$key][0]['alocacao_endereco']);
            $registros[$key][0]['alocacao_endereco_bairro'] = utf8_encode($registros[$key][0]['alocacao_endereco_bairro']);
            $registros[$key][0]['alocacao_endereco_cidade'] = utf8_encode($registros[$key][0]['alocacao_endereco_cidade']);
        }
        return $registros;
    }

    private function comparaRegistros($registros, $codigo_status_importacao)
    {
        $alertas = array();
        foreach ($registros as $key => $registro) {
            $alertas[$key] = $this->comparaRegistro($registro, $codigo_status_importacao);
        }
        return $alertas;
    }

    private function comparaRegistro($registro, $codigo_status_importacao)
    {
        $alertas = array_keys($registro[0]);
        $alertas = array_flip($alertas);
        $depara = $this->RegistroImportacao->depara();

        foreach ($depara as $campo_planilha => $campo_tabela) {
            $registro[0][$campo_tabela] = trim($registro[0][$campo_tabela]);
            $registro[0][$campo_planilha] = trim($registro[0][$campo_planilha]);

            // debug($registro[0][$campo_tabela]);
            // debug($registro[0][$campo_planilha]);

            if ($codigo_status_importacao == StatusImportacao::SEM_PROCESSAR && !empty($registro[0][$campo_planilha])) {
                if (!empty($registro[0][$campo_tabela])) {
                    $alertas[$campo_planilha] = 'inclusao';

                    // debug('to incluindo');
                    // debug($registro[0]);

                    //Identifica se há inclusão de nova unidade e alteração na unidade do funcionário
                    if ($campo_planilha == 'nome_alocacao') {
                        //Se o funcionario já possui matricula com outro cliente
                        if (!empty($registro[0]["cliente_alocacao_atual"])) {
                            $alertas[$campo_planilha] = 'ambos';
                        }
                    }
                } elseif ($registro[0][$campo_planilha] != $registro[0][$campo_tabela]) {

                    $alertas[$campo_planilha] = 'alteracao';
                    // debug('to alterando');

                    if ($campo_planilha == 'nome_setor') {
                        if (empty($registro[0]["existe_setor"])) {
                            $alertas[$campo_planilha] = 'ambos';
                        }
                    }

                    if ($campo_planilha == 'nome_cargo') {
                        if (empty($registro[0]["existe_cargo"])) {
                            $alertas[$campo_planilha] = 'ambos';
                        }
                    }

                    if ($campo_planilha == 'data_admissao' || $campo_planilha == 'data_inicio_cargo') {
                        if (empty($registro[0]['codigo_matricula']) && !empty($registro[0]['codigo_cliente_funcionario'])) {
                            $alertas[$campo_planilha] = 'inclusao';
                        }
                    }
                }
            } else {
                $alertas[$campo_planilha] = '';
            }
        }

        return $alertas;
    }

    private function gravaArquivo($data, $arquivo_nome)
    {
        if (!empty($data)) {
            set_time_limit(0);
            $destino = DIR_ARQUIVOS . 'importacao_dados' . DS;

            $arquivo = $destino . $arquivo_nome;

            if (!is_dir($destino))
                mkdir($destino);

            $arquivo_importacao = fopen($arquivo, "a+");
            $linha = '';

            if (isset($data[0]['erro']) && !empty($data[0]['erro'])) {
                $linha .= utf8_decode('Nome da Unidade;Nome do Setor;Nome do Cargo;Matrícula do Funcionário;Nome do Funcionário;Data de Nascimento(dd/mm/aaaa);Sexo(F:Feminino, M:Masculino);Situação Cadastral(S:Ativo, F:Férias, A:Afastado, I:Inativo);Data de Admissão(dd/mm/aaaa);Data de Demissão(dd/mm/aaaa);Estado Civil(1:Solteiro, 2:Casado, 3:Separado, 4:Divorciado, 5:Viúvo, 6:Outros);Pis/Pasep;Rg;Estado RG;CPF;CTPS;Série CTPS;Endereço;Número;Complemento;Bairro;Cidade;Estado;Cep;Possui Deficiência(S:Sim, N:Não);Código CBO;Código GFIP;Centro Custo;Data da Último ASO(dd/mm/aaaa);Aptidão(A:Apto, I:Inapto);Turno;Descrição Detalhada do Cargo;Celular do Funcionário((ddd)+número telefone);Autoriza envio de SMS ao funcionário;E-mail do Funcionário;Autoriza envio de e-mail ao funcionário;Contato do responsável da Unidade;Telefone do responsável da Unidade((ddd)+número telefone);E-maildo responsável da Unidade;Endereço da Unidade;Número da Unidade;Complemento da Unidade;Bairro da Unidade;Cidade da Unidade;Estado da Unidade;Cep da Unidade;CNPJ da Unidade;Inscrição Estadual;Inscrição Municipal;Cnae;Grau de risco;Razão Social Unidade;Unidade de Negócio;Regime Tributário(1:Simples Nacional, 2:Simples Nacional, excesso sublimite de receita bruta, 3:Regime Normal);Código Externo; Tipo Unidade(F: Fiscal, O: Operacional);Erros;') . "\n";
            } else {
                $linha .= utf8_decode('Nome da Unidade;Nome do Setor;Nome do Cargo;Matrícula do Funcionário;Nome do Funcionário;Data de Nascimento(dd/mm/aaaa);Sexo(F:Feminino, M:Masculino);Situação Cadastral(S:Ativo, F:Férias, A:Afastado, I:Inativo);Data de Admissão(dd/mm/aaaa);Data de Demissão(dd/mm/aaaa);Estado Civil(1:Solteiro, 2:Casado, 3:Separado, 4:Divorciado, 5:Viúvo, 6:Outros);Pis/Pasep;Rg;Estado RG;CPF;CTPS;Série CTPS;Endereço;Número;Complemento;Bairro;Cidade;Estado;Cep;Possui Deficiência(S:Sim, N:Não);Código CBO;Código GFIP;Centro Custo;Data da Último ASO(dd/mm/aaaa);Aptidão(A:Apto, I:Inapto);Turno;Descrição Detalhada do Cargo;Celular do Funcionário((ddd)+número telefone);Autoriza envio de SMS ao funcionário;E-mail do Funcionário;Autoriza envio de e-mail ao funcionário;Contato do responsável da Unidade;Telefone do responsável da Unidade((ddd)+número telefone);E-maildo responsável da Unidade;Endereço da Unidade;Número da Unidade;Complemento da Unidade;Bairro da Unidade;Cidade da Unidade;Estado da Unidade;Cep da Unidade;CNPJ da Unidade;Inscrição Estadual;Inscrição Municipal;Cnae;Grau de risco;Razão Social Unidade;Unidade de Negócio;Regime Tributário(1:Simples Nacional, 2:Simples Nacional, excesso sublimite de receita bruta, 3:Regime Normal);Código Externo; Tipo Unidade(F: Fiscal, O: Operacional);') . "\n";
            }

            for ($chave = 0; $chave < count($data); $chave++) {
                $linha .= $data[$chave]['Unidade']['nome_fantasia'] . ';';
                $linha .= $data[$chave]['Unidade']['setor_descricao'] . ';'; //setor
                $linha .= $data[$chave]['Unidade']['cargo_descricao'] . ';'; //cargo
                $linha .= $data[$chave]['Funcionario']['matricula'] . ';';
                $linha .= $data[$chave]['Funcionario']['nome'] . ';';
                $linha .= $data[$chave]['Funcionario']['data_nascimento'] . ';';
                $linha .= $data[$chave]['Funcionario']['sexo'] . ';';

                switch ($data[$chave]['Funcionario']['status']) {
                    case '0':
                        $status = "I";
                        break;
                    case '1':
                        $status = "S";
                        break;
                    case '2':
                        $status = "F";
                        break;
                    case '3':
                        $status = "A";
                        break;
                    default:
                        $status = '';
                        break;
                }

                $linha .= $status . ';';
                $linha .= $data[$chave]['Funcionario']['data_admissao'] . ';';
                $linha .= $data[$chave]['Funcionario']['data_demissao'] . ';';
                $linha .= $data[$chave]['Funcionario']['estado_civil'] . ';';
                $linha .= $data[$chave]['Funcionario']['nit'] . ';';
                $linha .= $data[$chave]['Funcionario']['rg'] . ';';
                $linha .= $data[$chave]['Funcionario']['uf_rg'] . ';';
                $linha .= $data[$chave]['Funcionario']['cpf'] . ';';

                $linha .= $data[$chave]['Funcionario']['ctps'] . ';';
                $linha .= $data[$chave]['Funcionario']['serie_ctps'] . ';';
                $linha .= $data[$chave]['Funcionario']['endereco_completo_funcionario'] . ';';
                $linha .= Comum::soNumero($data[$chave]['Funcionario']['numero_funcionario']) . ';';
                $linha .= $data[$chave]['Funcionario']['complemento_funcionario'] . ';';
                $linha .= $data[$chave]['Funcionario']['bairro_funcionario'] . ';';
                $linha .= $data[$chave]['Funcionario']['cidade_funcionario'] . ';';
                $linha .= $data[$chave]['Funcionario']['estado_funcionario'] . ';';
                $linha .= $data[$chave]['Funcionario']['cep_funcionario'] . ';';

                switch ($data[$chave]['Funcionario']['deficiencia']) {
                    case '0':
                        $deficiencia = "N";
                        break;
                    case '1':
                        $deficiencia = "S";
                        break;
                    default:
                        $deficiencia = "";
                        break;
                }

                $linha .= $deficiencia . ';';
                $linha .= $data[$chave]['Funcionario']['cbo'] . ';';
                $linha .= $data[$chave]['Funcionario']['codigo_gfip'] . ';';
                $linha .= $data[$chave]['Funcionario']['centro_custo'] . ';';
                $linha .= $data[$chave]['Funcionario']['data_ultima_aso'] . ';';
                switch ($data[$chave]['Funcionario']['aptidao']) {
                    case '0':
                        $aptidao = "I";
                        break;
                    case '1':
                        $aptidao = "A";
                        break;
                    default:
                        $aptidao = "";
                        break;
                }

                $linha .= $aptidao . ';';
                $linha .= $data[$chave]['Funcionario']['turno'] . ';';
                $linha .= $data[$chave]['Funcionario']['descricao_cargo'] . ';';
                $linha .= $data[$chave]['Funcionario']['telefone_funcionario'] . ';';
                $linha .= $data[$chave]['Funcionario']['autoriza_envio_sms'] . ';';
                $linha .= $data[$chave]['Funcionario']['email_funcionario'] . ';';
                $linha .= $data[$chave]['Funcionario']['autoriza_envio_email'] . ';';

                $linha .= $data[$chave]['Unidade']['contato_responsavel'] . ';';
                $linha .= $data[$chave]['Unidade']['telefone_responsavel'] . ';';
                $linha .= $data[$chave]['Unidade']['email_responsavel'] . ';';
                $linha .= $data[$chave]['Unidade']['endereco_completo_unidade'] . ';';
                $linha .= Comum::soNumero($data[$chave]['Unidade']['numero_unidade']) . ';';
                $linha .= $data[$chave]['Unidade']['complemento_unidade'] . ';';
                $linha .= $data[$chave]['Unidade']['bairro_unidade'] . ';';
                $linha .= $data[$chave]['Unidade']['cidade_unidade'] . ';';
                $linha .= $data[$chave]['Unidade']['estado_unidade'] . ';';
                $linha .= $data[$chave]['Unidade']['cep_unidade'] . ';';
                $linha .= $data[$chave]['Unidade']['cnpj'] . ';';

                $linha .= $data[$chave]['Unidade']['inscricao_estadual'] . ';';
                $linha .= $data[$chave]['Unidade']['inscricao_municipal'] . ';';
                $linha .= Comum::soNumero($data[$chave]['Unidade']['cnae']) . ';';
                $linha .= $data[$chave]['Unidade']['grau_risco'] . ';';
                $linha .= $data[$chave]['Unidade']['razao_social'] . ';';
                $linha .= $data[$chave]['Funcionario']['unidade_negocio'] . ';';
                $linha .= $data[$chave]['Unidade']['regime_tributario'] . ';';
                $linha .= $data[$chave]['Unidade']['codigo_externo'] . ';';
                $linha .= $data[$chave]['Unidade']['tipo_unidade'] . ';';

                if (isset($data[$chave]['erro']) && !empty($data[$chave]['erro'])) {
                    $linha .= utf8_decode($data[$chave]['erro']) . ';';
                }

                $linha .= "\n";
            }

            fwrite($arquivo_importacao, $linha . "\r\n");
            fclose($arquivo_importacao);
        }
    }

    function abre_arquivo($nome_arquivo, $local)
    {
        $this->render(false, false);
        $nome_arquivo = $nome_arquivo;

        // debug('local');debug($local);
        // debug('mapeamento');
        $arquivo = APP . 'tmp' . DS . $local . DS . $nome_arquivo;
        // debug($arquivo);exit;
        set_time_limit(0);
        if (!empty($arquivo)) {
            if (file_get_contents($arquivo)) {
                Configure::write('debug', 0);
                header("Content-Type: application/force-download");
                header('Content-Disposition: attachment; filename="' . $nome_arquivo . '"');
                echo file_get_contents($arquivo);
                // unlink($arquivo);
                die();
            }
        }
    }

    function importar_ppra($codigo_cliente)
    { //codigo_cliente -> GrupoEconomico.)
        $this->pageTitle = 'Importação PGR';
        if (!isset($codigo_cliente) && empty($codigo_cliente)) {
            $this->redirect('/');
        }

        if ($this->RequestHandler->isPost()) {
            set_time_limit(0);
            if (!empty($this->params['data']['Importar']['arquivo']['name'])) {
                if (preg_match('@\.(csv)$@i', $this->params['data']['Importar']['arquivo']['name'])) {
                    $retorno = $this->Importar->importar_ppra($this->data);

                    if (!empty($retorno)) {

                        if (!empty($retorno['Erro'])) {

                            // debug($retorno['Erro']);

                            $key = 0;

                            foreach ($retorno['Erro'] as $linha => $dados) {
                                $var_erro = "";
                                foreach ($dados['erros'] as $tipo_erros => $erros) {
                                    if (is_array($erros)) {
                                        foreach ($erros as $campo => $erro) {
                                            //implementado mais um nivel porque esta saindo com array no arquivo de erros
                                            if (is_array($erro)) {
                                                foreach ($erro as $err) {
                                                    $var_erro .= $err . '|';
                                                }
                                            } else {
                                                $var_erro .= $erro . '|';
                                            }
                                        }
                                    } else {
                                        $var_erro .= $erros . '|';
                                    }
                                    // $var_erro = substr($var_erro, 0, strlen($var_erro)-1);
                                }

                                $array_dados_erros[$key] = array_merge($dados['dados'], array('erro' => $var_erro));
                                $key++;
                            }

                            $nome_arquivo_erro = date('YmdHis') . 'E.csv';

                            $this->gravaArquivoPpra($array_dados_erros, $nome_arquivo_erro);
                        } else {
                            $array_dados_erros = array();
                            $nome_arquivo_erro = '';
                        }

                        if (!empty($retorno['Sucesso'])) {
                            $key = 0;

                            foreach ($retorno['Sucesso'] as $linha => $dados) {
                                $var_sucesso = "";

                                $array_dados_sucesso[$key] = $dados['dados'];
                                $key++;
                            }

                            $nome_arquivo_sucesso = date('YmdHis') . 'S.csv';

                            $this->gravaArquivoPpra($array_dados_sucesso, $nome_arquivo_sucesso);
                        } else {
                            $array_dados_sucesso = array();
                            $nome_arquivo_sucesso = '';
                        }

                        $erros = count($array_dados_erros);
                        $sucesso = count($array_dados_sucesso);
                        $total = ($erros + $sucesso);

                        $dados_arquivo = array(
                            'nome_arquivo' => $this->data['Importar']['arquivo']['name'],
                            'erros' => $erros,
                            'nome_arquivo_erro' => $nome_arquivo_erro,
                            'sucesso' => $sucesso,
                            'nome_arquivo_sucesso' => $nome_arquivo_sucesso,
                            'total' => $total
                        );
                    }
                } else {
                    $this->Importar->invalidate('arquivo', 'Extensão inválida!');
                }
            } else {
                $this->Importar->invalidate('arquivo', 'Arquivo não enviado!');
            }
        }
        $this->set(compact('codigo_cliente', 'dados_arquivo', 'nome_arquivo_sucesso', 'nome_arquivo_erro'));
    }

    private function gravaArquivoPpra($data, $arquivo_nome)
    {
        if (!empty($data)) {
            set_time_limit(0);
            $destino = APP . 'tmp' . DS . 'importacao_dados_ppra' . DS;
            // $destino = DIR_ARQUIVOS.'importacao_dados_ppra'.DS;
            $arquivo = $destino . $arquivo_nome;

            // cria diretorio
            if (!is_dir($destino))
                mkdir($destino);

            $arquivo_importacao = fopen($arquivo, "a+");
            $linha = '';

            if (isset($data[0]['erro']) && !empty($data[0]['erro'])) {
                $linha .= 'Razão Social da Unidade;Nome Fantasia da Unidade;Código Externo Unidade;Nome do Setor;Nome do Cargo;Nome do Funcionário;CPF do Funcionário;Tipo do PGR(1:Individual, 2:Individual por Funcionário, 3:Por Grupo Homogêneo);Nome do Grupo Homogêneo;Data da Vistoria;Pé Direito do Setor (3 Metros,Menor que 3 Metros,Maior que 3 Metros,Outros);Iluminação do Setor (Natural,Natural + Artificial (Florescentes),Natural + Artificial (Incandecentes),Natural + Artificial (Led),Natural + Artificial (Croica),Artificial (Florescentes),Artificial (Incandecentes),Artificial (Led),Artificial (Croica),Outros);Cobertura do Setor (Laje,Laje + Forro,Telhas Metálicas,Telhas Fibrocimento,Outros);Estrutura do Setor (Alvenaria,Concreto,Metálico,Madeira,Fechamento Lateral,Outros);Ventilação do Setor (Natural,Natural + Ventiladores,Natural + Ar Condicionado Local,Natural + Ar condicionado Central,Ar Condicionado Central,Outros);Piso do Setor (Industrial com revestimento, Industrial sem revestimento,Carpete de Madeira,Cerâmico,Outros);Observação;Descrição das Atividades;Medidas de Controle;Nome Funcionário (Entrevistado);Nome Funcionário (Entrevistado Terceiro);Data Início Vigência Grupo Exposição;Risco (Descrição);Fonte Geradora (Descrição);Efeito Crítico (Não Aplica,Leve,Moderado,Sério,Severo);Meio de Propagação (Ar,Contato,Ar / Contato);Tipo do Tempo de Exposicao (P: PERMANENTE,I: INTERMITENTE,O: OCASIONAL);Minutos;Jornada;Descanso;Intensidade (B: BAIXA,M: MÉDIA,A: ALTA,MA: MUITO ALTA);Exposição Resultante(I:IRRELEVANTE,A:DE ATENÇÃO,C:CRÍTICA,IN:INCERTA);Potencial de Dano (L: LEVE,B: BAIXO,M: MÉDIO,A: ALTO,I: IMINENTE);Grau de Risco (AC: ACEITÁVEL,M: MODERADO,A: ALTO,MA: MUITO ALTO);Tipo de Medição (1: Quantitativo, 2: Qualitativo);Dosimetria;Avaliação Instantanea;Técnica de Medição (º C,kgf/cm²,dB(A),dB(C),m/s,mSvMHz ou GHz);Valor Máximo;Valor Medido;Descanso no Local;Descanso TBN;Descanso TBS;Descanso TBG;Carga Solar;Trabalho TBN;Trabalho TBS;Trabalho TBG;EPI (Descrição);EPC (Descrição);CNPJ Fornecedor;Data Inicio Vigência Versão;Vigência Contrato (3, 6, 9, 12 Meses);Número do Conselho;Conselho Profissional(CREA/MTE);Conselho Estado(UF);Erros;' . "\n";
            } else {
                $linha .= 'Razão Social da Unidade;Nome Fantasia da Unidade;Código Externo Unidade;Nome do Setor;Nome do Cargo;Nome do Funcionário;CPF do Funcionário;Tipo do PGR(1:Individual, 2:Individual por Funcionário, 3:Por Grupo Homogêneo);Nome do Grupo Homogêneo;Data da Vistoria;Pé Direito do Setor (3 Metros,Menor que 3 Metros,Maior que 3 Metros,Outros);Iluminação do Setor (Natural,Natural + Artificial (Florescentes),Natural + Artificial (Incandecentes),Natural + Artificial (Led),Natural + Artificial (Croica),Artificial (Florescentes),Artificial (Incandecentes),Artificial (Led),Artificial (Croica),Outros);Cobertura do Setor (Laje,Laje + Forro,Telhas Metálicas,Telhas Fibrocimento,Outros);Estrutura do Setor (Alvenaria,Concreto,Metálico,Madeira,Fechamento Lateral,Outros);Ventilação do Setor (Natural,Natural + Ventiladores,Natural + Ar Condicionado Local,Natural + Ar condicionado Central,Ar Condicionado Central,Outros);Piso do Setor (Industrial com revestimento, Industrial sem revestimento,Carpete de Madeira,Cerâmico,Outros);Observação;Descrição das Atividades;Medidas de Controle;Nome Funcionário (Entrevistado);Nome Funcionário (Entrevistado Terceiro);Data Início Vigência Grupo Exposição;Risco (Descrição);Fonte Geradora (Descrição);Efeito Crítico (Não Aplica,Leve,Moderado,Sério,Severo);Meio de Propagação (Ar,Contato,Ar / Contato);Tipo do Tempo de Exposicao (P: PERMANENTE,I: INTERMITENTE,O: OCASIONAL);Minutos;Jornada;Descanso;Intensidade (B: BAIXA,M: MÉDIA,A: ALTA,MA: MUITO ALTA);Exposição Resultante(I:IRRELEVANTE,A:DE ATENÇÃO,C:CRÍTICA,IN:INCERTA);Potencial de Dano (L: LEVE,B: BAIXO,M: MÉDIO,A: ALTO,I: IMINENTE);Grau de Risco (AC: ACEITÁVEL,M: MODERADO,A: ALTO,MA: MUITO ALTO);Tipo de Medição (1: Quantitativo, 2: Qualitativo);Dosimetria;Avaliação Instantanea;Técnica de Medição (º C,kgf/cm²,dB(A),dB(C),m/s,mSvMHz ou GHz);Valor Máximo;Valor Medido;Descanso no Local;Descanso TBN;Descanso TBS;Descanso TBG;Carga Solar;Trabalho TBN;Trabalho TBS;Trabalho TBG;EPI (Descrição);EPC (Descrição);CNPJ Fornecedor;Data Inicio Vigência Versão;Vigência Contrato (3, 6, 9, 12 Meses);Número do Conselho;Conselho Profissional(CREA/MTE);Conselho Estado(UF);' . "\n";
            }
            $linha = utf8_decode($linha);

            for ($chave = 0; $chave < count($data); $chave++) {
                $linha .= utf8_decode($data[$chave]['razao_social']) . ';';
                $linha .= utf8_decode($data[$chave]['nome_fantasia']) . ';';
                $linha .= utf8_decode($data[$chave]['codigo_externo']) . ';';
                $linha .= utf8_decode($data[$chave]['setor_descricao']) . ';';
                $linha .= utf8_decode($data[$chave]['cargo_descricao']) . ';';
                $linha .= $data[$chave]['nome_funcionario'] . ';';
                $linha .= ($data[$chave]['cpf_funcionario'] == '00000000000') ? ';' : $data[$chave]['cpf_funcionario'] . ';';
                $linha .= $data[$chave]['tipo_ppra'] . ';';
                $linha .= $data[$chave]['nome_ghe'] . ';';
                $linha .= $data[$chave]['data_vistoria'] . ';';
                $linha .= $data[$chave]['pe_direito_setor'] . ';';
                $linha .= $data[$chave]['iluminacao_setor'] . ';';
                $linha .= $data[$chave]['cobertura_setor'] . ';';
                $linha .= $data[$chave]['estrutura_setor'] . ';';
                $linha .= $data[$chave]['ventilacao_setor'] . ';';
                $linha .= $data[$chave]['piso_setor'] . ';';
                $linha .= $data[$chave]['observacao'] . ';';
                $linha .= $data[$chave]['descricao_cargo'] . ';';
                $linha .= $data[$chave]['medidas_controle'] . ';';
                $linha .= $data[$chave]['funcionario_entrevistado'] . ';';
                $linha .= $data[$chave]['funcionario_entrevistado_terceiro'] . ';';
                $linha .= $data[$chave]['data_inicio_vigencia'] . ';';
                $linha .= utf8_decode($data[$chave]['risco']) . ';';
                $linha .= $data[$chave]['fonte_geradora'] . ';';
                $linha .= $data[$chave]['efeito_critico'] . ';';
                $linha .= $data[$chave]['meio_exposicao'] . ';';
                $linha .= $data[$chave]['tipo_tempo'] . ';';
                $linha .= $data[$chave]['minutos'] . ';';
                $linha .= $data[$chave]['jornada'] . ';';
                $linha .= $data[$chave]['descanso'] . ';';
                $linha .= $data[$chave]['intensidade'] . ';';
                $linha .= $data[$chave]['resultante'] . ';';
                $linha .= $data[$chave]['dano'] . ';';
                $linha .= $data[$chave]['grau_risco'] . ';';
                $linha .= $data[$chave]['codigo_tipo_medicao'] . ';';
                $linha .= $data[$chave]['dosimetria'] . ';';
                $linha .= $data[$chave]['avaliacao_instantanea'] . ';';
                $linha .= $data[$chave]['codigo_tecnica_medicao'] . ';';
                $linha .= $data[$chave]['valor_maximo'] . ';';
                $linha .= $data[$chave]['valor_medido'] . ';';
                $linha .= $data[$chave]['descanso_no_local'] . ';';
                $linha .= $data[$chave]['descanso_tbn'] . ';';
                $linha .= $data[$chave]['descanso_tbs'] . ';';
                $linha .= $data[$chave]['descanso_tbg'] . ';';
                $linha .= $data[$chave]['carga_solar'] . ';';
                $linha .= $data[$chave]['trabalho_tbn'] . ';';
                $linha .= $data[$chave]['trabalho_tbs'] . ';';
                $linha .= $data[$chave]['trabalho_tbg'] . ';';
                $linha .= $data[$chave]['epi'] . ';';
                $linha .= $data[$chave]['epc'] . ';';
                $linha .= $data[$chave]['documento_fornecedor'] . ';';
                $linha .= $data[$chave]['data_inicio_vigencia_contrato'] . ';';
                $linha .= $data[$chave]['vigencia_contrato'] . ';';
                $linha .= $data[$chave]['numero_conselho_medico_contrato'] . ';';
                $linha .= $data[$chave]['conselho_medico_contrato'] . ';';
                $linha .= $data[$chave]['uf_conselho_medico_contrato'] . ';';

                if (isset($data[$chave]['erro']) && !empty($data[$chave]['erro'])) {
                    $linha .= utf8_decode($data[$chave]['erro']) . ';';
                }

                $linha .= "\n";
            }
            // $linha = utf8_encode($linha);
            fwrite($arquivo_importacao, $linha . "\r\n");
            fclose($arquivo_importacao);
        }
    }

    function importar_pcmso($codigo_cliente)
    { //codigo_cliente -> GrupoEconomico.)
        $this->pageTitle = 'Importação PCMSO';
        if (!isset($codigo_cliente) && empty($codigo_cliente)) {
            $this->redirect('/');
        }
        //Inclusão de Funcionário
        if ($this->RequestHandler->isPost()) {
            set_time_limit(0);
            if (!empty($this->params['data']['Importar']['arquivo']['name'])) {
                if (preg_match('@\.(csv)$@i', $this->params['data']['Importar']['arquivo']['name'])) {
                    $retorno = $this->Importar->importar_pcmso($this->data);

                    if (!empty($retorno)) {

                        if (!empty($retorno['Erro'])) {
                            $key = 0;

                            foreach ($retorno['Erro'] as $linha => $dados) {
                                $var_erro = "";

                                foreach ($dados['erros'] as $tipo_erros => $erros) {

                                    foreach ($erros as $campo => $erro) {
                                        $var_erro .= $erro . '|';
                                    }
                                    // $var_erro = substr($var_erro, 0, strlen($var_erro)-1);
                                }

                                $array_dados_erros[$key] = array_merge($dados['dados'], array('erro' => utf8_encode($var_erro)));
                                $key++;
                            }

                            $nome_arquivo_erro = date('YmdHis') . 'E.csv';

                            $this->gravaArquivoPcmso($array_dados_erros, $nome_arquivo_erro);
                        } else {
                            $array_dados_erros = array();
                            $nome_arquivo_erro = '';
                        }

                        if (!empty($retorno['Sucesso'])) {
                            $key = 0;

                            foreach ($retorno['Sucesso'] as $linha => $dados) {
                                $var_sucesso = "";

                                $array_dados_sucesso[$key] = $dados['dados'];
                                $key++;
                            }

                            $nome_arquivo_sucesso = date('YmdHis') . 'S.csv';

                            $this->gravaArquivoPcmso($array_dados_sucesso, $nome_arquivo_sucesso);
                        } else {
                            $array_dados_sucesso = array();
                            $nome_arquivo_sucesso = '';
                        }

                        $erros = count($array_dados_erros);
                        $sucesso = count($array_dados_sucesso);
                        $total = ($erros + $sucesso);

                        $dados_arquivo = array(
                            'nome_arquivo' => $this->data['Importar']['arquivo']['name'],
                            'erros' => $erros,
                            'nome_arquivo_erro' => $nome_arquivo_erro,
                            'sucesso' => $sucesso,
                            'nome_arquivo_sucesso' => $nome_arquivo_sucesso,
                            'total' => $total
                        );
                    }
                } else {
                    $this->Importar->invalidate('arquivo', 'Extensão inválida!');
                }
            } else {
                $this->Importar->invalidate('arquivo', 'Arquivo não enviado!');
            }
        }

        $dados_cliente = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);

        $this->data['Matriz'] = $dados_cliente['Matriz'];
        $this->set(compact('codigo_cliente', 'dados_arquivo', 'nome_arquivo_sucesso', 'nome_arquivo_erro'));
    }

    private function gravaArquivoPcmso($data, $arquivo_nome)
    {
        if (!empty($data)) {
            set_time_limit(0);

            $destino = APP . 'tmp' . DS . 'importacao_dados_pcmso' . DS;
            $arquivo = $destino . $arquivo_nome;

            // cria diretorio
            if (!is_dir($destino))
                mkdir($destino);

            $arquivo_importacao = fopen($arquivo, "a+");
            $linha = '';

            if (isset($data[0]['erro']) && !empty($data[0]['erro'])) {
                $linha .= 'Código Externo Unidade (*);Nome do Setor (*);Nome do Cargo (*);Exame (*);Periodicidade - Frequência (em Meses);Periodicidade - Após admissão;Aplicável em (A: Admissional, P: Periódico, D: Demissional, R: Retorno ao Trabalho, M: Mudança de Riscos Ocupacionais) - A|P|D|R|M;A partir de qual idade?;Solicitar este exame em quanto tempo?;A partir de qual idade? (2);Solicitar este exame em quanto tempo? (2);A partir de qual idade? (3);Solicitar este exame em quanto tempo? (3);A partir de qual idade? (4);Solicitar este exame em quanto tempo? (4);Objetivo do Exame(O: Ocupacional, Q: Qualidade de Vida) (*);Tipos de Exames (CE: Convocação Exames, PP: PPP, AS: ASO, PC: PCMSO, RA: Relatório Anual) -  CE|PP|AS|PC|RA;CNPJ Fornecedor;CPF Funcionario;Erros;' . "\n";

                // 'Razão Social da Unidade;Nome Fantasia da Unidade;Código Externo Unidade;Nome do Setor;Nome do Cargo;Exame;Período Frequência;Período após Admissão;Aplicavel Exame;Idade  do Exame;Tempo a Realizar o Exame;Tipo de Exame;Erros;'."\n";

            } else {
                $linha .= 'Código Externo Unidade (*);Nome do Setor (*);Nome do Cargo (*);Exame (*);Periodicidade - Frequência (em Meses);Periodicidade - Após admissão;Aplicável em (A: Admissional, P: Periódico, D: Demissional, R: Retorno ao Trabalho, M: Mudança de Riscos Ocupacionais) - A|P|D|R|M;A partir de qual idade?;Solicitar este exame em quanto tempo?;A partir de qual idade? (2);Solicitar este exame em quanto tempo? (2);A partir de qual idade? (3);Solicitar este exame em quanto tempo? (3);A partir de qual idade? (4);Solicitar este exame em quanto tempo? (4);Objetivo do Exame(O: Ocupacional, Q: Qualidade de Vida) (*);Tipos de Exames (CE: Convocação Exames, PP: PPP, AS: ASO, PC: PCMSO, RA: Relatório Anual) -  CE|PP|AS|PC|RA;CNPJ Fornecedor;CPF Funcionario' . "\n";
                // $linha .= 'Razão Social da Unidade;Nome Fantasia da Unidade;Código Externo Unidade;Nome do Setor;Nome do Cargo;Exame;Período Frequência;Período após Admissão;Aplicavel Exame;Idade  do Exame;Tempo a Realizar o Exame;Tipo de Exame;'."\n";
            }
            $linha = utf8_decode($linha);

            for ($chave = 0; $chave < count($data); $chave++) {
                $linha .= $data[$chave]['codigo_externo'] . ';';
                $linha .= $data[$chave]['setor_descricao'] . ';';
                $linha .= $data[$chave]['cargo_descricao'] . ';';
                $linha .= $data[$chave]['exame'] . ';';
                $linha .= $data[$chave]['periodo_frequencia'] . ';';
                $linha .= $data[$chave]['periodo_apos_admissao'] . ';';
                $linha .= $data[$chave]['momento_exame'] . ';';
                $linha .= $data[$chave]['idade'] . ';';
                $linha .= $data[$chave]['tempo'] . ';';
                $linha .= $data[$chave]['idade_2'] . ';';
                $linha .= $data[$chave]['tempo_2'] . ';';
                $linha .= $data[$chave]['idade_3'] . ';';
                $linha .= $data[$chave]['tempo_3'] . ';';
                $linha .= $data[$chave]['idade_4'] . ';';
                $linha .= $data[$chave]['tempo_4'] . ';';
                $linha .= $data[$chave]['objetivo'] . ';';
                $linha .= $data[$chave]['tipo_exame'] . ';';
                $linha .= $data[$chave]['documento_fornecedor'] . ';';
                $linha .= $data[$chave]['cpf_funcionario'] . ';';

                if (isset($data[$chave]['erro']) && !empty($data[$chave]['erro'])) {
                    $linha .= utf8_decode($data[$chave]['erro']) . ';';
                }

                $linha .= "\n";
            }
            fwrite($arquivo_importacao, $linha . "\r\n");
            fclose($arquivo_importacao);
        }
    }

    /*
     * Exporta planilha processada para importação de atestados, onde será gerado um arquivo em formato CSV e correção das inconsistências de atestados
     * Será efeito o download de um arquivo .CSV com os dados importados para processamento de atestados
     * @param $codigo_importacao_atestado int Código de Identificação da Importação de Atestados
     */
    function exportar_importacao_atestados_processada($codigo_importacao_atestado)
    {

        App::import('model', 'StatusImportacao');

        $this->loadModel('ImportacaoAtestadosRegistros');
        $fields = array(
            'ImportacaoAtestadosRegistros.codigo AS codigo',
            'ImportacaoAtestadosRegistros.nome_empresa AS nome_empresa',
            'ImportacaoAtestadosRegistros.nome_unidade AS nome_unidade',
            'ImportacaoAtestadosRegistros.nome_setor AS nome_setor',
            'ImportacaoAtestadosRegistros.nome_cargo AS nome_cargo',
            'ImportacaoAtestadosRegistros.matricula AS matricula',
            'ImportacaoAtestadosRegistros.cpf AS cpf',
            'ImportacaoAtestadosRegistros.tipo_atestado AS tipo_atestado',
            'ImportacaoAtestadosRegistros.sem_profissional AS sem_profissional',
            'ImportacaoAtestadosRegistros.codigo_medico AS codigo_medico',
            'ImportacaoAtestadosRegistros.medico_solicitante AS medico_solicitante',
            'ImportacaoAtestadosRegistros.conselho_classe AS conselho_classe',
            'ImportacaoAtestadosRegistros.UF AS UF',
            'ImportacaoAtestadosRegistros.sigla_conselho AS sigla_conselho',
            'ImportacaoAtestadosRegistros.especialidade AS especialidade',
            // 'ImportacaoAtestadosRegistros.especialidade2 AS especialidade2',
            'ImportacaoAtestadosRegistros.data_inicio_afastamento AS data_inicio_afastamento',
            'ImportacaoAtestadosRegistros.data_retorno_afastamento AS data_retorno_afastamento',
            'ImportacaoAtestadosRegistros.dias AS dias',
            'ImportacaoAtestadosRegistros.hora_inicio_afastamento AS hora_inicio_afastamento',
            'ImportacaoAtestadosRegistros.hora_termino_afastamento AS hora_termino_afastamento',
            'ImportacaoAtestadosRegistros.horas AS horas',
            'ImportacaoAtestadosRegistros.codigo_cid AS codigo_cid',
            'ImportacaoAtestadosRegistros.nome_cid AS nome_cid',
            'ImportacaoAtestadosRegistros.restricao_retorno AS restricao_retorno',
            'ImportacaoAtestadosRegistros.tabela_18_esocial AS tabela_18_esocial',
            'ImportacaoAtestadosRegistros.motivo_licenca AS motivo_licenca',
            'ImportacaoAtestadosRegistros.tipo_licenca AS tipo_licenca',
            'ImportacaoAtestadosRegistros.motivo_afastamento AS motivo_afastamento',
            'ImportacaoAtestadosRegistros.origem_retificacao AS origem_retificacao',
            'ImportacaoAtestadosRegistros.tipo_acidente_transito AS tipo_acidente_transito',
            'ImportacaoAtestadosRegistros.tipo_processo AS tipo_processo',
            'ImportacaoAtestadosRegistros.numero_processo AS numero_processo',
            'ImportacaoAtestadosRegistros.codigo_documento_entidade AS codigo_documento_entidade',
            'ImportacaoAtestadosRegistros.onus_remuneracao AS onus_remuneracao',
            'ImportacaoAtestadosRegistros.onus_requisicao AS onus_requisicao',
            "ImportacaoAtestadosRegistros.tp_acid_transito AS tp_acid_transito",
            "ImportacaoAtestadosRegistros.obs_afastamento AS obs_afastamento",
            "ImportacaoAtestadosRegistros.renumeracao_cargo AS renumeracao_cargo",
            "ImportacaoAtestadosRegistros.data_inicio_p_aquisitivo AS data_inicio_p_aquisitivo",
            "ImportacaoAtestadosRegistros.data_fim_p_aquisitivo AS data_fim_p_aquisitivo",
            'ImportacaoAtestadosRegistros.observacao AS observacao'


        );
        $conditions = array(
            'codigo_importacao_atestados' => $codigo_importacao_atestado,
            'OR' => array(
                'codigo_status_importacao <>' => StatusImportacao::PROCESSADO,
                'codigo_status_importacao' => null
            )
        );
        $query = $this->ImportacaoAtestadosRegistros->find('sql', compact('conditions', 'fields'));
        // debug($query);exit;
        $dbo = $this->ImportacaoAtestadosRegistros->getDataSource();
        $dbo->results = $dbo->rawQuery($query);
        // $dbo->results = $dbo->_execute($query);
        $nome_arquivo = date('YmdHis') . 'er.csv';
        $cabecalho_arquivo = '"Nome da Empresa";';
        $cabecalho_arquivo .= '"Nome da Unidade";';
        $cabecalho_arquivo .= '"Nome do Setor";';
        $cabecalho_arquivo .= '"Nome do Cargo";';
        $cabecalho_arquivo .= '"Matricula do Funcionario";';
        $cabecalho_arquivo .= '"CPF";';
        $cabecalho_arquivo .= '"Tipo";';
        $cabecalho_arquivo .= '"Atestado sem profissional médico?";';
        $cabecalho_arquivo .= '"Código do Profissional";';
        $cabecalho_arquivo .= '"Nome do Médico";';
        $cabecalho_arquivo .= '"Número do Conselho";';
        $cabecalho_arquivo .= '"Estado";';
        $cabecalho_arquivo .= '"Conselho";';
        $cabecalho_arquivo .= '"Especialidade do Médico";';
        $cabecalho_arquivo .= '"Início Afastamento";';
        $cabecalho_arquivo .= '"Retorno Afastamento";';
        $cabecalho_arquivo .= '"Dias";';
        $cabecalho_arquivo .= '"Hora Início Afastamento";';
        $cabecalho_arquivo .= '"Hora Final Afastamento";';
        $cabecalho_arquivo .= '"Horas";';
        $cabecalho_arquivo .= '"CID";';
        $cabecalho_arquivo .= '"Descrição CID";';
        $cabecalho_arquivo .= '"Restrição Retorno";';
        $cabecalho_arquivo .= '"Motivo Licença";';
        $cabecalho_arquivo .= '"Tipo de Afastamento";';
        $cabecalho_arquivo .= '"Motivo da Licença (Tabela 18 – eSocial)";';
        $cabecalho_arquivo .= '"Acidente de Transito?";';
        $cabecalho_arquivo .= '"Tipo acidente de transito";';
        $cabecalho_arquivo .= '"Afastamento decorre de mesmo motivo de afastamento anterior?";';
        $cabecalho_arquivo .= '"Origem da Retificação";';
        $cabecalho_arquivo .= '"Tipo processo";';
        $cabecalho_arquivo .= '"Numero processo";';
        $cabecalho_arquivo .= '"CNPJ";';
        $cabecalho_arquivo .= '"Ônus da remuneração";';
        $cabecalho_arquivo .= '"Ônus da cessăo/requisição";';
        $cabecalho_arquivo .= '"Observaçăo (eSocial)";';
        $cabecalho_arquivo .= '"Renumeração do Cargo";';
        $cabecalho_arquivo .= '"Data Início Período Aquisitivo";';
        $cabecalho_arquivo .= '"Data Fim Período Aquisitivo";';
        $cabecalho_arquivo .= '"Observação";';
        $cabecalho_arquivo .= "\n";
        ob_clean();
        header('Content-Encoding: ISO-8859-1');
        header('Content-type: text/csv; charset=ISO-8859-1');
        header(sprintf('Content-Disposition: attachment; filename="%s"', $nome_arquivo));
        header('Pragma: no-cache');
        echo utf8_decode($cabecalho_arquivo);
        while ($dado = $dbo->fetchRow()) {
            $dado = $dado[0];
            $linha = $dado['nome_empresa'] . ';';
            $linha .= '"' . $dado['nome_unidade'] . '";';
            $linha .= '"' . $dado['nome_setor'] . '";';
            $linha .= '"' . $dado['nome_cargo'] . '";';
            $linha .= '"' . $dado['matricula'] . '";';
            $linha .= '"' . $dado['cpf'] . '";';
            $linha .= '"' . $dado['tipo_atestado'] . '";';
            $linha .= '"' . $dado['sem_profissional'] . '";';
            $linha .= '"' . $dado['codigo_medico'] . '";';
            $linha .= '"' . $dado['medico_solicitante'] . '";';
            $linha .= '"' . $dado['conselho_classe'] . '";';
            $linha .= '"' . $dado['UF'] . '";';
            $linha .= '"' . $dado['sigla_conselho'] . '";';
            $linha .= '"' . $dado['especialidade'] . '";';
            $linha .= '"' . $dado['data_inicio_afastamento'] . '";';
            $linha .= '"' . $dado['data_retorno_afastamento'] . '";';
            $linha .= '"' . $dado['dias'] . '";';
            $linha .= '"' . $dado['hora_inicio_afastamento'] . '";';
            $linha .= '"' . $dado['hora_termino_afastamento'] . '";';
            $linha .= '"' . $dado['horas'] . '";';
            $linha .= '"' . $dado['codigo_cid'] . '";';
            $linha .= '"' . $dado['nome_cid'] . '";';
            $linha .= '"' . $dado['restricao_retorno'] . '";';
            $linha .= '"' . $dado['motivo_licenca'] . '";';
            $linha .= '"' . $dado['tipo_licenca'] . '";';
            $linha .= '"' . $dado['tabela_18_esocial'] . '";';
            $linha .= '"' . $dado['tp_acid_transito'] . '";';
            $linha .= '"' . $dado['tipo_acidente_transito'] . '";';
            $linha .= '"' . $dado['motivo_afastamento'] . '";';
            $linha .= '"' . $dado['origem_retificacao'] . '";';
            $linha .= '"' . $dado['tipo_processo'] . '";';
            $linha .= '"' . $dado['numero_processo'] . '";';
            $linha .= '"' . $dado['codigo_documento_entidade'] . '";';
            $linha .= '"' . $dado['onus_remuneracao'] . '";';
            $linha .= '"' . $dado['onus_requisicao'] . '";';
            $linha .= '"' . $dado['obs_afastamento'] . '";';
            $linha .= '"' . $dado['renumeracao_cargo'] . '";';
            $linha .= '"' . $dado['data_inicio_p_aquisitivo'] . '";';
            $linha .= '"' . $dado['data_fim_p_aquisitivo'] . '";';
            $linha .= '"' . $dado['observacao'] . '";';
            //joga no arquivo os dados
            echo utf8_decode($linha) . "\n";
        }
        //mata o metodo
        die();
    }
    /*
     * Exporta planilha processada para importação de atestados
     * Será efeito o download de um arquivo .CSV com os dados importados para processamento de atestados
     * @param $codigo_importacao_atestado int Código de Identificação da Importação de Atestados
     */
    function importar_atestado($codigo_cliente)
    { //codigo_cliente -> GrupoEconomico.)
        $this->loadModel('Cliente');
        $this->loadModel('GrupoEconomico');
        $this->loadModel('ImportacaoAtestados');
        $this->loadModel('ImportacaoAtestadosRegistros');
        $this->pageTitle = 'Importação Atestados';
        if (!isset($codigo_cliente) && empty($codigo_cliente)) {
            $this->redirect('/');
        }
        if (!empty($this->data)) {
            if (preg_match('@\.(csv)$@i', $this->data['ImportacaoAtestados']['nome_arquivo']['name'])) {
                $path_destino = APP . 'tmp' . DS;
                $arquivo_destino = $this->data['ImportacaoAtestados']['nome_arquivo']['name'];
                if (move_uploaded_file($this->data['ImportacaoAtestados']['nome_arquivo']['tmp_name'], $path_destino . $arquivo_destino)) {
                    if ($this->ImportacaoAtestados->incluir($path_destino, $arquivo_destino, $codigo_cliente)) {
                        $this->BSession->setFlash('save_success');
                    } else {
                        $error = $this->ImportacaoAtestados->invalidFields();
                        $this->BSession->setFlash(array(MSGT_ERROR, $error['codigo']));
                    }
                } else {
                    $this->BSession->setFlash('save_error');
                }
            } else {
                $this->Importar->invalidate('nome_arquivo', 'Extensão inválida!');
                $this->BSession->setFlash('save_error');
            }
        }

        $this->GrupoEconomico->bindModel(
            array('hasOne' => array('GrupoEconomicoCliente' => array('foreignKey' => 'codigo_grupo_economico'))),
            array('belongsTo' => array('Cliente' => array('foreignKey' => 'GrupoEconomicoCliente.codigo_cliente')))
        );

        $grupo_economico = $this->GrupoEconomico->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));

        $cliente = $this->Cliente->find('first', array('conditions' => array('codigo' => $codigo_cliente)));

        $grupo_economico['Cliente'] = $cliente['Cliente'];

        $arquivos_importados = $this->ImportacaoAtestados->getAtestadosListagem($grupo_economico['GrupoEconomico']['codigo']);

        $this->set(compact('arquivos_importados', 'grupo_economico'));
    }
    /*
     * Exclui planilha importada de atestados para processamento
     * Será excluídos os registros de um arquivo .CSV importados para processamento de atestados
     * @param $codigo_importacao_atestado int Código de Identificação da Importação de Atestados
     */
    function eliminar_importacao_atestado($codigo_cliente, $codigo_importacao_atestado)
    {

        $this->pageTitle = 'Exclusão de arquivo para importação de atestados'; //Titulo da página

        $this->loadModel('ImportacaoAtestados');

        if ($this->RequestHandler->isPost()) {
            $atestados = $this->ImportacaoAtestados->carregar($codigo_importacao_atestado);
            if ($atestados['ImportacaoAtestados']['codigo_status_importacao'] == StatusImportacao::SEM_PROCESSAR) {
                if ($this->ImportacaoAtestados->excluir($codigo_importacao_atestado)) {
                    $this->BSession->setFlash('delete_success');
                    $this->redirect(array('action' => 'importar_atestado', $codigo_cliente));
                } else {
                    $this->BSession->setFlash('delete_error');
                }
            } else {
                $this->BSession->setFlash('delete_error_imp_em_proc');
                $this->redirect(array('action' => 'importar_atestado', $codigo_cliente));
            }
        }

        $atestados = $this->ImportacaoAtestados->getAtestados($codigo_importacao_atestado);

        $this->set(compact('atestados'));
    }
    /**
     * Método de chamada da tela de gerenciamento dos arquivos importados para geração de atestados médicos
     * @param type $codigo_cliente Código de identificação do cliente onde será gerados os atestados médicos importados
     * @param type $codigo_importacao_atestado Código de Identificação do arquivo importado
     * @return avoid
     */
    function gerenciar_importacao_atestados($codigo_cliente, $codigo_importacao_atestado)
    {
        $this->pageTitle = 'Gerenciar Arquivo para Importação de Atestados';
        $this->loadModel('ImportacaoAtestados');
        if ($this->RequestHandler->isPost()) {
            // pr(ROOT . '/cake/console/cake -app '. ROOT . DS . 'app importacao atestados '."{$_SESSION['Auth']['Usuario']['codigo_empresa']} {$_SESSION['Auth']['Usuario']['codigo']} {$codigo_importacao_atestado}");exit;
            Comum::execInBackground(ROOT . '/cake/console/cake -app ' . ROOT . DS . 'app importacao atestados ' . "{$_SESSION['Auth']['Usuario']['codigo_empresa']} {$_SESSION['Auth']['Usuario']['codigo']} {$codigo_importacao_atestado}");
            $this->redirect(array('action' => 'importar_atestado', $codigo_cliente));
        }
        $atestados = $this->ImportacaoAtestados->carregar($codigo_importacao_atestado);
        $this->set(compact('atestados'));
    }

    /**
     * Método de chamada da tela de gerenciamento das validações de dados de um arquivo importado
     * @param type $codigo_cliente Código de identificação do cliente onde será gerados os atestados médicos importados
     * @param type $codigo_importacao_atestado Código de Identificação do arquivo importado
     * @return avoid
     */
    function importacao_atestados_listagem($codigo_importacao_atestado)
    {
        $this->loadModel('ImportacaoAtestados');
        $this->loadModel('ImportacaoAtestadosRegistros');
        $conditions = array(
            'codigo_importacao_atestados' => $codigo_importacao_atestado,
        );
        $this->paginate['ImportacaoAtestadosRegistros'] = array(
            'conditions' => $conditions,
            'limit' => 100,
            'order' => 'nome_funcionario',
            'extra' => array('importacao' => true)
        );
        $registros = $this->paginate('ImportacaoAtestadosRegistros');
        $importacao_atestados = $this->ImportacaoAtestados->carregar($codigo_importacao_atestado);
        $codigo_status_importacao = $importacao_atestados['ImportacaoAtestados']['codigo_status_importacao'];

        $validaCadastrosRegistro = $this->ImportacaoAtestadosRegistros->alertasRegistrosCadastros($registros, $codigo_status_importacao);

        $alertas = $validaCadastrosRegistro['alertas'];
        $depara = $this->ImportacaoAtestadosRegistros->depara();
        $titulos = $this->ImportacaoAtestadosRegistros->titulos();
        $validacoes = $validaCadastrosRegistro['validacoes'];
        $this->set(compact('registros', 'alertas', 'depara', 'titulos', 'validacoes', 'codigo_status_importacao'));
    }

    /*
     * Exporta planilha processada para importação de pedidos de exame, onde será gerado um arquivo em formato CSV e correção das
     * inconsistências de pedidos de exame. Será efeito o download de um arquivo .CSV com os dados importados para processamento de pedidos de exame
     * @param $codigo_importacao_pedido_exame int Código de Identificação da Importação de Pedidos de Exame
     */
    function exportar_importacao_pedidos_exame_processada($codigo_importacao_pedido_exame)
    {
        App::import('model', 'StatusImportacao');
        $this->loadModel('ImportacaoPedidosExamesRegistros');
        $fields = array(
            'ImportacaoPedidosExamesRegistros.codigo AS codigo',
            'ImportacaoPedidosExamesRegistros.nome_empresa AS nome_empresa',
            'ImportacaoPedidosExamesRegistros.nome_unidade AS nome_unidade',
            'ImportacaoPedidosExamesRegistros.nome_setor AS nome_setor',
            'ImportacaoPedidosExamesRegistros.nome_cargo AS nome_cargo',
            'ImportacaoPedidosExamesRegistros.cpf AS cpf',
            'ImportacaoPedidosExamesRegistros.data_solicitacao AS data_solicitacao',
            'ImportacaoPedidosExamesRegistros.tipo_item_pedido AS tipo_item_pedido',
            'ImportacaoPedidosExamesRegistros.nome_exame AS nome_exame',
            'ImportacaoPedidosExamesRegistros.tipo_exame AS tipo_exame',
            'ImportacaoPedidosExamesRegistros.fornecedor AS fornecedor',
            'ImportacaoPedidosExamesRegistros.data_realizacao AS data_realizacao',
            'ImportacaoPedidosExamesRegistros.resultado_exame AS resultado_exame',
            'ImportacaoPedidosExamesRegistros.resultado_observacao AS resultado_observacao',
            'ImportacaoPedidosExamesRegistros.observacao AS observacao'
        );
        $conditions = array(
            'codigo_importacao_pedidos_exames' => $codigo_importacao_pedido_exame,
            'OR' => array(
                'codigo_status_importacao <>' => StatusImportacao::PROCESSADO,
                'codigo_status_importacao' => null
            )
        );
        $query = $this->ImportacaoPedidosExamesRegistros->find('sql', compact('conditions', 'fields'));
        $dbo = $this->ImportacaoPedidosExamesRegistros->getDataSource();
        ini_set('max_execution_time', 0);
        ini_set('max_input_time', 0);
        $dbo->results = $dbo->_execute($query);
        $nome_arquivo = date('YmdHis') . 'er.csv';
        $cabecalho_arquivo = '"Nome da Empresa";';
        $cabecalho_arquivo .= '"Nome da Unidade";';
        $cabecalho_arquivo .= '"Nome do Setor";';
        $cabecalho_arquivo .= '"Nome do Cargo";';
        $cabecalho_arquivo .= '"CPF";';
        $cabecalho_arquivo .= '"Data de Solicitação";';
        $cabecalho_arquivo .= '"Item (OCUPACIONAL ou QUALIDADE DE VIDA)";';
        $cabecalho_arquivo .= '"Nome Exame";';
        $cabecalho_arquivo .= '"Sigla Conselho";';
        $cabecalho_arquivo .= '"Prestador de Serviço";';
        $cabecalho_arquivo .= '"Data Realização Exame";';
        $cabecalho_arquivo .= '"Resultado (ALTERADO ou SEM ALTERAÇÃO APARENTE)";';
        $cabecalho_arquivo .= '"Observação Resultado";';
        $cabecalho_arquivo .= '"Observação";';
        $cabecalho_arquivo .= "\n";
        ob_clean();
        header('Content-Encoding: ISO-8859-1');
        header('Content-type: text/csv; charset=ISO-8859-1');
        header(sprintf('Content-Disposition: attachment; filename="%s"', $nome_arquivo));
        header('Pragma: no-cache');
        echo utf8_decode($cabecalho_arquivo);
        while ($dado = $dbo->fetchRow()) {
            $dado = $dado[0];
            $linha = $dado['nome_empresa'] . ';';
            $linha .= '"' . $dado['nome_unidade'] . '";';
            $linha .= '"' . $dado['nome_setor'] . '";';
            $linha .= '"' . $dado['nome_cargo'] . '";';
            $linha .= '"' . $dado['cpf'] . '";';
            $linha .= '"' . $dado['data_solicitacao'] . '";';
            $linha .= '"' . $dado['tipo_item_pedido'] . '";';
            $linha .= '"' . $dado['nome_exame'] . '";';
            $linha .= '"' . $dado['tipo_exame'] . '";';
            $linha .= '"' . $dado['fornecedor'] . '";';
            $linha .= '"' . $dado['data_realizacao'] . '";';
            $linha .= '"' . $dado['resultado_exame'] . '";';
            $linha .= '"' . $dado['resultado_observacao'] . '";';
            $linha .= '"' . $dado['observacao'] . '";';
            $linha .= "\n";
            echo utf8_decode($linha);
        }
        exit;
    }
    /*
     * Exporta planilha processada para importação de pedidos de exame
     * Será efeito o download de um arquivo .CSV com os dados importados para processamento de pedidos de exame
     * @param $codigo_importacao_pedido_exame int Código de Identificação da Importação de Pedidos de Exame
     */
    function importar_pedido_exame($codigo_cliente)
    { //codigo_cliente -> GrupoEconomico.)
        $this->loadModel('Cliente');
        $this->loadModel('GrupoEconomico');
        $this->loadModel('ImportacaoPedidosExame');
        $this->loadModel('ImportacaoPedidosExamesRegistros');
        $this->pageTitle = 'Importação Pedidos de Exame';
        if (!isset($codigo_cliente) && empty($codigo_cliente)) {
            $this->redirect('/');
        }
        if (!empty($this->data)) {
            if (preg_match('@\.(csv)$@i', $this->data['ImportacaoPedidosExame']['nome_arquivo']['name'])) {
                $path_destino = APP . 'tmp' . DS;
                $arquivo_destino = $this->data['ImportacaoPedidosExame']['nome_arquivo']['name'];
                if (move_uploaded_file($this->data['ImportacaoPedidosExame']['nome_arquivo']['tmp_name'], $path_destino . $arquivo_destino)) {
                    if ($this->ImportacaoPedidosExame->incluir($path_destino, $arquivo_destino, $codigo_cliente)) {
                        $this->BSession->setFlash('save_success');
                    } else {
                        $error = $this->ImportacaoPedidosExame->invalidFields();
                        $this->BSession->setFlash(array(MSGT_ERROR, $error['codigo']));
                    }
                } else {
                    $this->BSession->setFlash('save_error');
                }
            } else {
                $this->Importar->invalidate('nome_arquivo', 'Extensão inválida!');
                $this->BSession->setFlash('save_error');
            }
        }
        $this->GrupoEconomico->bindModel(
            array('hasOne' => array('GrupoEconomicoCliente' => array('foreignKey' => 'codigo_grupo_economico'))),
            array('belongsTo' => array('Cliente' => array('foreignKey' => 'GrupoEconomicoCliente.codigo_cliente')))
        );
        $grupo_economico = $this->GrupoEconomico->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $codigo_cliente)));
        $cliente = $this->Cliente->find('first', array('conditions' => array('codigo' => $codigo_cliente)));
        $grupo_economico['Cliente'] = $cliente['Cliente'];
        $conditions = array('ImportacaoPedidosExame.codigo_grupo_economico' => $grupo_economico['GrupoEconomico']['codigo']);
        $order = array('ImportacaoPedidosExame.data_inclusao DESC');
        $this->ImportacaoPedidosExame->bindModel(
            array(
                'belongsTo' =>
                array(
                    'StatusImportacao' => array('foreignKey' => 'codigo_status_importacao'),
                    'GrupoEconomico' => array('foreignKey' => false, 'conditions' => 'GrupoEconomico.codigo = ImportacaoPedidosExame.codigo_grupo_economico')
                ),
            )
        );
        $arquivos_importados = $this->ImportacaoPedidosExame->find('all', compact('conditions', 'order'));
        $this->set(compact('arquivos_importados', 'grupo_economico'));
    }
    /*
     * Exclui planilha importada de pedidos de exame para processamento
     * Será excluídos os registros de um arquivo .CSV importados para processamento de pedidos de exame
     * @param $codigo_importacao_pedido_exame int Código de Identificação da Importação de Pedidos
     */
    function eliminar_importacao_pedido_exame($codigo_cliente, $codigo_importacao_pedido_exame)
    {
        $this->pageTitle = 'Exclusão de arquivo para importação de pedidos de exame';
        $this->loadModel('ImportacaoPedidosExame');
        $this->ImportacaoPedidosExame->bindModel(array('belongsTo' => array(
            'ImportacaoPedidosExamesRegistros' => array('foreignKey' => false, 'conditions' => array(
                'ImportacaoPedidosExame.codigo = ImportacaoPedidosExamesRegistros.codigo_importacao_pedidos_exames'
            ))
        )));
        if ($this->RequestHandler->isPost()) {
            $pedidos_exame = $this->ImportacaoPedidosExame->carregar($codigo_importacao_pedido_exame);
            if ($pedidos_exame['ImportacaoPedidosExame']['codigo_status_importacao'] == StatusImportacao::SEM_PROCESSAR) {
                if ($this->ImportacaoPedidosExame->excluir($codigo_importacao_pedido_exame)) {
                    $this->BSession->setFlash('delete_success');
                    $this->redirect(array('action' => 'importar_pedido_exame', $codigo_cliente));
                } else {
                    $this->BSession->setFlash('delete_error');
                }
            } else {
                $this->BSession->setFlash('delete_error_imp_em_proc');
                $this->redirect(array('action' => 'importar_pedido_exame', $codigo_cliente));
            }
        } else {
            $conditions = array('ImportacaoPedidosExame.codigo' => $codigo_importacao_pedido_exame);
            $fields = array(
                'ImportacaoPedidosExame.nome_arquivo AS nome_arquivo',
                'ImportacaoPedidosExamesRegistros.nome_empresa AS nome_empresa',
                'ImportacaoPedidosExame.data_inclusao AS data_inclusao',
                'COUNT(ImportacaoPedidosExamesRegistros.codigo) AS qtd_pedidos_exame'
            );
            $group = array('nome_arquivo', 'nome_empresa', 'data_inclusao');
            $pedidos_exame = $this->ImportacaoPedidosExame->find('all', compact('fields', 'conditions', 'group'));
        }
        $this->set(compact('pedidos_exame'));
    }
    /**
     * Método de chamada da tela de gerenciamento dos arquivos importados para geração de pedidos de exame dos médico
     * @param type $codigo_cliente Código de identificação do cliente onde será gerados os pedidos de exame médico importados
     * @param type $codigo_importacao_pedido_exame Código de Identificação do arquivo importado
     * @return avoid
     */
    function gerenciar_importacao_pedidos_exame($codigo_cliente, $codigo_importacao_pedido_exame)
    {
        $this->pageTitle = 'Gerenciar Arquivo para Importação de Pedidos de Exame';
        $this->loadModel('ImportacaoPedidosExame');
        if ($this->RequestHandler->isPost()) {
            Comum::execInBackground(ROOT . '/cake/console/cake -app ' . ROOT . DS . 'app importacao pedidos_exame ' . "{$_SESSION['Auth']['Usuario']['codigo_empresa']} {$_SESSION['Auth']['Usuario']['codigo']} {$codigo_importacao_pedido_exame}");
            $this->redirect(array('action' => 'importar_pedido_exame', $codigo_cliente));
        }
        $pedidos_exame = $this->ImportacaoPedidosExame->carregar($codigo_importacao_pedido_exame);
        $this->set(compact('pedidos_exame'));
    }
    /**
     * Método de chamada da tela de gerenciamento das validações de dados de um arquivo importado
     * @param type $codigo_cliente Código de identificação do cliente onde será gerados os pedidos de exame médico importados
     * @param type $codigo_importacao_pedido_exame Código de Identificação do arquivo importado
     * @return avoid
     */
    function importacao_pedido_exame_listagem($codigo_importacao_pedido_exame)
    {
        $this->loadModel('ImportacaoPedidosExame');
        $this->loadModel('ImportacaoPedidosExamesRegistros');
        $conditions = array(
            'codigo_importacao_pedidos_exames' => $codigo_importacao_pedido_exame,
        );
        $this->paginate['ImportacaoPedidosExamesRegistros'] = array(
            'conditions' => $conditions,
            'limit' => 100,
            'order' => 'nome_funcionario',
            'extra' => array('importacao' => true)
        );
        $registros = $this->paginate('ImportacaoPedidosExamesRegistros');
        $importacao_pedidos_exame = $this->ImportacaoPedidosExame->carregar($codigo_importacao_pedido_exame);
        $codigo_status_importacao = $importacao_pedidos_exame['ImportacaoPedidosExame']['codigo_status_importacao'];
        $validaCadastrosRegistro = $this->ImportacaoPedidosExamesRegistros->alertasRegistrosCadastros($registros);
        $alertas = $validaCadastrosRegistro['alertas'];
        $depara = $this->ImportacaoPedidosExamesRegistros->depara();
        $titulos = $this->ImportacaoPedidosExamesRegistros->titulos();
        $validacoes = $validaCadastrosRegistro['validacoes'];
        $this->set(compact('registros', 'alertas', 'depara', 'titulos', 'validacoes', 'codigo_status_importacao'));
    }


    /**
     * metodo para fazer uma manutenção nos pedidos exames importados
     *
     * @param  [type] $codigo_cliente [description]
     * @return [type]                 [description]
     */
    public function manutencao_pedido_exame()
    {

        $this->pageTitle = 'Manutenção Pedidos Exames';

        $this->data = array();

        $this->loadModel('ImportacaoPedidosExame');
        $this->data = $this->Filtros->controla_sessao($this->data, $this->Importar->name);
    } //fim manutencao_pedido_exame

    /**
     * [manutencao_pedido_exame_listagem description]
     * @return [type] [description]
     */
    public function manutencao_pedido_exame_listagem($codigo_cliente = null, $cpf = null)
    {
        $this->layout = 'ajax';

        if (!is_null($codigo_cliente) && !is_null($cpf)) {

            // $cliente = $this->Cliente->find('first', array('conditions' => array('codigo' => $codigo_cliente)));

            //retira a formatacao
            $cpf = str_replace(".", "", str_replace("-", "", $cpf));

            $this->loadModel('Funcionario');
            $funcionario = $this->Funcionario->find('first', array('conditions' => array('cpf' => $cpf)));

            $pedidos_exame = array();

            //monta a query com os pedidos de exames
            if (!empty($funcionario)) {

                $this->loadModel('PedidoExame');

                //relacionamentos
                $joins = array(
                    array(
                        'table' => 'RHHealth.dbo.itens_pedidos_exames',
                        'alias' => 'ItemPedidoExame',
                        'type' => 'LEFT',
                        'conditions' => 'PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames',
                    ),
                    array(
                        'table' => 'RHHealth.dbo.itens_pedidos_exames_baixa',
                        'alias' => 'ItemPedidoExameBaixa',
                        'type' => 'LEFT',
                        'conditions' => 'ItemPedidoExame.codigo = ItemPedidoExameBaixa.codigo_itens_pedidos_exames',
                    ),
                    array(
                        'table' => 'RHHealth.dbo.funcionario_setores_cargos',
                        'alias' => 'FuncionarioSetorCargo',
                        'type' => 'INNER',
                        'conditions' => 'PedidoExame.codigo_func_setor_cargo = FuncionarioSetorCargo.codigo',
                    ),
                    array(
                        'table' => 'RHHealth.dbo.setores',
                        'alias' => 'Setor',
                        'type' => 'INNER',
                        'conditions' => 'FuncionarioSetorCargo.codigo_setor = Setor.codigo',
                    ),
                    array(
                        'table' => 'RHHealth.dbo.cargos',
                        'alias' => 'Cargo',
                        'type' => 'INNER',
                        'conditions' => 'FuncionarioSetorCargo.codigo_cargo = Cargo.codigo',
                    ),
                    array(
                        'table' => 'RHHealth.dbo.cliente_funcionario',
                        'alias' => 'ClienteFuncionario',
                        'type' => 'INNER',
                        'conditions' => 'FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo OR ClienteFuncionario.codigo = PedidoExame.codigo_cliente_funcionario',
                    ),
                    array(
                        'table' => 'RHHealth.dbo.grupos_economicos_clientes',
                        'alias' => 'GrupoEconomicoCliente',
                        'type' => 'INNER',
                        'conditions' => 'GrupoEconomicoCliente.codigo_cliente = PedidoExame.codigo_cliente',
                    ),
                    array(
                        'table' => 'RHHealth.dbo.Cliente',
                        'alias' => 'Cliente',
                        'type' => 'INNER',
                        'conditions' => 'PedidoExame.codigo_cliente = Cliente.codigo',
                    ),
                );

                //campos
                $fields = array(
                    'PedidoExame.codigo as codigo_pedido',
                    'Cliente.nome_fantasia as cliente',
                    'Setor.descricao as setor',
                    'Cargo.descricao as cargo',
                    'ItemPedidoExameBaixa.pedido_importado as pedido_importado',
                    'ClienteFuncionario.codigo as codigo_matricula',
                );
                //agrupamento
                $group = array(
                    'PedidoExame.codigo',
                    'Cliente.nome_fantasia',
                    'Setor.descricao',
                    'Cargo.descricao',
                    'ItemPedidoExameBaixa.pedido_importado',
                    'ClienteFuncionario.codigo',
                );

                //monta as condições para buscar no banco de dados
                $conditions = array('ClienteFuncionario.codigo_funcionario' => $funcionario['Funcionario']['codigo']);

                //monta a query
                $pedidos_exame = $this->PedidoExame->find('all', array('fields' => $fields, 'joins' => $joins, 'conditions' => $conditions, 'group' => $group));
            } //fim verificacao se existe registro do funcionario

        } //fim is null codigo cliente e cpf

        // pr($pedidos_exame);

        $this->set(compact('pedidos_exame', 'funcionario'));
    } //fim manutencao_pedido_exame_listagem

    /**
     * [modal_manutencao_pedido_exame description]
     *
     * metodo para apresentar a modal e fazer a manutenção do pedido
     *
     * @param  [type] $codigo_pedido [description]
     * @return [type]                [description]
     */
    public function modal_manutencao_pedido_exame($codigo_pedido, $codigo_funcionario)
    {
        //query para pegar os dados
        $this->loadModel('Funcionario');
        //relacionamentos
        $joins = array(
            array(
                'table' => 'RHHealth.dbo.cliente_funcionario',
                'alias' => 'ClienteFuncionario',
                'type' => 'INNER',
                'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
            ),
            array(
                'table' => 'RHHealth.dbo.funcionario_setores_cargos',
                'alias' => 'FuncionarioSetorCargo',
                'type' => 'INNER',
                'conditions' => 'ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario',
            ),
            array(
                'table' => 'RHHealth.dbo.setores',
                'alias' => 'Setor',
                'type' => 'INNER',
                'conditions' => 'FuncionarioSetorCargo.codigo_setor = Setor.codigo',
            ),
            array(
                'table' => 'RHHealth.dbo.cargos',
                'alias' => 'Cargo',
                'type' => 'INNER',
                'conditions' => 'FuncionarioSetorCargo.codigo_cargo = Cargo.codigo',
            ),
            array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'ClienteFuncionario.codigo_cliente = Cliente.codigo',
            ),
        );
        //campos
        $fields = array('FuncionarioSetorCargo.codigo as codigo', "CONCAT(ClienteFuncionario.codigo,': ',Cliente.nome_fantasia, ' - ', setor.descricao,' - ', cargo.descricao) as unidade_setor_cargo");
        //monta as condições para buscar no banco de dados
        $conditions = array('Funcionario.codigo' => $codigo_funcionario);
        //monta a query
        $dados = $this->Funcionario->find('all', array('fields' => $fields, 'joins' => $joins, 'conditions' => $conditions));
        $matriculas = array();
        //varre as matriculas do funcionario
        foreach ($dados as $matr) {
            $matriculas[$matr[0]['codigo']] = $matr[0]['unidade_setor_cargo'];
        }

        // $this->loadModel('PedidoExame');
        $joinExames = array(
            array(
                'table' => 'RHHealth.dbo.itens_pedidos_exames',
                'alias' => 'ItemPedidoExame',
                'type' => 'LEFT',
                'conditions' => 'PedidoExame.codigo = ItemPedidoExame.codigo_pedidos_exames',
            ),
            array(
                'table' => 'RHHealth.dbo.exames',
                'alias' => 'Exame',
                'type' => 'LEFT',
                'conditions' => 'ItemPedidoExame.codigo_exame = Exame.codigo',
            ),
            array(
                'table' => 'RHHealth.dbo.itens_pedidos_exames_baixa',
                'alias' => 'ItemPedidoExameBaixa',
                'type' => 'LEFT',
                'conditions' => 'ItemPedidoExameBaixa.codigo_itens_pedidos_exames = ItemPedidoExame.codigo',
            ),
        );
        $conditionsExames = array('PedidoExame.codigo' => $codigo_pedido);
        $fieldPedido = array('PedidoExame.*', 'Exame.descricao', 'ItemPedidoExameBaixa.data_realizacao_exame', 'ItemPedidoExameBaixa.data_inclusao');
        $pedidos_exames = $this->PedidoExame->find('all', array('fields' => $fieldPedido, 'joins' => $joinExames, 'conditions' => $conditionsExames));

        // pr($pedidos_exames);exit;

        //compacties
        $this->set(compact('matriculas', 'pedidos_exames', 'codigo_pedido', 'codigo_funcionario'));
    } //fim modal_manutencao_pedido_exame

    /**
     * [salvar_manutencao description]
     * gravar a manutencao
     * @param  [type] $codigo_pedido      [description]
     * @param  [type] $codiog_funcionario [description]
     * @return [type]                     [description]
     */
    public function manutencao_salvar()
    {
        $this->autoRender = false;

        $codigo_pedido      = $_POST['codigo_pedido'];
        $codigo_funcionario = $_POST['codigo_func_setor_cargo'];

        $dados['retorno'] = false;

        $this->loadModel('PedidoExame');
        $pedido = $this->PedidoExame->find('first', array('conditions' => array('codigo' => $codigo_pedido)));

        // pr($pedido);


        if (!empty($pedido)) {

            //funcionario setor e cargo codigo da matricula dele
            $this->loadModel('FuncionarioSetorCargo');
            $fsc = $this->FuncionarioSetorCargo->find('first', array('conditions' => array('FuncionarioSetorCargo.codigo' => $codigo_funcionario)));

            // pr($fsc);

            //seta o novo pedido para atualizar
            $pedido['PedidoExame']['codigo_func_setor_cargo'] = $codigo_funcionario;
            $pedido['PedidoExame']['codigo_cliente_funcionario'] = $fsc['FuncionarioSetorCargo']['codigo_cliente_funcionario'];

            // pr($pedido);exit;

            $dados['retorno'] = true;
            if (!$this->PedidoExame->atualizar($pedido)) {
                $dados['retorno'] = false;
            }
        }
        //retorna os dados com json de sucesso ou falha
        echo json_encode($dados);
        exit;
    } //fim salvar manutencao

    /**
     * [manutencao_excluir_pedido description]
     *
     * metodo para excluir o pedido de exame para este funcionario
     *
     * @return [type] [description]
     */
    public function manutencao_excluir_pedido()
    {

        //autorender
        $this->autoRender = false;

        //recupera o codigo do pedido
        $codigo_pedido = $_POST['codigo_pedido'];
        //retorno dos dados
        $dados['retorno'] = false;

        //pega o pedido de exame
        $this->loadModel('PedidoExame');
        $this->loadModel('ItemPedidoExame');
        $this->loadModel('ItemPedidoExameBaixa');

        try {
            $this->PedidoExame->query('begin transaction');

            //pega os itens
            $itens_pedidos_exames = $this->ItemPedidoExame->find('list', array('conditions' => array('ItemPedidoExame.codigo_pedidos_exames' => $codigo_pedido)));

            //deleta as baixas
            if ($this->ItemPedidoExameBaixa->deleteAll(array('ItemPedidoExameBaixa.codigo_itens_pedidos_exames'  => $itens_pedidos_exames), false, true)) {

                //deleta os itens
                if ($this->ItemPedidoExame->deleteAll(array('ItemPedidoExame.codigo'  => $itens_pedidos_exames), false, true)) {

                    //deleta o pedido
                    if (!$this->PedidoExame->delete($codigo_pedido)) {
                        //caso de erro volta tudo
                        throw new Exception();
                    } //fim pedido exame
                } else {
                    //caso de erro volta tudo
                    throw new Exception();
                } //fim item pedido exame
            } else {
                //caso de erro volta tudo
                throw new Exception();
            } //fim item pedido exame baixa

            $this->PedidoExame->commit();
            // $this->PedidoExame->rollback();
            $dados['retorno'] = true;
        } catch (Exception $ex) {

            $this->PedidoExame->rollback();
            $dados['retorno'] = false;
        }

        //retorna os dados com json de sucesso ou falha
        echo json_encode($dados);
        exit;
    } //fim manutencao_excluir_pedido

    /**
     * Exporta planilha processada para importação de usuario unidades
     * Será efeito o download de um arquivo .CSV com os dados importados para processamento de usuarios e as unidades relacionadas
     * @param $codigo_importacao_pedido_exame int Código de Identificação da Importação de Pedidos de Exame
     *
     */
    public function importar_usuario_unidade($codigo_cliente) //codigo_cliente -> GrupoEconomico.)
    {

        //titulo da pagina
        $this->pageTitle = 'Importação Usuário Unidades';

        //quando não existir o codigo do cliente irá direcionar para o home do sistema
        if (!isset($codigo_cliente) && empty($codigo_cliente)) {
            $this->redirect('/');
        }

        //Inclusão dos relacionamentos do usuarios com as unidades
        if ($this->RequestHandler->isPost()) {
            //zera o tempo de processamento do php
            set_time_limit(0);

            //verifica se os parametros de importacao do arquivo foi passado
            if (!empty($this->params['data']['Importar']['arquivo']['name'])) {

                //verifica se o arquivo é um csv para importar
                if (preg_match('@\.(csv)$@i', $this->params['data']['Importar']['arquivo']['name'])) {

                    //envia os dados para processamento do arquivo
                    $retorno = $this->Importar->importar_usuario_unidade($this->data);

                    //verifica se existe dados retornados
                    if (!empty($retorno)) {

                        //verifica se ocorreu erro
                        if (!empty($retorno['Erro'])) {
                            //seta variavel auxiliar
                            $key = 0;
                            //varre o primeiro array
                            foreach ($retorno['Erro'] as $linha => $dados) {
                                //variavel auxiliar
                                $var_erro = "";

                                //varre os erros para criacao do tratamento
                                foreach ($dados['erros'] as $tipo_erros => $erros) {
                                    //pega os erros
                                    foreach ($erros as $campo => $erro) {
                                        //seta a variavel para escrever no arquivo de erros
                                        $var_erro .= $erro . '|';
                                    } //fim foreach erros
                                    // $var_erro = substr($var_erro, 0, strlen($var_erro)-1);
                                } //fim foreach dados[erros]

                                //junta os dados
                                $array_dados_erros[$key] = array_merge($dados['dados'], array('erro' => utf8_encode($var_erro)));
                                $key++; //soma contador

                            } //fim foreach
                            //gera o nome do arquivo com erros
                            $nome_arquivo_erro = date('YmdHis') . 'E.csv';

                            //gera o arquivo com os erros
                            $this->gravaArquivoUsuarioUnidade($array_dados_erros, $nome_arquivo_erro);
                        } //fim valicadao erro
                        else {
                            $array_dados_erros = array();
                            $nome_arquivo_erro = '';
                        } //fim else

                        //verifica se existem dados cadastrados com sucesso
                        if (!empty($retorno['Sucesso'])) {
                            $key = 0; //contador
                            //verre os dados com sucesso
                            foreach ($retorno['Sucesso'] as $linha => $dados) {
                                $var_sucesso = ""; //variavel auxiliar
                                //seta os dados que ocorreu tudo certo
                                $array_dados_sucesso[$key] = $dados['dados'];
                                $key++; //soma no contador
                            } //fim foreach
                            //cria o nome do arquivo
                            $nome_arquivo_sucesso = date('YmdHis') . 'S.csv';
                            //gera o arquivo de sucesso
                            $this->gravaArquivoUsuarioUnidade($array_dados_sucesso, $nome_arquivo_sucesso);
                        } //fim verificacao de dados com sucesso
                        else {
                            $array_dados_sucesso = array();
                            $nome_arquivo_sucesso = '';
                        } //fim else sucesso

                        //conta quantos erros ocorreram
                        $erros = count($array_dados_erros);
                        //conta quantos sucessos teve
                        $sucesso = count($array_dados_sucesso);
                        //soma para obter o total
                        $total = ($erros + $sucesso);
                        //gera os dados para imprimir na tela
                        $dados_arquivo = array(
                            'nome_arquivo' => $this->data['Importar']['arquivo']['name'],
                            'erros' => $erros,
                            'nome_arquivo_erro' => $nome_arquivo_erro,
                            'sucesso' => $sucesso,
                            'nome_arquivo_sucesso' => $nome_arquivo_sucesso,
                            'total' => $total
                        );
                    } //fim validacao retorno
                } //fim verificacao se é um csv
                else {
                    $this->Importar->invalidate('arquivo', 'Extensão inválida!');
                } //fim else
            } //fim verificacao se foi passado o arquivo para importar
            else {
                $this->Importar->invalidate('arquivo', 'Arquivo não enviado!');
            } //fim else

        } //fim post

        //pega os dados dos clientes
        $dados_cliente = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);
        //seta os dados do cliente
        $this->data['Matriz'] = $dados_cliente['Matriz'];

        //compacta para poder usar as variaveis
        $this->set(compact('codigo_cliente', 'dados_arquivo', 'nome_arquivo_sucesso', 'nome_arquivo_erro'));
    } //importar_usuario_unidade

    /**
     * [gravaArquivoUsuarioUnidade description]
     *
     * metodo para gerar os arquivos que irá apresentar na tela
     *
     * @param  [type] $data         [description]
     * @param  [type] $arquivo_nome [description]
     * @return [type]               [description]
     */
    private function gravaArquivoUsuarioUnidade($data, $arquivo_nome)
    {
        //verifica se existe data
        if (!empty($data)) {
            //seta o tempo como infinito
            set_time_limit(0);
            //seta o path de destino
            $destino = DIR_ARQUIVOS . 'importacao_dados_usuario_unidade' . DS;
            $arquivo = $destino . $arquivo_nome;

            // cria diretorio
            if (!is_dir($destino))
                mkdir($destino);
            //abre o arquivo para escrita e gravação
            $arquivo_importacao = fopen($arquivo, "a+");
            $linha = '';
            //caso tenha arquivo com erros
            if (isset($data[0]['erro']) && !empty($data[0]['erro'])) {
                $linha .= 'Login;CNPJ;Erros;' . "\n";
            } else {
                $linha .= 'Login;CNPJ;' . "\n";
            }

            $linha = utf8_decode($linha);

            //varre os dados
            for ($chave = 0; $chave < count($data); $chave++) {

                $linha .= $data[$chave]['login'] . ';';
                $linha .= $data[$chave]['cnpj_unidade'] . ';';

                if (isset($data[$chave]['erro']) && !empty($data[$chave]['erro'])) {
                    $linha .= utf8_decode($data[$chave]['erro']) . ';';
                }

                $linha .= "\n";
            } //fim for

            fwrite($arquivo_importacao, $linha . "\r\n");
            fclose($arquivo_importacao);
        } //fim if data
    } //fim gravaArquivoUsuarioUnidade



    /**
     * Exporta planilha processada para importação de usuario
     * Será efeito o download de um arquivo .CSV com os dados importados para processamento de usuarios
     * @param $codigo_importacao_pedido_exame int Código de Identificação da Importação de Pedidos de Exame
     *
     */
    public function importar_usuario($codigo_cliente) //codigo_cliente -> GrupoEconomico.)
    {

        //titulo da pagina
        $this->pageTitle = 'Importação Usuário';

        //quando não existir o codigo do cliente irá direcionar para o home do sistema
        if (!isset($codigo_cliente) && empty($codigo_cliente)) {
            $this->redirect('/');
        }

        //Inclusão dos relacionamentos do usuarios com as unidades
        if ($this->RequestHandler->isPost()) {
            //zera o tempo de processamento do php
            set_time_limit(0);

            //verifica se os parametros de importacao do arquivo foi passado
            if (!empty($this->params['data']['Importar']['arquivo']['name'])) {

                //verifica se o arquivo é um csv para importar
                if (preg_match('@\.(csv)$@i', $this->params['data']['Importar']['arquivo']['name'])) {

                    //envia os dados para processamento do arquivo
                    $retorno = $this->Importar->importar_usuario($codigo_cliente, $this->data);

                    // pr($retorno);exit;

                    //verifica se existe dados retornados
                    if (!empty($retorno)) {

                        //verifica se ocorreu erro
                        if (!empty($retorno['Erro'])) {
                            //seta variavel auxiliar
                            $key = 0;
                            //varre o primeiro array
                            foreach ($retorno['Erro'] as $linha => $dados) {
                                //variavel auxiliar
                                $var_erro = "";

                                //varre os erros para criacao do tratamento
                                foreach ($dados['erros'] as $tipo_erros => $erros) {
                                    //pega os erros
                                    foreach ($erros as $campo => $erro) {
                                        //seta a variavel para escrever no arquivo de erros
                                        $var_erro .= $erro . '|';
                                    } //fim foreach erros
                                    // $var_erro = substr($var_erro, 0, strlen($var_erro)-1);
                                } //fim foreach dados[erros]

                                //junta os dados
                                $array_dados_erros[$key] = array_merge($dados['dados'], array('erro' => utf8_encode($var_erro)));
                                $key++; //soma contador

                            } //fim foreach
                            //gera o nome do arquivo com erros
                            $nome_arquivo_erro = date('YmdHis') . 'E.csv';

                            //gera o arquivo com os erros
                            $this->gravaArquivoUsuario($array_dados_erros, $nome_arquivo_erro);
                        } //fim valicadao erro
                        else {
                            $array_dados_erros = array();
                            $nome_arquivo_erro = '';
                        } //fim else

                        //verifica se existem dados cadastrados com sucesso
                        if (!empty($retorno['Sucesso'])) {
                            $key = 0; //contador
                            //verre os dados com sucesso
                            foreach ($retorno['Sucesso'] as $linha => $dados) {
                                $var_sucesso = ""; //variavel auxiliar
                                //seta os dados que ocorreu tudo certo
                                $array_dados_sucesso[$key] = $dados['dados'];
                                $key++; //soma no contador
                            } //fim foreach
                            //cria o nome do arquivo
                            $nome_arquivo_sucesso = date('YmdHis') . 'S.csv';
                            //gera o arquivo de sucesso
                            $this->gravaArquivoUsuario($array_dados_sucesso, $nome_arquivo_sucesso);
                        } //fim verificacao de dados com sucesso
                        else {
                            $array_dados_sucesso = array();
                            $nome_arquivo_sucesso = '';
                        } //fim else sucesso

                        //conta quantos erros ocorreram
                        $erros = count($array_dados_erros);
                        //conta quantos sucessos teve
                        $sucesso = count($array_dados_sucesso);
                        //soma para obter o total
                        $total = ($erros + $sucesso);
                        //gera os dados para imprimir na tela
                        $dados_arquivo = array(
                            'nome_arquivo' => $this->data['Importar']['arquivo']['name'],
                            'erros' => $erros,
                            'nome_arquivo_erro' => $nome_arquivo_erro,
                            'sucesso' => $sucesso,
                            'nome_arquivo_sucesso' => $nome_arquivo_sucesso,
                            'total' => $total
                        );
                    } //fim validacao retorno
                } //fim verificacao se é um csv
                else {
                    $this->Importar->invalidate('arquivo', 'Extensão inválida!');
                } //fim else
            } //fim verificacao se foi passado o arquivo para importar
            else {
                $this->Importar->invalidate('arquivo', 'Arquivo não enviado!');
            } //fim else

        } //fim post

        //pega os dados dos clientes
        $dados_cliente = $this->GrupoEconomicoCliente->retorna_dados_cliente($codigo_cliente);
        //seta os dados do cliente
        $this->data['Matriz'] = $dados_cliente['Matriz'];

        //compacta para poder usar as variaveis
        $this->set(compact('codigo_cliente', 'dados_arquivo', 'nome_arquivo_sucesso', 'nome_arquivo_erro'));
    } //importar_usuario


    /**
     * [gravaArquivoUsuario description]
     *
     * metodo para gerar os arquivos que irá apresentar na tela
     *
     * @param  [type] $data         [description]
     * @param  [type] $arquivo_nome [description]
     * @return [type]               [description]
     */
    private function gravaArquivoUsuario($data, $arquivo_nome)
    {
        //verifica se existe data
        if (!empty($data)) {
            //seta o tempo como infinito
            set_time_limit(0);
            //seta o path de destino
            $destino = APP . 'tmp' . DS . 'importacao_dados_usuario' . DS;
            $arquivo = $destino . $arquivo_nome;

            // cria diretorio
            if (!is_dir($destino))
                mkdir($destino);
            //abre o arquivo para escrita e gravação
            $arquivo_importacao = fopen($arquivo, "a+");
            $linha = '';
            //caso tenha arquivo com erros
            if (isset($data[0]['erro']) && !empty($data[0]['erro'])) {
                $linha .= 'Login;Nome;Perfil (nome do perfil);E-mail;Status (A: ativo, I: inativo);Erros;' . "\n";
            } else {
                $linha .= 'Login;Nome;Perfil (nome do perfil);E-mail;Status (A: ativo, I: inativo);' . "\n";
            }

            $linha = utf8_decode($linha);

            //varre os dados
            for ($chave = 0; $chave < count($data); $chave++) {

                $linha .= $data[$chave]['login'] . ';';
                $linha .= $data[$chave]['nome'] . ';';
                $linha .= $data[$chave]['perfil'] . ';';
                $linha .= $data[$chave]['email'] . ';';
                $linha .= $data[$chave]['status'] . ';';

                if (isset($data[$chave]['erro']) && !empty($data[$chave]['erro'])) {
                    $linha .= utf8_decode($data[$chave]['erro']) . ';';
                }

                $linha .= "\n";
            } //fim for

            fwrite($arquivo_importacao, $linha . "\r\n");
            fclose($arquivo_importacao);
        } //fim if data
    } //fim gravaArquivoUsuario

}
