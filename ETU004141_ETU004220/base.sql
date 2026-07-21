PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS operation;
DROP TABLE IF EXISTS tranche_frais;
DROP TABLE IF EXISTS commission_interoperateur;
DROP TABLE IF EXISTS type_operation;
DROP TABLE IF EXISTS client;
DROP TABLE IF EXISTS utilisateur;
DROP TABLE IF EXISTS operateur_config;

CREATE TABLE IF NOT EXISTS operateur_config (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT NOT NULL UNIQUE,
    libelle TEXT,
    actif INTEGER NOT NULL DEFAULT 1 CHECK (actif IN (0,1))
);



CREATE TABLE IF NOT EXISTS utilisateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    login TEXT NOT NULL UNIQUE,
    mot_de_passe TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'AGENT' CHECK (role IN ('ADMIN','AGENT'))
);

CREATE TABLE IF NOT EXISTS type_operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE CHECK (code IN ('DEPOT','RETRAIT','TRANSFERT')),
    libelle TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS tranche_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_type_operation INTEGER NOT NULL,
    montant_min REAL NOT NULL,
    montant_max REAL NOT NULL,
    type_calcul TEXT NOT NULL DEFAULT 'MONTANT_FIXE' CHECK (type_calcul IN ('MONTANT_FIXE','POURCENTAGE')),
    valeur REAL NOT NULL CHECK (valeur >= 0),
    date_debut_validite TEXT NOT NULL DEFAULT (datetime('now')),
    date_fin_validite TEXT,
    FOREIGN KEY (id_type_operation) REFERENCES type_operation(id),
    CHECK (montant_min < montant_max)
);

CREATE INDEX IF NOT EXISTS idx_tranche_frais_type ON tranche_frais(id_type_operation);

CREATE TABLE IF NOT EXISTS client (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_telephone TEXT NOT NULL UNIQUE,
    nom TEXT,
    prenom TEXT,
    solde REAL NOT NULL DEFAULT 0 CHECK (solde >= 0),
    statut TEXT NOT NULL DEFAULT 'ACTIF' CHECK (statut IN ('ACTIF','BLOQUE')),
    date_creation TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_type_operation INTEGER NOT NULL,
    id_client_source INTEGER,
    id_client_destination INTEGER,
    id_utilisateur INTEGER,
    montant REAL NOT NULL CHECK (montant > 0),
    frais_appliques REAL NOT NULL DEFAULT 0,
    solde_avant_source REAL,
    solde_apres_source REAL,
    solde_avant_destination REAL,
    solde_apres_destination REAL,
    date_operation TEXT NOT NULL DEFAULT (datetime('now')),
    statut TEXT NOT NULL DEFAULT 'VALIDEE' CHECK (statut IN ('VALIDEE','ECHOUEE')),
    FOREIGN KEY (id_type_operation) REFERENCES type_operation(id),
    FOREIGN KEY (id_client_source) REFERENCES client(id),
    FOREIGN KEY (id_client_destination) REFERENCES client(id),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id)
);

CREATE INDEX IF NOT EXISTS idx_operation_source ON operation(id_client_source);
CREATE INDEX IF NOT EXISTS idx_operation_destination ON operation(id_client_destination);
CREATE INDEX IF NOT EXISTS idx_operation_date ON operation(date_operation);
CREATE INDEX IF NOT EXISTS idx_operation_utilisateur ON operation(id_utilisateur);

INSERT INTO operateur_config (prefixe, libelle, actif) VALUES
    ('033', 'Operateur A', 1),
    ('037', 'Operateur B', 1);

INSERT INTO utilisateur (nom, login, mot_de_passe, role) VALUES
    ('Admin', 'admin', 'admin123', 'ADMIN');

INSERT INTO type_operation (code, libelle) VALUES
    ('DEPOT', 'Depot'),
    ('RETRAIT', 'Retrait'),
    ('TRANSFERT', 'Transfert');

