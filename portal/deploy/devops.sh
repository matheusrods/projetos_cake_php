


#Manter este arquivo e a pasta pai,  apenas como read and Write para os usuarios
# chown root:root /scripts
# chmod u=rwx,g=rwx,o=rx /scripts
echo 'Script Started: devops.sh'


#navega até a pasta do projeto
# Exemplo $1: /home/sistemas/rhhealth/api_rhhealth/api/
cd  $1


git checkout feedback
# Realiza o git pull com a credencial passada por parametro
# Exemplo $2:  https://gitusername:gitpassword@dev.azure.com/IT-HEALTH/ithealth_api/_git/ithealth_api
git pull $2 feedback

echo 'Script Ended: devops.sh'
