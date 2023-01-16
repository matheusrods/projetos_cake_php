#!/bin/sh
if ! ps -ef | grep -v grep | grep 'scorecard_extracao processar' ; then
	/home/sistemas/portal/portal/cake/console/cake -app /home/sistemas/portal/portal/app scorecard_extracao processar
fi
