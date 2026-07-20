Init projet CodeIgniter4 (via composer), config SQLite dans .env (chemin writable/database.sqlite), .gitignore (vendor, .env, writable/cache, writable/session, writable/logs), vérifier que l'app démarre.
Layout général : header (nom app + navigation), footer, intégration Bootstrap (CDN), organisation des vues (views/layouts, views/operateur, views/client).
Création de base.sql : les 5 tables ci-dessus + insertion des données de seed (préfixes 033/037, les 3 types d'opération, la grille de frais complète).
Login automatique par numéro de téléphone : formulaire (1 champ numéro), contrôleur qui vérifie le préfixe, crée le compte si besoin, ouvre la session, redirige vers le tableau de bord client. Message d'erreur clair si préfixe invalide.
Écran opérateur : CRUD des préfixes (liste, ajout avec validation format 2-3 chiffres + unicité, activer/désactiver ou supprimer).
Écran client : affichage du solde (numéro, solde formaté), avec liens vers dépôt / retrait / transfert / historique.
Écran opérateur : gestion des types d'opérations et de leurs barèmes de frais (CRUD des tranches : montant_min, montant_max, frais ; validation que les tranches ne se chevauchent pas).
Squelette des formulaires client dépôt / retrait / transfert (champ montant, + champ numéro destinataire pour le transfert), validations basiques (montant > 0), sans le calcul de frais ni la mise à jour du solde.
Écran opérateur : situation globale (total des soldes, total des frais collectés, nombre de comptes, nombre de transactions, liste des comptes avec leur solde).
Logique métier dépôt : mise à jour du solde du compte (solde + montant), création de la transaction (frais = 0), mise à jour de la session.
Logique métier retrait : calcul des frais selon la grille, vérification solde suffisant (solde >= montant + frais), mise à jour du solde, création de la transaction, mise à jour de la session.
Logique métier transfert : vérification destinataire existe, calcul des frais, vérification solde suffisant, mise à jour des soldes expéditeur et destinataire, création de la transaction avec compte_destination_id, mise à jour de la session.
