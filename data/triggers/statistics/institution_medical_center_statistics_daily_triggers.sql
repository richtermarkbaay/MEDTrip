DELIMITER $$
DROP TRIGGER IF EXISTS `institution_medical_center_statistics_daily_ai`$$

CREATE TRIGGER `institution_medical_center_statistics_daily_ai` AFTER INSERT ON `institution_medical_center_statistics_daily`
 FOR EACH ROW BEGIN
    DECLARE _annual_id BIGINT(20);
    -- update the count in consolidation table
    INSERT INTO `institution_medical_center_statistics_annual` (`date`, `institution_id`, `institution_medical_center_id`, `category_id`, `total`) 
    VALUES (NEW.`date`, NEW.`institution_id`,NEW.`institution_medical_center_id`, NEW.`category_id`, 1)
    ON DUPLICATE KEY UPDATE total = total+1;

    SELECT id INTO _annual_id FROM `institution_medical_center_statistics_annual` isa
    WHERE isa.date = NEW.`date`
    AND isa.institution_id = NEW.`institution_id`
    AND isa.institution_medical_center_id = NEW.`institution_medical_center_id`
    AND isa.category_id = NEW.`category_id`;

    INSERT INTO `institution_medical_center_statistics_annual_ip_addresses` (`institution_medical_center_statistics_annual_id`, `ip_addresses`) 
    VALUES ( _annual_id, NEW.`ip_address`)
    ON DUPLICATE KEY UPDATE ip_addresses = CONCAT(ip_addresses, ",", NEW.`ip_address`);
END;
$$