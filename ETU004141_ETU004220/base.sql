-- ============================================================
-- MOBILE MONEY - VERSION 1
-- Base SQLite unique a la racine du projet
-- Correspond exactement au MCD (6 entites)
-- ============================================================

PRAGMA foreign_keys = ON;

-- Clean up existing tables to allow re-running the script safely
DROP TABLE IF EXISTS operation;
DROP TABLE IF EXISTS tranche_frais;
DROP TABLE IF EXISTS commission_interoperateur;
DROP TABLE IF EXISTS type_operation;
DROP TABLE IF EXISTS client;
DROP TABLE IF EXISTS utilisateur;
DROP TABLE IF EXISTS operateur_config;
-- ============================================================
-- === BINOME 1 - COTE OPERATEUR (referentiels & config) ===
-- ============================================================

-- OPERATEUR_CONFIG : prefixes valables (ex: 033, 037)
CREATE TABLE IF NOT EXISTS operateur_config (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT NOT NULL UNIQUE,
    libelle TEXT,
    actif INTEGER NOT NULL DEFAULT 1 CHECK (actif IN (0,1))
);

-- UTILISATEUR : comptes back-office (agents / admin)
CREATE TABLE IF NOT EXISTS utilisateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    login TEXT NOT NULL UNIQUE,
    mot_de_passe TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'AGENT' CHECK (role IN ('ADMIN','AGENT'))
);

-- TYPE_OPERATION : referentiel DEPOT / RETRAIT / TRANSFERT
CREATE TABLE IF NOT EXISTS type_operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE CHECK (code IN ('DEPOT','RETRAIT','TRANSFERT')),
    libelle TEXT NOT NULL
);

-- TRANCHE_FRAIS : bareme par tranche de montant, versionne
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

-- ============================================================
-- === BINOME 2 - COTE CLIENT (comptes & operations) ===
-- ============================================================

-- CLIENT : compte mobile money, cree automatiquement au 1er login
CREATE TABLE IF NOT EXISTS client (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_telephone TEXT NOT NULL UNIQUE,
    nom TEXT,
    prenom TEXT,
    solde REAL NOT NULL DEFAULT 0 CHECK (solde >= 0),
    statut TEXT NOT NULL DEFAULT 'ACTIF' CHECK (statut IN ('ACTIF','BLOQUE')),
    date_creation TEXT NOT NULL DEFAULT (datetime('now'))
);

-- OPERATION : table pivot, immuable (INSERT only, jamais d'UPDATE/DELETE)
CREATE TABLE IF NOT EXISTS operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_type_operation INTEGER NOT NULL,
    id_client_source INTEGER,          -- NULL si depot (pas de source)
    id_client_destination INTEGER,     -- NULL si retrait (pas de destination)
    id_utilisateur INTEGER,            -- NULL en v1 (operation automatique, pas d'agent)
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

-- ============================================================
-- === DONNEES INITIALES (seed) ===
-- ============================================================

-- Prefixes valables
INSERT INTO operateur_config (prefixe, libelle, actif) VALUES
    ('033', 'Operateur A', 1),
    ('037', 'Operateur B', 1);

-- Un admin par defaut (mot de passe en clair, pas de hachage - simplification v1)
INSERT INTO utilisateur (nom, login, mot_de_passe, role) VALUES
    ('Admin', 'admin', 'admin123', 'ADMIN');

-- Types d'operation (ids : 1=DEPOT, 2=RETRAIT, 3=TRANSFERT)
INSERT INTO type_operation (code, libelle) VALUES
    ('DEPOT', 'Depot'),
    ('RETRAIT', 'Retrait'),
    ('TRANSFERT', 'Transfert');

-- Bareme de frais (exemple donne dans le sujet) applique au RETRAIT (id=2)
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

-- Meme bareme applique au TRANSFERT (id=3) -- a ajuster si le sujet en donne un different
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

-- Le DEPOT (id=1) n'a volontairement aucune tranche_frais :
-- FraisCalculatorService doit renvoyer 0 quand aucune tranche n'est trouvee.

ALTER TABLE tranche_frais ADD COLUMN id_operateur_config INTEGER REFERENCES operateur_config(id);

-- ============================================================
-- ============================================================
-- MOBILE MONEY - VERSION 2
-- Ajouts multi-operateurs, commission interoperateur,
-- frais de retrait inclus, envoi multiple (lot)
-- ============================================================
-- ============================================================

-- ------------------------------------------------------------
-- === BINOME 1 - COTE OPERATEUR (multi-operateurs) ===
-- ------------------------------------------------------------

-- Distinguer notre operateur des operateurs externes (Orange, Airtel, Telma)
ALTER TABLE operateur_config ADD COLUMN est_notre_operateur INTEGER NOT NULL DEFAULT 0 CHECK (est_notre_operateur IN (0,1));

-- COMMISSION_INTEROPERATEUR : commission additionnelle appliquee uniquement
-- quand le transfert sort vers un operateur externe (id_operateur_config = operateur destination)
CREATE TABLE IF NOT EXISTS commission_interoperateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_operateur_config INTEGER NOT NULL,
    pourcentage REAL NOT NULL CHECK (pourcentage >= 0 AND pourcentage <= 100),
    actif INTEGER NOT NULL DEFAULT 1 CHECK (actif IN (0,1)),
    FOREIGN KEY (id_operateur_config) REFERENCES operateur_config(id)
);

CREATE INDEX IF NOT EXISTS idx_commission_interop_operateur ON commission_interoperateur(id_operateur_config);

-- ------------------------------------------------------------
-- === BINOME 2 - COTE CLIENT (operation : nouveaux champs) ===
-- ------------------------------------------------------------

-- Frais de retrait inclus par l'expediteur au moment du transfert
ALTER TABLE operation ADD COLUMN frais_retrait_inclus INTEGER NOT NULL DEFAULT 0 CHECK (frais_retrait_inclus IN (0,1));
ALTER TABLE operation ADD COLUMN montant_frais_retrait_inclus REAL NOT NULL DEFAULT 0;

-- Regroupement des operations issues d'un envoi multiple (meme lot)
ALTER TABLE operation ADD COLUMN id_lot_envoi TEXT;

CREATE INDEX IF NOT EXISTS idx_operation_lot_envoi ON operation(id_lot_envoi);

-- Traçabilite "frais de retrait deja paye" sur le compte destinataire :
-- une operation de retrait qui beneficie d'un frais deja paye a l'avance
-- doit pouvoir etre identifiee. On reutilise frais_appliques = 0 sur ce retrait,
-- avec une reference vers l'operation de transfert d'origine :
ALTER TABLE operation ADD COLUMN id_operation_frais_retrait_origine INTEGER REFERENCES operation(id);

-- ------------------------------------------------------------
-- === DONNEES V2 (seed) ===
-- ------------------------------------------------------------

-- Marquer notre operateur (033) comme etant le notre, les autres restent externes
UPDATE operateur_config SET est_notre_operateur = 1 WHERE prefixe = '033';

-- Ajout des prefixes des operateurs externes (Orange, Airtel, Telma)
INSERT INTO operateur_config (prefixe, libelle, actif, est_notre_operateur) VALUES
    ('032', 'Orange', 1, 0),
    ('031', 'Airtel', 1, 0),
    ('034', 'Telma', 1, 0);

-- Commission interoperateur par operateur externe (exemple : 2%)
-- id_operateur_config correspond a l'operateur EXTERNE de destination
INSERT INTO commission_interoperateur (id_operateur_config, pourcentage, actif)
SELECT id, 2.0, 1 FROM operateur_config WHERE prefixe IN ('032', '031', '034');
