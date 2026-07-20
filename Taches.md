[Toi] Init projet CodeIgniter4 (via composer), config SQLite dans .env (chemin writable/database.sqlite), .gitignore (vendor, .env, writable/cache, writable/session, writable/logs), vérifier que l'app démarre.
[Binôme] Layout général : header (nom app + navigation), footer, intégration Bootstrap (CDN), organisation des vues (views/layouts, views/operateur, views/client).
[Toi] Création de base.sql : les 5 tables ci-dessus + insertion des données de seed (préfixes 033/037, les 3 types d'opération, la grille de frais complète).
[Binôme] Login automatique par numéro de téléphone : formulaire (1 champ numéro), contrôleur qui vérifie le préfixe, crée le compte si besoin, ouvre la session, redirige vers le tableau de bord client. Message d'erreur clair si préfixe invalide.
[Toi] Écran opérateur : CRUD des préfixes (liste, ajout avec validation format 2-3 chiffres + unicité, activer/désactiver ou supprimer).
