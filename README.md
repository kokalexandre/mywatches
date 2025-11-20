# =============================================
# MyWatches — Gestion de collections de montres
# Auteur : Alexandre KOK
# =============================================

## 1) Explication générale du site

MyWatches est une application Web Symfony permettant de gérer une collection personnelle de montres (**Coffre**) et d’en publier une partie via des galeries (**Vitrines**).  

Objectifs principaux :

- Mettre en pratique **Symfony + Doctrine + Twig + Bootstrap**
- Modèle de données clair, sans sur-ingénierie :
  - **Pas d’héritage d’entités**
  - Associations simples (1–1, 1–N, M–N)
- Mise en place de la **sécurité** :
  - Entité `Member` comme utilisateur
  - Rôles applicatifs (`ROLE_USER`, éventuellement `ROLE_ADMIN`)
  - Accès contextualisé aux données (coffre, vitrines, montres)

---

## 2) Rôles et public visé

- **Visiteur (anonyme)**  
  - Accès à la **page d’accueil**.
  - Accès à la **liste des vitrines publiques**.
  - Invitation à se connecter pour gérer sa propre collection.

- **Membre (`ROLE_USER`, authentifié, entité `Member`)**  
  - Dispose d’un **Coffre unique** (`Member` 1–1 `Coffre`) contenant ses **Montres**.
  - Peut créer / modifier / supprimer **ses montres**.
  - Peut créer des **Vitrines** (publiques ou privées) et y ajouter des montres de son coffre.
  - Voit :
    - Toutes les **vitrines publiques**.
    - **Ses vitrines privées** en plus.

- **Admin (`ROLE_ADMIN`) – optionnel / à compléter**  
  - Vocation à pouvoir consulter **toutes** les données (tous les coffres, toutes les vitrines…).  
  - Utile pour un usage « back-office » dans un contexte réel.

---

## 3) Comptes de test et rôles

Dans les **fixtures**, 5 membres de test sont créés.  
Sauf mention contraire, ils ont le **même mot de passe** : `123456`.

| Rôle fonctionnel (projet)                 | Email                 | Mot de passe | Remarques synthétiques                                     |
|-------------------------------------------|-----------------------|-------------|------------------------------------------------------------|
| Membre 1  | `test@localhost`   | `123456`    | Coffre orienté « quotidien & sport » plongeuses & GMT.   |
| Membre 2  | `nicolas@localhost`     | `123456`    | Coffre orienté montres habillées & classiques.            |
| Membre 3  | `alexandre@localhost`   | `123456`    | Montres vintage (plongeuses 60s, chronos…), plusieurs vitrines. |
| Membre 4  | `quentin@localhost`     | `123456`    | Sélection de marques indépendantes / micro-marques.       |
| Membre 5  | `catherine@localhost`  | `123456`    | Coffre orienté G-Shock, Casio digitales outdoor.         |

> **Rôles techniques** : dans la configuration actuelle, ces comptes sont créés avec le rôle `ROLE_USER`.  
> Un compte admin (`ROLE_ADMIN`) peut être ajouté ultérieurement si nécessaire (par mise à jour en base ou via un script dédié).

---

## 4) Gestion des images de montres (upload)

La fonctionnalité **« Ajout de la mise en ligne des images »** est implémentée :

- L’entité **`Montre`** contient un champ `imageFilename` (string, nullable) qui stocke le nom de fichier de la photo.
- Le formulaire **`MontreType`** propose un champ **fichier** (type `FileType`) non mappé, permettant d’uploader une image.
- Le contrôleur **`MontreController`** :
  - Récupère le fichier uploadé.
  - Crée le répertoire `public/uploads/montres` s’il n’existe pas.
  - Génère un nom de fichier unique (ex. `montre_XXXXX.jpg`).
  - Déplace le fichier dans `public/uploads/montres`.
  - Enregistre ce nom dans `Montre::imageFilename`.

Les images sont ensuite affichées :

- dans la fiche **privée** d’une montre,
- dans la liste des montres d’un **Coffre**,
- et dans la liste des montres d’une **Vitrine**.

⚠️ **Images des montres :**

- Les images uploadées sont stockées dans :  
  `public/uploads/montres`
- Pour des raisons de **propreté du dépôt** et de **taille**,  
  **aucune image n’est versionnée dans Git** (dossier ignoré via `.gitignore`).
- La **gestion technique des images (upload + affichage)** est néanmoins **fonctionnelle et testée** localement.  
  Il suffit d’uploader vos propres images en développement pour les voir apparaître.

---

## 5) Nomenclature du projet

- **Objet**      → `Montre`  
- **Inventaire** → `Coffre`  
- **Galerie**    → `Vitrine`  

Ces noms sont utilisés de façon systématique dans le code, les routes et les templates.

---

## 6) Portée fonctionnelle actuelle

### 6.1. Navigation générale & layout

