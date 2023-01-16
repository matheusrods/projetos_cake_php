#!/bin/bash
export TERM=linux;
#atual
/home/sistemas/rhhealth/portal/cake/console/cake -app /home/sistemas/rhhealth/portal/app integracao_nexo;
/home/sistemas/rhhealth/portal/cake/console/cake -app /home/sistemas/rhhealth/portal/app integracao_nexo enviosAtestados;
#energy
/home/sistemas/rhhealth/portal/cake/console/cake -app /home/sistemas/rhhealth/portal/app integracao_nexo_energy;
/home/sistemas/rhhealth/portal/cake/console/cake -app /home/sistemas/rhhealth/portal/app integracao_nexo_energy enviosAtestados;
