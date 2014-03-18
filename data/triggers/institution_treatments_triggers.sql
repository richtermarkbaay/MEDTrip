####
# This sql file contains the definitions of triggers involving `institution_triggers` table.
#
# Note: DO NOT INCLUDE ANY OTHER DEFINITIONS FROM OTHER TABLES.
#
# @author: Allejo Chris G. Velarde
# @date: February 4, 2013
#

##----------------------
# AFTER INSERT institution_treatments trigger.
#
# name: `institution_treatments_ai`
# table: `institution_treatments`
# event: AFTER INSERT
# author: acgvelarde
DROP TRIGGER IF EXISTS institution_treatments_ai $$
CREATE TRIGGER institution_treatments_ai AFTER INSERT on `institution_treatments`
FOR EACH ROW
BEGIN
    SET @DOCUMENT_TYPE_TREATMENT = 3;
    SET @DOCUMENT_TYPE_SUB_SPECIALIZATION = 2;
    SET @ACTIVE_IMC_STATUS = 2;
    SET @ACTIVE_INSTITUTION_STATUS =9;
#
#    -- insert search terms for this added institution treatment
#    INSERT INTO `search_terms` (`term_id`, `institution_id`, `institution_medical_center_id`, `term_document_id`, `document_id`, `type`, `specialization_id`, `sub_specialization_id`, `treatment_id`, `country_id`, `city_id`, `specialization_name`, `sub_specialization_name`, `treatment_name`, `country_name`, `city_name`, `status`)
#    SELECT td.`term_id`, inst.`id` as institution_id, imc.`id` as institution_medical_center_id, td.`id` as term_document_id, td.`document_id`, td.`type`,  sp.`id` as specialization_id, NULL as sub_specialization_id, tr.id as treatment_id, inst.`country_id`, inst.`city_id`, sp.`name` as specialization, NULL as sub_specialization, tr.`name` as treatment, co.`name` as country, ci.`name` as city, (IF(imc.status = @ACTIVE_IMC_STATUS AND inst.status = @ACTIVE_INSTITUTION_STATUS, 1, 0)) as status
#    FROM `term_documents` td
#    INNER JOIN `treatments` tr ON td.`document_id` = tr.`id` AND td.`type` = @DOCUMENT_TYPE_TREATMENT
#    INNER JOIN `specializations` sp ON tr.`specialization_id` = sp.`id`
#    INNER JOIN `institution_specializations` ins_sp ON sp.`id` = ins_sp.`specialization_id`
#    INNER JOIN `institution_medical_centers` imc ON imc.`id` = ins_sp.`institution_medical_center_id`
#    INNER JOIN `institutions` inst ON inst.`id` = imc.`institution_id`
#    INNER JOIN `countries` co ON co.`id` = inst.`country_id`
#    LEFT JOIN `cities` ci ON ci.id = inst.`city_id`
#    WHERE ins_sp.`id` = NEW.`institution_specialization_id`
#    AND tr.id = NEW.`treatment_id`
#
#    -- union subspecializations of this added treatment
#    UNION
#    SELECT td.`term_id`, inst.`id` as institution_id, imc.`id` as institution_medical_center_id, td.`id` as term_document_id, td.`document_id`, td.`type`,  sp.`id` as specialization_id, sub_sp.id as sub_specialization_id, tr.id as treatment_id, inst.`country_id`, inst.`city_id`, sp.`name` as specialization, sub_sp.`name` as sub_specialization, tr.`name` as treatment, co.`name` as country, ci.`name` as city, (IF(imc.status = @ACTIVE_IMC_STATUS AND inst.status = @ACTIVE_INSTITUTION_STATUS, 1, 0)) as status
#    FROM `term_documents` td
#    INNER JOIN `sub_specializations` sub_sp ON td.`document_id` = sub_sp.`id` AND td.`type` = @DOCUMENT_TYPE_SUB_SPECIALIZATION
#    INNER JOIN `treatment_sub_specializations` tr_sub_sp ON sub_sp.`id` = tr_sub_sp.`sub_specialization_id`
#    INNER JOIN `treatments` tr ON  tr_sub_sp.`treatment_id` = tr.`id`
#    INNER JOIN `specializations` sp ON tr.`specialization_id` = sp.`id`
#    INNER JOIN `institution_specializations` ins_sp ON sp.`id` = ins_sp.`specialization_id`
#    INNER JOIN `institution_medical_centers` imc ON imc.`id` = ins_sp.`institution_medical_center_id`
#    INNER JOIN `institutions` inst ON inst.`id` = imc.`institution_id`
#    INNER JOIN `countries` co ON co.`id` = inst.`country_id`
#    LEFT JOIN `cities` ci ON ci.id = inst.`city_id`
#    WHERE 1
#    AND ins_sp.`id` = NEW.`institution_specialization_id`
#    AND tr.id = NEW.`treatment_id`;
END; $$
### end institution_treatments_ai trigger definition


##----------------------
# AFTER DELETE institution_treatments trigger.
#
# name: `institution_treatments_ad`
# table: `institution_treatments`
# event: AFTER DELETE
# author: acgvelarde
DROP TRIGGER IF EXISTS institution_treatments_ad $$
CREATE TRIGGER institution_treatments_ad AFTER DELETE ON `institution_treatments`
FOR EACH ROW
BEGIN
    SET @DOCUMENT_TYPE_TREATMENT = 3;
    SET @DOCUMENT_TYPE_SUB_SPECIALIZATION = 2;

    -- delete search terms that are pointing to this institution treatment
    SET @imc_id = (SELECT inst_sp.institution_medical_center_id FROM `institution_specializations` inst_sp WHERE `id` = OLD.`institution_specialization_id` LIMIT 1);

    -- todo: this is still 100% correct,
    -- unless we change the constraint in institution_specializations where institution_medical_center_id and specialization_id is a UNIQUE combination
    DELETE FROM `search_terms`
    WHERE `document_id` = OLD.treatment_id
    AND type = @DOCUMENT_TYPE_TREATMENT
    AND `institution_medical_center_id` = @imc_id;

    -- delete subspecializations of this institution treatment
    DELETE FROM `search_terms`
    WHERE treatment_id = OLD.treatment_id
    AND type = @DOCUMENT_TYPE_SUB_SPECIALIZATION
    AND `institution_medical_center_id` = @imc_id;
END; $$
### end institution_treatments_ad