- Layout principal : `templates/base.html.twig`
  - Barre de navigation Bootstrap :
    - **MyWatches** (brand) : lien vers la page d’accueil (route d’entrée de l’application).
    - Menu principal géré par **`bootstrap_menu`** (coffres, membres, vitrines…).
    - Bouton **Vitrines** vers la liste des vitrines visibles.
  - Zone droite : **authentification** :
    - Si **connecté** : affichage de l’email + lien **Se déconnecter**.
    - Si **non connecté** : bouton **Connexion**.

### 6.2. Page d’accueil (`/`)

Route principale : `coffre_list` (dans `CoffreController`).

Comportement cible :

- **Visiteur non connecté**
  - Voit une **présentation du site** (objectif : gérer sa collection, publier des vitrines).
  - Appel clair à l’action : **bouton de connexion**.
  - Pas d’accès direct aux coffres (données privées).

- **Membre connecté (`ROLE_USER`)**
  - Si un **Coffre** est associé au membre :
    - La page d’accueil affiche **uniquement son Coffre** (et les montres qu’il contient).
  - Si aucun Coffre n’est associé :
    - Message pour expliquer la situation (ou créer un Coffre, selon les besoins).

- **Admin (`ROLE_ADMIN`)**
  - Peut voir la **liste complète** des Coffres (fonctionnement plus proche d’un back-office).

> Si nécessaire, le contrôleur `CoffreController::list()` peut être adapté pour charger des données différentes selon `isGranted('ROLE_ADMIN')` et selon l’utilisateur courant (`$this->getUser()`).

### 6.3. Membres (`/member`)

(Partie front-office, non destinée à la gestion back-office des comptes.)

- `GET /member` → **liste** des membres (utile surtout en phase pédagogique / debug).
- `GET /member/{id}` (`MemberController::show`) :
  - Affiche les infos du membre (Id, email, etc.).
  - Lien **Mon Coffre** si un Coffre est associé.
  - Liste des **Vitrines** créées par ce membre :
    - Vitrines **publiques**
    - + Vitrines **privées** (si on est le propre membre connecté).
  - Lien **Créer une nouvelle Vitrine** contextualisé sur ce membre.

### 6.4. Coffres & Montres

- **Coffres**
  - Chaque `Member` a **un seul Coffre** (`Member` 1–1 `Coffre`).
  - Fiche Coffre : `/coffre/{id}` (`coffre_show`) :
    - Tableau d’infos du Coffre.
    - **Liste des montres** associées (OneToMany).
    - Pour chaque montre :
      - lien vers la **fiche de gestion privée** de la montre,
      - affichage éventuel d’une **miniature** (image).
    - Bouton **Ajouter une montre** :
      - Redirige vers `/montre/new/{id_du_coffre}` (création contextualisée).
    - Bouton **Retour au membre**.

- **Montres (CRUD membre privé)**
  - `GET /montre/new/{id}` (`MontreController::new`) :
    - `id` = identifiant du Coffre.
    - Le Coffre est injecté et fixé sur la Montre (`setCoffre`).
    - Champ Coffre affiché en **lecture seule** dans le formulaire.
  - `GET /montre/{id}` :
    - Affiche tous les champs de la montre + image éventuelle.
    - Boutons :
      - **Retour au Coffre**
      - **Modifier cette montre**
      - **Supprimer**
  - `GET /montre/{id}/edit` :
    - Édition de la montre (champs descriptifs + changement d’image).
    - Redirection après édition : retour à la **fiche Coffre**.
  - Suppression :
    - Redirection vers la **fiche Coffre** correspondante.

### 6.5. Vitrines

- CRUD généré via `make:crud` puis **contextualisé** :

  - `GET /vitrine` (`app_vitrine_index`) :
    - Affiche :
      - Toutes les **vitrines publiques** (`publiee = true`).
      - + les **vitrines privées** du membre connecté.
    - Design amélioré (Bootstrap) : affichage sous forme de **cartes** avec compteur de montres.

  - `GET /vitrine/{id}` (`app_vitrine_show`) :
    - Infos de la vitrine (description, statut publiée / non).
    - **Liste des montres** liées (ManyToMany) :
      - Titre + éventuelle **miniature** si `imageFilename` est renseigné.
      - Lien vers une page `Montre (publique)` dans le contexte de la vitrine.
    - Bouton **Retour au membre créateur**.
    - Bouton **Modifier cette vitrine** + formulaire de **suppression**.

  - Création contextualisée :
    - Route de type : `GET /member/{id}/vitrine/new`
    - Le `createur` (Member) est fixé à partir de l’URL et le champ correspondant est **désactivé** dans le formulaire.

  - Édition d’une Vitrine (`/vitrine/{id}/edit`) :
    - Formulaire `VitrineType` :
      - Filtre la liste des **Montres sélectionnables** :
        - ne propose que les montres du Coffre du **créateur** de la vitrine.
      - Gère l’association ManyToMany avec les options de formulaire adéquates (`by_reference => false`, sélection multiple…).
    - Redirection après édition : retour à la fiche du **Member créateur**.

  - Suppression d’une Vitrine :
    - Redirection vers `MemberController::show` du créateur.

