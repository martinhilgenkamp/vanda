ALTER TABLE vanda_work_orders DROP COLUMN opdrachtnr;
ALTER TABLE vanda_work_orders ADD COLUMN status VARCHAR(50) NOT NULL DEFAULT 'New';

