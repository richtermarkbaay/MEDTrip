##----------------------
# AFTER UPDATE advertisements trigger.
#
# name: `advertisements_au`
# table: `advertisements`
# event: AFTER UPDATE
# author: acgvelarde
DROP TRIGGER IF EXISTS advertisements_au $$
CREATE TRIGGER advertisements_au AFTER UPDATE on `advertisements`
FOR EACH ROW
BEGIN
    SET @ACTIVE_ADVERTISEMENT_STATUS = 1;
    SET @ACTIVE_DENORMALIZED_STATUS = 0;
    SET @INACTIVE_DENORMALIZED_STATUS = 0;
    
    
    -- status has been changed from ACTIVE to NON-ACTIVE  
    IF OLD.`status` = @ACTIVE_ADVERTISEMENT_STATUS AND NEW.`status` != @ACTIVE_ADVERTISEMENT_STATUS THEN
        -- update statuses of existing search terms referencing this medical center to INACTIVE_DENORMALIZED_STATUS
        
        UPDATE `advertisement_denormalized_properties` SET `status` = @INACTIVE_DENORMALIZED_STATUS 
        WHERE `id` = OLD.`id`;
    END IF;  
END; $$
### end advertisements_au trigger definition