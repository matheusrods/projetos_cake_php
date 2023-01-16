<?php
class EmailListSubscribers extends AppModel {
    
    public $name          = 'EmailListSubscribers';
    public $useDbConfig   = 'mailing';
    public $databaseTable = 'mailing';
    public $useTable      = 'email_list_subscribers';
    public $primaryKey    = 'subscriberid';
    public $tableSchema   = 'dbo';

    var $validate = array(                
        'emailaddress' => array(
            'rule' 	   => 'validaChaveComposta',
            'message'  => 'Email já cadastrado!',
        ),
        'confirmcode' => array(
            'notEmpty' => array(
	            'rule' 	   => 'notEmpty',
	            'message'  => 'Vazio não meu',
            )
        ),
    );

    function validaChaveComposta(){    	
		$email = $this->find('count',array(
			'conditions' => array(
				'listid'       => $this->data[$this->name]['listid'],
				'emailaddress' => $this->data[$this->name]['emailaddress'],
			)
		));

		if( $email )
			return false;
		else
			return true;
    }
    
}
