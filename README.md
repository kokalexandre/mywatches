# ===============================================
# MyWatches — Gestion de collections de montres
# Auteur : Alexandre KOK
# ===============================================

## 1) Explication du site
MyWatches est un site pour gérer une collection personnelle de montres et publier une partie via des vitrines (ultérieur). L’objectif est pédagogique : appliquer Symfony + Doctrine + Twig, sans sur-ingénierie ni héritage d’entités.

## 2) Public visé
- Visiteur (anonyme) : pages publiques basiques (à venir).
- Membre : consultation et, plus tard, gestion de son Coffre/Montres.
- Admin (optionnel) : back-office minimal (ultérieur).

## 3) Nomenclature
- Objet      → Montre  
- Inventaire → Coffre  
- Galerie    → Vitrine (à implémenter plus tard)

## 4) Portée actuelle
- Consultation (front) :
  - Liste des Coffres : `/`
  - Fiche d’un Coffre : `/coffre/{id}`
  - Fiche d’une Montre : `/montre/{id}`
- Données : SQLite + Fixtures de démonstration
- Pas de formulaires publics ni de vitrine pour l’instant (étapes ultérieures)

## 5) Modèle de données
------------------------------------------------------------
Entités et propriétés
------------------------------------------------------------

Coffre
- `id`          : entier, auto-incrément, PK
- `description` : string (~255), NOT NULL

Montre
- `id`          : entier, auto-incrément, PK
- `description` : string (~255), NOT NULL
- `marque`      : string (~120), NOT NULL
- `reference`   : string (~120), NOT NULL
- `annee`       : entier, NULLABLE

------------------------------------------------------------
Associations
------------------------------------------------------------

Coffre (1) — (0..n) Montre
- Type          : OneToMany (côté “Many” sur Montre)
- Clé étrangère : `Montre.coffre` (NOT NULL)
- Intégrité     : une Montre appartient obligatoirement à UN Coffre
- Suppression   : `orphanRemoval` activé côté Coffre (si détachée du Coffre)

------------------------------------------------------------
Contraintes et validations
------------------------------------------------------------

- `Coffre.description` : non vide
- `Montre.description / marque / reference` : non vides
- `Montre.annee` : NULLABLE ; si renseignée, entier
- `Montre.coffre` : NOT NULL (FK obligatoire)

## 6) Lancer le projet (développement local)
Prérequis : PHP ≥ 8.1, Composer, Symfony CLI.
