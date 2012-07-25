{\rtf1\ansi\ansicpg1252\cocoartf1138\cocoasubrtf470
{\fonttbl\f0\fswiss\fcharset0 Helvetica;}
{\colortbl;\red255\green255\blue255;}
\margl1440\margr1440\vieww10800\viewh8400\viewkind0
\pard\tx566\tx1133\tx1700\tx2267\tx2834\tx3401\tx3968\tx4535\tx5102\tx5669\tx6236\tx6803\pardirnatural

\f0\fs24 \cf0 DROP EVENT token_expiration_event $$\
\
\
CREATE EVENT token_expiration_event \
ON SCHEDULE EVERY 1 MINUTE\
DO\
	BEGIN\
		UPDATE `healthcareabroad`.`invitation_tokens` SET `invitation_tokens`. `status` = 2 WHERE `invitation_tokens`.`expiration_date` <= NOW();\
	END $$}