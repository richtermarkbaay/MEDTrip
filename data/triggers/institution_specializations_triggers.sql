####
# This sql file contains the definitions of triggers involving `institution_specializations` table.
#
# Note: DO NOT INCLUDE ANY OTHER DEFINITIONS FROM OTHER TABLES.
#
# @author: Allejo Chris G. Velarde
# @date: February 1, 2013
#

##----------------------
# AFTER INSERT institution_specializations trigger.
#
# name: `institution_specializations_ai`
# table: `institution_specializations`
# event: AFTER INSERT
# author: acgvelarde
DROP TRIGGER IF EXISTS institution_specializations_ai $$
CREATE TRIGGER institution_specializations_ai AFTER INSERT on `institution_specializations`
FOR EACH ROW
BEGIN
    DECLARE DOCUMENT_TYPE_SPECIALIZATION tinyint(3);
    SET DOCUMENT_TYPE_SPECIALIZATION = 1;
    #
    #IF NEW.status = 1 THEN
    #    -- insert search terms for this added institution specialization
    #    INSERT INTO `search_terms` (`term_id`, `institution_id`, `institution_medical_center_id`, `term_document_id`, `document_id`, `type`, `specialization_id`, `sub_specialization_id`, `treatment_id`, `country_id`, `city_id`, `specialization_name`, `sub_specialization_name`, `treatment_name`, `country_name`, `city_name`, `status`)
    #    SELECT td.`term_id`, inst.`id` as institution_id, imc.`id` as institution_medical_center_id, td.`id` as term_document_id, td.`document_id`, td.`type`,  sp.`id` as specialization_id, NULL as sub_specialization_id, NULL as treatment_id, inst.`country_id`, inst.`city_id`, sp.`name` as specialization, NULL as sub_specialization, NULL as treatment, co.`name` as country, ci.`name` as city, (IF(imc.status = 2 AND inst.status = 9, 1, 0)) as status
    #    FROM `term_documents` td
    #    INNER JOIN `specializations` sp ON td.`document_id` = sp.id AND td.`type` = DOCUMENT_TYPE_SPECIALIZATION
    #    INNER JOIN `institution_specializations` ins_sp ON sp.`id` = ins_sp.`specialization_id`
    #    INNER JOIN `institution_medical_centers` imc ON imc.`id` = ins_sp.`institution_medical_center_id`
    #    INNER JOIN `institutions` inst ON inst.`id` = imc.`institution_id`
    #    INNER JOIN `countries` co ON co.`id` = inst.`country_id`
    #    LEFT JOIN `cities` ci ON ci.id = inst.`city_id`
    #    WHERE sp.`id` = NEW.`specialization_id`
    #    AND imc.id = NEW.`institution_medical_center_id`;
    #END IF;

END; $$
### end institution_specializations_ai trigger definition

##----------------------
# AFTER DELETE institution_specializations trigger.
#
# name: `institution_specializations_ad`
# table: `institution_specializations`
# event: AFTER DELETE
# author: acgvelarde
DROP TRIGGER IF EXISTS institution_specializations_ad $$
CREATE TRIGGER institution_specializations_ad AFTER DELETE ON `institution_specializations`
FOR EACH ROW
BEGIN
    DECLARE DOCUMENT_TYPE_SPECIALIZATION tinyint(3);
    SET DOCUMENT_TYPE_SPECIALIZATION = 1;

    -- delete search terms that are pointing to this institution specialization
    DELETE FROM `search_terms` WHERE document_id = OLD.`specialization_id`
    AND type = DOCUMENT_TYPE_SPECIALIZATION
    AND institution_medical_center_id = OLD.`institution_medical_center_id`;
END; $$
### end institution_specializations_ad
