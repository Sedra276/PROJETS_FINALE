-- ============================================================
-- MOBILE MONEY - VERSION 1
-- Base SQLite unique a la racine du projet
-- Correspond exactement au MCD (6 entites)
-- ============================================================

PRAGMA foreign_keys = ON;

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
