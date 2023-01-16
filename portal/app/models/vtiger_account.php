<?php
class VtigerAccount extends AppModel {
    public $name = 'VtigerAccount';
    public $useDbConfig = 'crm';
    public $databaseTable = 'crm521';
    public $useTable = 'vtiger_account';
    public $primaryKey = 'accountid';
    //public $tableSchema = 'dbo';

    public function obterDocumentos($cnpj) {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (empty($cnpj)) {
            return false;
        }

        $fields = array(
            'VtigerAttachment.attachmentsid',
            'VtigerAttachment.name',
            'VtigerAttachment.path'
        );

        $joins = array(
            array(
                'table' => 'vtiger_accountscf',
                'alias' => 'VtigerAccountscf',
                'conditions' => 'VtigerAccountscf.accountid = VtigerAccount.accountid',
            ),
            array(
                'table' => 'vtiger_senotesrel',
                'alias' => 'VtigerSenotesrel',
                'conditions' => 'VtigerSenotesrel.crmid = VtigerAccount.accountid',
            ),
            array(
                'table' => 'vtiger_notes',
                'alias' => 'VtigerNote',
                'conditions' => 'VtigerSenotesrel.notesid = VtigerNote.notesid',
            ),
            array(
                'table' => 'vtiger_crmentity',
                'alias' => 'VtigerCrmentity',
                'conditions' => 'VtigerCrmentity.crmid = VtigerNote.notesid',
            ),
            array(
                'table' => 'vtiger_seattachmentsrel',
                'alias' => 'VtigerSeattachmentsrel',
                'conditions' => 'VtigerSeattachmentsrel.crmid = VtigerCrmentity.crmid',
            ),
            array(
                'table' => 'vtiger_attachments',
                'alias' => 'VtigerAttachment',
                'conditions' => 'VtigerSeattachmentsrel.attachmentsid = VtigerAttachment.attachmentsid',
            ),
        );

        $conditions = array(
            'VtigerAccountscf.cf_905' => $cnpj
        );
        
        $retorno = array();
        $documentos_cliente = $this->find('all', compact('fields', 'joins', 'conditions'));
        
        foreach ($documentos_cliente as &$documento) {
            $retorno[] = $documento['VtigerAttachment']['path'] . $documento['VtigerAttachment']['attachmentsid'] . '_' . $documento['VtigerAttachment']['name'];
        }

        return $retorno;
    }
}
