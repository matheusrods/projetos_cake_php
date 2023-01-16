<?php
class LdapUser extends Model {
    var $name = 'User';
    var $useDbConfig = 'ldap';
    var $primaryKey = 'uid';
    var $useTable = 'ou=ithealth';
}
?>