============================================================
Projet : MyWatches — Gestion de collections de montres
Type : Application Web Symfony (front-office + base de données)
============================================================
1) Explication générale du site

MyWatches est un site pour gérer une collection personnelle de montres (Coffre)
et en publier une partie via des vitrines (plus tard). L’objectif est pédagogique :
appliquer Symfony + Doctrine + Twig, sans sur-ingénierie ni héritage d’entités.

2) Public visé

Visiteur (anonyme) : pages publiques basiques (à venir).

Membre : consultation et, plus tard, gestion de son Coffre/Montres.

Admin (optionnel) : back-office minimal (ultérieur).

3) Nomenclature

Objet → Montre

Inventaire → Coffre

Galerie → Vitrine (à implémenter plus tard)

4) Portée actuelle (MVP)

Consultation (front) :

Liste des Coffres : /

Fiche d’un Coffre : /coffre/{id}

Fiche d’une Montre : /montre/{id}

Données : SQLite + Fixtures de démo

Pas de formulaires publics ni de vitrine pour l’instant (étapes ultérieures)

5) Modèle de données (minimal, sans héritage)
Entités et propriétés

Coffre

id : entier, auto-incrément, PK

description : string (~255), NOT NULL

Montre

id : entier, auto-incrément, PK

description : string (~255), NOT NULL

marque : string (~120), NOT NULL

reference : string (~120), NOT NULL

annee : entier, NULLABLE

Associations

Coffre (1) — (0..n) Montre

Type : OneToMany (côté “Many” sur Montre)

Clé étrangère : Montre.coffre (NOT NULL)

Intégrité : une Montre appartient obligatoirement à UN Coffre

orphanRemoval : activé côté Coffre (suppression orpheline des Montres si détachées)

Contraintes et validations (actuelles)

Coffre.description : non vide

Montre.description / marque / reference : non vides

Montre.annee : NULLABLE ; si renseignée, entier

Montre.coffre : NOT NULL (FK obligatoire)

6) Architecture logique

src/Entity/ : entités Doctrine (Coffre, Montre)

src/Controller/ : contrôleurs front (CoffreController, MontreController)

templates/ : gabarits Twig

templates/base.html.twig

templates/coffre/list.html.twig

templates/coffre/show.html.twig

templates/montre/show.html.twig

src/DataFixtures/ : données de test (Fixtures avec yield)

.env : configuration (SQLite en dev/test)

7) Lancer le projet (développement local)

Prérequis : PHP, Composer, Symfony CLI.

Installer les dépendances :