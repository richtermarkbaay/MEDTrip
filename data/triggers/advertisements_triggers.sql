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
    UPDATE `advertisement_denormalized_properties` SET `status` = NEW.`status` WHERE `id` = NEW.`id`;    
END; $$
### end advertisements_au trigger definition