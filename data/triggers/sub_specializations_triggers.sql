####
# This sql file contains the definitions of triggers involving `sub_specializations` table.
#
# Note: DO NOT INCLUDE ANY OTHER DEFINITIONS FOR OTHER TABLES.
#
# @author: Allejo Chris G. Velarde
# @date: January, 29, 2013
#

##----------------------
# INSERT sub_specialization trigger. Insert corresponding row to terms and term_documents
#
# name: `sub_specializations_ai`
# table: `sub_specializations`
# event: AFTER INSERT
# author: acgvelarde
DROP TRIGGER IF EXISTS sub_specializations_ai $$
CREATE TRIGGER sub_specializations_ai AFTER INSERT on `sub_specializations`
FOR EACH ROW
BEGIN
    DECLARE termId BIGINT(20);
    DECLARE DOCUMENT_TYPE_SUB_SPECIALIZATION tinyint(3);
    SET DOCUMENT_TYPE_SUB_SPECIALIZATION = 2;
    -- insert the name to `terms` table
    -- check if this NEW.name exists in `terms`
    SET termId = (SELECT a.`id` FROM `terms` a WHERE a.`name` = NEW.name LIMIT 1);
    IF termId THEN 
        -- NEW.name already exists in terms, insert to `terms_documents`
        INSERT INTO `term_documents` SET `term_id` = termID, `document_id` = NEW.id, `type` = DOCUMENT_TYPE_SUB_SPECIALIZATION;
    ELSE
        -- NEW.name does not exist, insert it to terms
        INSERT INTO `terms` SET `name` = NEW.name, `terms`.slug = NEW.slug, `internal` = 1;
        -- insert into `terms_documents`, it maybe unsafe to use LAST_INSERT_ID here
        -- for now using select query of persisted NEW.name, consider perfomance impact
        INSERT INTO `term_documents` (`term_id`, `document_id`, `type`)
            SELECT a.`id`, NEW.id, DOCUMENT_TYPE_SUB_SPECIALIZATION FROM `terms` a WHERE a.`name` = NEW.name LIMIT 1;
    END IF;
    
END; $$
### end sub_specializations_ai trigger definition

##----------------------
# AFTER UPDATE sub_specialization trigger
#
# name: sub_specializations_au
# table: `sub_specializations`
# event: AFTER UPDATE
# author: acgvelarde
DROP TRIGGER IF EXISTS sub_specializations_au $$
CREATE TRIGGER sub_specializations_au AFTER UPDATE on `sub_specializations`
FOR EACH ROW
BEGIN
    DECLARE DOCUMENT_TYPE_SUB_SPECIALIZATION tinyint(3);
    DECLARE uniqueTermDocuments bigint(20); 
    DECLARE termId BIGINT(20);
    SET DOCUMENT_TYPE_SUB_SPECIALIZATION = 2;
    
    -- name has been updated, do trigger for terms
    IF OLD.name != NEW.name THEN
        -- find number term_documents that is using OLD.name
        SET uniqueTermDocuments = (SELECT IFNULL(SUM(i.num),0) FROM (SELECT COUNT(*) as num FROM `terms` a INNER JOIN `term_documents` b ON a.`id` = b.`term_id` WHERE a.`name` = OLD.name GROUP BY b.`type`, b.`document_id`) i);
        
        IF uniqueTermDocuments > 0 THEN
            -- get the terms.id of NEW.name if it already exists
            SET termId = (SELECT t.`id` FROM `terms` t WHERE t.`name` = NEW.name LIMIT 1);
            
            -- OLD.name term is only used by NEW.id
            IF uniqueTermDocuments = 1 THEN
                IF termId THEN
                    -- NEW.name already exists in `terms`
                    -- update the term.id of the term_documents into the existing term
                    UPDATE `term_documents` SET `term_id` = termId
                    WHERE `term_documents`.`document_id` = NEW.id
                    AND `term_documents`.`type` = DOCUMENT_TYPE_SUB_SPECIALIZATION;
                    
                    -- since NEW.id is the only one using this term, delete the old term
                    DELETE FROM `terms` WHERE `terms`.name = OLD.name;
                ELSE
                    -- NEW.name does not exist yet in terms, let's just update terms.term to NEW.name
                    UPDATE `terms` SET `terms`.`name` = NEW.name, `terms`.slug = NEW.slug, `internal` = 1 WHERE `terms`.`name` = OLD.name;
                END IF;
            ELSE
                -- other term_documents are using OLD.name, create a new row in `terms` with `terms`.`name` = NEW.name
                INSERT INTO `terms` SET `terms`.`name` = NEW.name, `terms`.slug = NEW.slug, `internal` = 1 ON DUPLICATE KEY UPDATE `terms`.`name` = NEW.name;
                -- update the term_id of term_documents to the new term
                UPDATE `term_documents` SET `term_id` = (SELECT `terms`.`id` FROM `terms` WHERE `terms`.`name` = NEW.name LIMIT 1)
                WHERE `term_documents`.`document_id` = NEW.id
                AND `term_documents`.`type` = DOCUMENT_TYPE_SUB_SPECIALIZATION;
            END IF;
            
        ELSE
            -- this will only be executed if OLD.name and it's term_documents has been manually deleted, or has not been persisted yet 
            -- OLD.name term does not exist, create term with NEW.name, 
            -- then create a row term_documents
            INSERT INTO `terms` SET `terms`.`name` = NEW.name, `terms`.slug = NEW.slug, `internal` = 1 ON DUPLICATE KEY UPDATE `terms`.`name` = NEW.`name`;
            INSERT INTO `term_documents` (`term_id`, `document_id`, `type`)
                SELECT a.`id`, NEW.id, DOCUMENT_TYPE_SUB_SPECIALIZATION FROM `terms` a WHERE a.`name` = NEW.name LIMIT 1;
        END IF;
    END IF;
    -- end logic for terms trigger
    
END; $$
## end of sub_specializations_au definition 
##----------------------

##----------------------
# DELETE sub_specialization trigger. Delete terms that are linked to 
#
# name: `sub_specializations_ad`
# table: `sub_specializations`
# event: AFTER DELETE
# author: acgvelarde
DROP TRIGGER IF EXISTS sub_specializations_ad $$
CREATE TRIGGER sub_specializations_ad AFTER DELETE on `sub_specializations`
FOR EACH ROW
BEGIN
    DECLARE DOCUMENT_TYPE_SUB_SPECIALIZATION tinyint(3);
    DECLARE cntTermDocuments bigint(20); 
    SET DOCUMENT_TYPE_SUB_SPECIALIZATION = 2;
     
    -- delete term_documents linked to OLD.id
    DELETE FROM `term_documents` WHERE `type` = DOCUMENT_TYPE_SUB_SPECIALIZATION AND `document_id` = OLD.id;
    
    -- get the remaining term_documents with `terms`.`name` = OLD.name
    SET cntTermDocuments = (SELECT COUNT(td.id) FROM `terms` t LEFT JOIN `term_documents` td ON td.`term_id` = `t`.`id` WHERE t.`name` = OLD.name GROUP BY t.id);
    IF cntTermDocuments = 0 THEN
        -- no more term_documents are using this term, delete it
        DELETE FROM `terms` WHERE `terms`.`name` = OLD.name; 
    END IF;
END; $$