### 6.6. Affichage public d’une Montre via une Vitrine

- Route de type :  
  `/vitrine/{vitrine_id}/montre/{montre_id}` → `VitrineController::montreShow()`
- Vérifications effectuées :
  - La montre est bien **présente dans la vitrine**.
  - La vitrine est **publique** (ou accessible selon la logique de visibilité).
- Affiche une page « **Montre (publique)** » :
  - Champs principaux de la montre (+ image éventuelle).
  - Lien de retour **« Retour à la vitrine »**.

---

## 7) Modèle de données

### 7.1. Entités principales

**Member**

- `id` : int, PK  
- `email` : string(180), unique, NOT NULL  
- `roles` : array, NOT NULL (au moins `ROLE_USER`)  
- `password` : string, NOT NULL (hashé)  
- Associations :
  - `coffre`   : OneToOne vers `Coffre` (un membre ↔ un Coffre)
  - `vitrines` : OneToMany vers `Vitrine` (`createur`)

**Coffre**

- `id`          : int, PK  
- `description` : string(~255), NOT NULL  
- Associations :
  - `member`  : OneToOne (propriétaire du Coffre)
  - `montres` : OneToMany vers `Montre`

**Montre**

- `id`            : int, PK  
- `description`   : string(~255), NOT NULL  
- `marque`        : string(~120), NOT NULL  
- `reference`     : string(~120), NOT NULL  
- `annee`         : int, NULLABLE  
- `imageFilename` : string, NULLABLE (nom de fichier image)  
- Associations :
  - `coffre`   : ManyToOne vers `Coffre` (NOT NULL)  
  - `vitrines` : ManyToMany vers `Vitrine`

**Vitrine**

- `id`          : int, PK  
- `description` : string(~255), NOT NULL  
- `publiee`     : bool (`true` = publique, `false` = privée)  
- Associations :
  - `createur` : ManyToOne vers `Member`  
  - `montres`  : ManyToMany vers `Montre`  

### 7.2. Associations globales

- `Member` (1) — (1) `Coffre`  
- `Coffre` (1) — (0..n) `Montre`  
- `Member` (1) — (0..n) `Vitrine`  
- `Vitrine` (M) — (N) `Montre`  

---

## 8) Authentification

Fonctionnalité **« Ajout de l’authentification »** :

- L’entité **`Member`** implémente `UserInterface` et `PasswordAuthenticatedUserInterface`.
- Configuration **`security.yaml`** :
  - Provider `app_user_provider` basé sur `App\Entity\Member` (propriété `email`).
  - Pare-feu `main` pour l’application.
- Les mots de passe sont **hashés** (mécanisme basé sur le password hasher Symfony).
- La barre de navigation affiche :
  - L’état de connexion (`app.user`),
  - Un bouton **Connexion** (si non connecté),
  - Un lien **Déconnexion** (si connecté).

L’authentification permet ensuite de **contextualiser le chargement des données** dans les contrôleurs :

- Si l’utilisateur a `ROLE_ADMIN` → accès aux données complètes (findAll).
- Sinon → accès limité aux **données liées au `Member` courant** (coffre, montres, vitrines…).

---

## 9) Architecture succincte du projet

- `src/Entity/`
  - `Member.php`
  - `Coffre.php`
  - `Montre.php`
  - `Vitrine.php`

- `src/Controller/`
  - `CoffreController.php` (page d’accueil, fiches coffres)
  - `MontreController.php` (CRUD privé sur les montres)
  - `VitrineController.php` (CRUD vitrines, vues publiques / privées)
  - `MemberController.php` (liste + fiche des membres)
  - `SecurityController.php` (si généré par `make:security:form-login` : login / logout)

- `src/Form/`
  - `MontreType.php` (formulaire avec upload d’image)
  - `VitrineType.php` (formulaire avec filtrage des montres par membre + gestion ManyToMany)
  - Autres `*Type` générés par `make:crud`.

- `src/DataFixtures/`
  - `AppFixtures.php` (génère les 5 membres + leur coffre + montres + vitrines liées).

- `templates/`
  - `base.html.twig` (layout général, menu, login/logout)
  - `coffre/` (liste, show)
  - `montre/` (CRUD privé, show avec image)
  - `vitrine/` (index, show, edit, montre_show publique)
  - `member/` (index, show)
  - `security/login.html.twig` (formulaire d’authentification, si généré)

- `public/`
  - `css/styles.css`, `js/scripts.js` (thème Bootstrap)
  - `uploads/montres/` (fichiers images uploadés)


