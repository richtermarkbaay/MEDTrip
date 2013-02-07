CREATE EVENT advertisement_statistics_daily_event
ON SCHEDULE EVERY 5 SECOND
DO
BEGIN
    
    -- drop current AFTER INSERT trigger
    DROP TRIGGER IF EXISTS advertisement_statistics_daily_ai;
    
    
END; $$