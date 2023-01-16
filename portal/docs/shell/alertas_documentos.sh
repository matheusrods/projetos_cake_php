#!/bin/bash
export TERM=linux;
/home/sistemas/rhhealth/portal/cake/console/cake -app /home/sistemas/rhhealth/portal/app proposta_credenciamento alerta_envio_documento_pendente;
/home/sistemas/rhhealth/portal/cake/console/cake -app /home/sistemas/rhhealth/portal/app fornecedor verifica_validade_documento;