ALTER TABLE vanda_work_orders DROP COLUMN omschrijving_klant;
ALTER TABLE vanda_work_orders DROP COLUMN opmerkingen;
ALTER TABLE vanda_work_orders
ADD COLUMN recurrence_type VARCHAR(20) DEFAULT NULL, -- daily, weekly, monthly
ADD COLUMN recurrence_interval INT DEFAULT 1,        -- bijv. elke 1 dag/week/maand
ADD COLUMN recurrence_until DATE DEFAULT NULL,       -- einddatum herhaling
ADD COLUMN recurrence_days VARCHAR(20) DEFAULT NULL; -- bijv. "Mon,Wed,Fri" (alleen bij weekly)