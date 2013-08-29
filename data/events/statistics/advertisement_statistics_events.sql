####
# This sql file contains the definitions of events involving `advertisement_statistics_daily` table.
#
# Note: DO NOT INCLUDE ANY OTHER DEFINITIONS FROM OTHER TABLES.
#
# @author: Allejo Chris G. Velarde
# @date: August 29, 2013
#

DROP EVENT IF EXISTS advertisement_statistics_daily_event $$

## create event for advertisement_statistics_daily table
CREATE EVENT advertisement_statistics_daily_event
ON SCHEDULE 
EVERY 1 DAY
COMMENT 'Purges >1 day old data in advertisement_statistics_daily every day'
DO
BEGIN
    ## purges > 1 day old data
    DELETE FROM `advertisement_statistics_daily` WHERE DATEDIFF(NOW(), `date`) > 1;
END; 
$$