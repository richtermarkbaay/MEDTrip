####
# This sql file contains the definitions of triggers involving `institution_medical_centers` table.
#
# Note: DO NOT INCLUDE ANY OTHER DEFINITIONS FROM OTHER TABLES.
#
# @author: Allejo Chris G. Velarde
# @date: February 8, 2013
#

##----------------------
# AFTER UPDATE institution_medical_centers trigger.
#
# name: `institution_medical_centers_au`
# table: `institution_medical_centers`
# event: AFTER UPDATE
# author: acgvelarde
DROP TRIGGER IF EXISTS institution_medical_centers_au $$
CREATE TRIGGER institution_medical_centers_au AFTER UPDATE on `institution_medical_centers`
FOR EACH ROW
BEGIN
    DECLARE _sum_clinic_ranking_points INT(11);
    SET @ACTIVE_CLINIC_STATUS = 2;
    SET @ACTIVE_SEARCH_TERM_STATUS = 1;
    SET @INACTIVE_SEARCH_TERM_STATUS = 0;
    
    IF OLD.`status` != NEW.`status` THEN
        IF NEW.`status` =  @ACTIVE_CLINIC_STATUS THEN
            -- update statuses of existing search terms referencing this medical center to ACTIVE_SEARCH_TERM_STATUS
            UPDATE `search_terms` SET `status` = @ACTIVE_SEARCH_TERM_STATUS 
            WHERE `institution_medical_center_id` = OLD.`id`;
        ELSE
            -- update statuses of existing search terms referencing this medical center to INACTIVE_SEARCH_TERM_STATUS
            UPDATE `search_terms` SET `status` = @INACTIVE_SEARCH_TERM_STATUS 
            WHERE `institution_medical_center_id` = OLD.`id`;
        END IF;
    END IF;
      
    
END; $$
### end institution_medical_centers_au trigger definition


##----------------------
# AFTER DELETE institution_medical_centers trigger.
#
# name: `institution_medical_centers_ad`
# table: `institution_medical_centers`
# event: AFTER DELETE
# author: acgvelarde
DROP TRIGGER IF EXISTS institution_medical_centers_ad $$
CREATE TRIGGER institution_medical_centers_ad AFTER DELETE on `institution_medical_centers`
FOR EACH ROW
BEGIN
    SET @ACTIVE_CLINIC_STATUS = 2;
    SET @ACTIVE_SEARCH_TERM_STATUS = 1;
    SET @INACTIVE_SEARCH_TERM_STATUS = 0;
    
    -- deletes search terms referencing this medical center
    DELETE FROM `search_terms` WHERE `institution_medical_center_id` = OLD.`id`;
END; $$
### end institution_medical_centers_ad trigger definition