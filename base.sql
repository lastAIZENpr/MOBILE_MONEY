-- Base de données Mobile Money
-- Tables et données de seed

-- Table operateurs_externes
CREATE TABLE IF NOT EXISTS operateurs_externes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    taux_commission REAL NOT NULL
);

-- Table prefixes
CREATE TABLE IF NOT EXISTS prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT NOT NULL UNIQUE,
    actif INTEGER NOT NULL DEFAULT 1,
    operateur_externe_id INTEGER,
    FOREIGN KEY (operateur_externe_id) REFERENCES operateurs_externes(id)
);

-- Table types_operation
CREATE TABLE IF NOT EXISTS types_operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE,
    libelle TEXT NOT NULL
);

-- Table baremes_frais
CREATE TABLE IF NOT EXISTS baremes_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    montant_min INTEGER NOT NULL,
    montant_max INTEGER NOT NULL,
    frais INTEGER NOT NULL,
    FOREIGN KEY (type_operation_id) REFERENCES types_operation(id)
);

-- Table comptes_clients
CREATE TABLE IF NOT EXISTS comptes_clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_telephone TEXT NOT NULL UNIQUE,
    solde INTEGER NOT NULL DEFAULT 0,
    credit_frais_retrait INTEGER NOT NULL DEFAULT 0,
    date_creation TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table transactions
CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    compte_id INTEGER NOT NULL,
    type_operation_id INTEGER NOT NULL,
    montant INTEGER NOT NULL,
    frais INTEGER NOT NULL DEFAULT 0,
    solde_apres INTEGER NOT NULL,
    date_transaction TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    compte_destination_id INTEGER,
    FOREIGN KEY (compte_id) REFERENCES comptes_clients(id),
    FOREIGN KEY (type_operation_id) REFERENCES types_operation(id),
    FOREIGN KEY (compte_destination_id) REFERENCES comptes_clients(id)
);

-- Données de seed

-- Opérateurs externes
INSERT INTO operateurs_externes (nom, taux_commission) VALUES ('Orange Money', 2.5);
INSERT INTO operateurs_externes (nom, taux_commission) VALUES ('MobiCash', 3.0);

-- Préfixes valides (notre opérateur)
INSERT INTO prefixes (prefixe, actif, operateur_externe_id) VALUES ('033', 1, NULL);
INSERT INTO prefixes (prefixe, actif, operateur_externe_id) VALUES ('037', 1, NULL);

-- Préfixes externes (Orange Money)
INSERT INTO prefixes (prefixe, actif, operateur_externe_id) VALUES ('032', 1, 1);
INSERT INTO prefixes (prefixe, actif, operateur_externe_id) VALUES ('031', 1, 1);

-- Préfixes externes (MobiCash)
INSERT INTO prefixes (prefixe, actif, operateur_externe_id) VALUES ('034', 1, 2);

-- Types d'opérations
INSERT INTO types_operation (code, libelle) VALUES ('depot', 'Dépôt');
INSERT INTO types_operation (code, libelle) VALUES ('retrait', 'Retrait');
INSERT INTO types_operation (code, libelle) VALUES ('transfert', 'Transfert');

-- Barèmes de frais (pour retrait et transfert uniquement)
-- Grille de frais par défaut
-- 100 – 1 000 : 50
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 100, 1000, 50);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 100, 1000, 50);

-- 1 001 – 5 000 : 50
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 1001, 5000, 50);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 1001, 5000, 50);

-- 5 001 – 10 000 : 100
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 5001, 10000, 100);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 5001, 10000, 100);

-- 10 001 – 25 000 : 200
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 10001, 25000, 200);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 10001, 25000, 200);

-- 25 001 – 50 000 : 400
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 25001, 50000, 400);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 25001, 50000, 400);

-- 50 001 – 100 000 : 800
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 50001, 100000, 800);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 50001, 100000, 800);

-- 100 001 – 250 000 : 1 500
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 100001, 250000, 1500);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 100001, 250000, 1500);

-- 250 001 – 500 000 : 1 500
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 250001, 500000, 1500);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 250001, 500000, 1500);

-- 500 001 – 1 000 000 : 2 500
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 500001, 1000000, 2500);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 500001, 1000000, 2500);

-- 1 000 001 – 2 000 000 : 3 000
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (2, 1000001, 2000000, 3000);
INSERT INTO baremes_frais (type_operation_id, montant_min, montant_max, frais) VALUES (3, 1000001, 2000000, 3000);
