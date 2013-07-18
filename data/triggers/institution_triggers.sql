####
# This sql file contains the definitions of triggers involving `institutions` table.
#
# Note: DO NOT INCLUDE ANY OTHER DEFINITIONS FROM OTHER TABLES.
#
# @author: Allejo Chris G. Velarde
# @date: February 13, 2013
#

DELIMITER $$

##----------------------
# AFTER UPDATE institutions trigger.
#
# name: `institutions_au`
# table: `institutions`
# event: AFTER UPDATE
# author: acgvelarde
DROP TRIGGER IF EXISTS institutions_au $$
CREATE TRIGGER institutions_au AFTER UPDATE on `institutions`
FOR EACH ROW
BEGIN
    DECLARE _city_name VARCHAR(250);
    DECLARE _country_name VARCHAR(250);
    SET @ACTIVE_INSTITUTION_STATUS = 9;
    SET @ACTIVE_SEARCH_TERM_STATUS = 1;
    SET @INACTIVE_SEARCH_TERM_STATUS = 0;

    -- clinic status has been changed from NON-ACTIVE to ACTIVE
    IF OLD.`status` != @ACTIVE_INSTITUTION_STATUS AND NEW.`status` = @ACTIVE_INSTITUTION_STATUS THEN

        -- update statuses of existing search terms referencing this medical center to ACTIVE_SEARCH_TERM_STATUS
        UPDATE `search_terms` SET `status` = @ACTIVE_SEARCH_TERM_STATUS
        WHERE `institution_id` = OLD.`id`;

    -- clinic status has been changed from ACTIVE to NON-ACTIVE
    ELSEIF OLD.`status` = @ACTIVE_INSTITUTION_STATUS AND OLD.`status` != NEW.`status` THEN
        -- update statuses of existing search terms referencing this medical center to INACTIVE_SEARCH_TERM_STATUS
        UPDATE `search_terms` SET `status` = @INACTIVE_SEARCH_TERM_STATUS
        WHERE `institution_id` = OLD.`id`;
    END IF;

    IF OLD.country_id != NEW.country_id THEN
        SELECT name INTO _country_name FROM countries WHERE id = NEW.country_id;
        UPDATE search_terms SET country_id = NEW.country_id, country_name = _country_name WHERE institution_id = OLD.id;
    END IF;

    IF OLD.city_id IS NOT NULL AND NEW.city_id IS NULL THEN
        UPDATE search_terms SET city_id = NULL, city_name = NULL WHERE institution_id = OLD.id;
    ELSEIF (OLD.city_id IS NULL AND NEW.city_id IS NOT NULL) OR OLD.city_id != NEW.city_id THEN
        SELECT name INTO _city_name FROM cities WHERE id = NEW.city_id;
        UPDATE search_terms SET city_id = NEW.city_id, city_name = _city_name WHERE institution_id = OLD.id;
    END IF;
END $$
### end institutions_au trigger definition


##----------------------
# AFTER DELETE institutions trigger.
#
# name: `institutions_ad`
# table: `institutions`
# event: AFTER DELETE
# author: acgvelarde
DROP TRIGGER IF EXISTS institutions_ad $$
CREATE TRIGGER institutions_ad AFTER DELETE on `institutions`
FOR EACH ROW
BEGIN
    -- deletes search terms referencing this medical center
    DELETE FROM `search_terms` WHERE `institution_id` = OLD.`id`;
END $$
### end institutions_ad trigger definition

DELIMITER ;