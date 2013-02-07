DROP TRIGGER IF EXISTS `advertisement_statistics_daily_ai` $$
CREATE TRIGGER advertisement_statistics_daily_ai AFTER INSERT on `advertisement_statistics_daily`
FOR EACH ROW
BEGIN
    -- update the count in consolidation table
    INSERT INTO `advertisement_statistics_annual` (`date`, `advertisement_id`, `institution_id`, `category_id`, `total`) 
    VALUES (NEW.`date`, NEW.`advertisement_id`, NEW.`institution_id`, NEW.`category_id`, 1)
    ON DUPLICATE KEY  UPDATE total = total+1;
END; $$