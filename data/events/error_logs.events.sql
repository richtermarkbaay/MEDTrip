DROP EVENT IF EXISTS error_logs_purger $$

CREATE  EVENT `error_logs_purger` 
ON SCHEDULE 
EVERY 1 DAY  
COMMENT 'Purges >1 day old data in error_logs every day'
DO 
BEGIN
    ## purges > 1 day old data
    DELETE FROM `error_logs` WHERE DATEDIFF(NOW(), `date_created`) > 1;
END;
$$