INSERT INTO tranche_frais (id_type_operation, montant_min, montant_max, type_calcul, valeur) VALUES
    (2, 100, 1000, 'MONTANT_FIXE', 50),
    (2, 1001, 5000, 'MONTANT_FIXE', 50),
    (2, 5001, 10000, 'MONTANT_FIXE', 100),
    (2, 10001, 25000, 'MONTANT_FIXE', 200),
    (2, 25001, 50000, 'MONTANT_FIXE', 400),
    (2, 50001, 100000, 'MONTANT_FIXE', 800),
    (2, 100001, 250000, 'MONTANT_FIXE', 1500),
    (2, 250001, 500000, 'MONTANT_FIXE', 1500),
    (2, 500001, 1000000, 'MONTANT_FIXE', 2500),
    (2, 1000001, 2000000, 'MONTANT_FIXE', 3000);

INSERT INTO tranche_frais (id_type_operation, montant_min, montant_max, type_calcul, valeur) VALUES
    (3, 100, 1000, 'MONTANT_FIXE', 50),
    (3, 1001, 5000, 'MONTANT_FIXE', 50),
    (3, 5001, 10000, 'MONTANT_FIXE', 100),
    (3, 10001, 25000, 'MONTANT_FIXE', 200),
    (3, 25001, 50000, 'MONTANT_FIXE', 400),
    (3, 50001, 100000, 'MONTANT_FIXE', 800),
    (3, 100001, 250000, 'MONTANT_FIXE', 1500),
    (3, 250001, 500000, 'MONTANT_FIXE', 1500),
    (3, 500001, 1000000, 'MONTANT_FIXE', 2500),
    (3, 1000001, 2000000, 'MONTANT_FIXE', 3000);

ALTER TABLE tranche_frais ADD COLUMN id_operateur_config INTEGER REFERENCES operateur_config(id);

UPDATE tranche_frais SET id_operateur_config = 1;

ALTER TABLE operateur_config ADD COLUMN est_notre_operateur INTEGER NOT NULL DEFAULT 0 CHECK (est_notre_operateur IN (0,1));

CREATE TABLE IF NOT EXISTS commission_interoperateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_operateur_config INTEGER NOT NULL,
    pourcentage REAL NOT NULL CHECK (pourcentage >= 0 AND pourcentage <= 100),
    actif INTEGER NOT NULL DEFAULT 1 CHECK (actif IN (0,1)),
    FOREIGN KEY (id_operateur_config) REFERENCES operateur_config(id)
);
CREATE TABLE IF NOT EXISTS promotion (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    pourcentage REAL NOT NULL CHECK (pourcentage >= 0 AND pourcentage <= 100),
    actif INTEGER NOT NULL DEFAULT 1 CHECK (actif IN (0,1)),
    id_operateur_config INTEGER NOT NULL,
    libelle TEXT NOT NULL,
    date_debut TEXT NOT NULL DEFAULT (datetime('now')),
    date_fin TEXT,
    FOREIGN KEY (id_operateur_config) REFERENCES operateur_config(id)

);

CREATE INDEX IF NOT EXISTS idx_commission_interop_operateur ON commission_interoperateur(id_operateur_config);

ALTER TABLE operation ADD COLUMN frais_retrait_inclus INTEGER NOT NULL DEFAULT 0 CHECK (frais_retrait_inclus IN (0,1));
ALTER TABLE operation ADD COLUMN montant_frais_retrait_inclus REAL NOT NULL DEFAULT 0;

ALTER TABLE operation ADD COLUMN id_lot_envoi TEXT;

CREATE INDEX IF NOT EXISTS idx_operation_lot_envoi ON operation(id_lot_envoi);

ALTER TABLE operation ADD COLUMN id_operation_frais_retrait_origine INTEGER REFERENCES operation(id);

UPDATE operateur_config SET est_notre_operateur = 1 WHERE prefixe = '033';

INSERT INTO operateur_config (prefixe, libelle, actif, est_notre_operateur) VALUES
    ('032', 'Orange', 1, 0),
    ('031', 'Airtel', 1, 0),
    ('034', 'Telma', 1, 0);

INSERT INTO commission_interoperateur (id_operateur_config, pourcentage, actif)
SELECT id, 2.0, 1 FROM operateur_config WHERE prefixe IN ('032', '031', '034');
