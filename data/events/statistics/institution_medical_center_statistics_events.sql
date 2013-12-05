####
# This sql file contains the definitions of events involving `institution_medical_center_statistics_daily` table.
#
# Note: DO NOT INCLUDE ANY OTHER DEFINITIONS FROM OTHER TABLES.
#
# @author: Adelbert D. Silla
# @date: November 27, 2013
#

DROP EVENT IF EXISTS institution_medical_center_statistics_daily_event $$

## create event for institution_medical_center_statistics_daily_event table
CREATE EVENT institution_medical_center_statistics_daily_event
ON SCHEDULE 
EVERY 1 DAY
COMMENT 'Purges >1 day old data in institution_medical_center_statistics_daily every day'
DO
BEGIN
    ## purges > 1 day old data
    DELETE FROM `institution_medical_center_statistics_daily` WHERE DATEDIFF(NOW(), `date`) > 1;
END; 
$$