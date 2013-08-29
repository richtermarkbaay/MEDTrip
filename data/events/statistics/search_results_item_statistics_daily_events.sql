####
# This sql file contains the definitions of events involving `search_results_item_statistics_daily` table.
#
# Note: DO NOT INCLUDE ANY OTHER DEFINITIONS FROM OTHER TABLES.
#
# @author: Allejo Chris G. Velarde
# @date: August 29, 2013
#

DROP EVENT IF EXISTS search_results_item_statistics_daily_event $$

## create event for search_results_item_statistics_daily table
CREATE EVENT search_results_item_statistics_daily_event
ON SCHEDULE 
EVERY 1 DAY
COMMENT 'Purges >1 day old data in search_results_item_statistics_daily every day'
DO
BEGIN
    ## purges > 1 day old data
    DELETE FROM `search_results_item_statistics_daily` WHERE DATEDIFF(NOW(), `date`) > 1;
END; 
$$