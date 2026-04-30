ALTER TABLE sanction
    ADD COLUMN tache_id INT NULL AFTER presence_id,
    ADD CONSTRAINT fk_sanction_tache
        FOREIGN KEY (tache_id)
        REFERENCES tache(id)
        ON DELETE SET NULL;