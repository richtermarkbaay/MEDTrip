####
# This sql file contains the definitions of triggers involving `term_documents` table.
#
# Note: DO NOT INCLUDE ANY OTHER DEFINITIONS FROM OTHER TABLES.
#
# @author: Allejo Chris G. Velarde
# @date: February 1, 2013
#

##----------------------
# AFTER INSERT term_documents trigger.
#
# name: `term_documents_ai`
# table: `term_documents`
# event: AFTER INSERT
# author: acgvelarde
DROP TRIGGER IF EXISTS term_documents_ai $$
CREATE TRIGGER term_documents_ai AFTER INSERT on `term_documents`
FOR EACH ROW
BEGIN

    DECLARE otherTermDocumentsCnt int(10);
    
    SET otherTermDocumentsCnt = (SELECT COUNT(*) cnt FROM `search_terms` a WHERE a.`document_id` = NEW.`document_id` AND a.`type` = NEW.`type`);

    #IF otherTermDocumentsCnt > 0 THEN
    #    -- insert this term document with values from insitution medical centers that already used the document_id and type 
    #    INSERT INTO `search_terms` (`term_id`, `institution_id`, `institution_medical_center_id`, `term_document_id`, `document_id`, `type`, `specialization_id`, `sub_specialization_id`, `treatment_id`, `country_id`, `city_id`, `specialization_name`, `sub_specialization_name`, `treatment_name`, `country_name`, `city_name`, `status`)
    #    SELECT NEW.`term_id`, `institution_id`, `institution_medical_center_id`, NEW.`id` as term_document_id, NEW.`document_id`, NEW.`type`, `specialization_id`, `sub_specialization_id`, `treatment_id`, `country_id`, `city_id`, `specialization_name`, `sub_specialization_name`, `treatment_name`, `country_name`, `city_name`, `status`
    #    FROM `search_terms` a
    #    WHERE a.`document_id` = NEW.`document_id`
    #    AND a.`type` = NEW.`type`;
    #    
    #    -- else statement here is most likely not to happen since we will be populating search_terms from actual entity names
    #END IF;
END; $$
### end term_documents_ai trigger definition

##----------------------
# AFTER DELETE term_documents trigger.
#
# name: `term_documents_ad`
# table: `term_documents`
# event: AFTER DELETE
# author: acgvelarde
DROP TRIGGER IF EXISTS term_documents_ad $$
CREATE TRIGGER term_documents_ad AFTER DELETE ON `term_documents`
FOR EACH ROW
BEGIN
    -- delete all search terms that is pointing to this term document
    DELETE FROM `search_terms` WHERE `term_document_id` = OLD.`id`;
END; $$
### end term_documents_ad
