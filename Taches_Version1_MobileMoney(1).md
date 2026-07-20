

## 1. MCD (Modèle Conceptuel de Données)

### Entités et cardinalités

```
OPERATEUR_CONFIG (1,n) --- (0,n) CLIENT           via règle de préfixe (pas de FK directe, juste validation)
TYPE_OPERATION (1,1) --- (0,n) TRANCHE_FRAIS
TYPE_OPERATION (1,1) --- (0,n) OPERATION
CLIENT (0,1) --- (0,n) OPERATION   [rôle "source"]
CLIENT (0,1) --- (0,n) OPERATION   [rôle "destination"]
UTILISATEUR (0,1) --- (0,n) OPERATION   [agent ayant exécuté, nullable en v1 car "automatique"]
```

### Détail des entités

**OPERATEUR_CONFIG** — référentiel des préfixes valides
- id (PK)
- prefixe
- libelle
- actif

**UTILISATEUR** — comptes back-office (agents / admin)
- id (PK)
- nom
- login
- mot_de_passe (haché)
- role (ADMIN, AGENT)

**CLIENT**
- id (PK)
- numero_telephone (unique)
- nom, prenom
- solde
- statut (ACTIF, BLOQUE)
- date_creation

**TYPE_OPERATION** — référentiel (DEPOT, RETRAIT, TRANSFERT)
- id (PK)
- code
- libelle

**TRANCHE_FRAIS** — barème, **versionné pour supporter un changement de règles sans perdre l'historique**
- id (PK)
- id_type_operation (FK)
- montant_min
- montant_max
- **type_calcul** (`MONTANT_FIXE` ou `POURCENTAGE`) ← champ clé pour la maintenabilité, voir §2
- valeur (montant fixe OU taux selon type_calcul)
- date_debut_validite
- date_fin_validite (NULL = barème actuellement actif)

**OPERATION** — table pivot, jamais modifiée après création (traçabilité)
- id (PK)
- id_type_operation (FK)
- id_client_source (FK, nullable)
- id_client_destination (FK, nullable)
- id_utilisateur (FK, nullable)
- montant
- frais_appliques
- solde_avant_source / solde_apres_source
- solde_avant_destination / solde_apres_destination
- date_operation
- statut (VALIDEE, ECHOUEE)

### Pourquoi ce MCD tient face à un changement de règles

1. **`type_calcul` + `valeur`** dans `tranche_frais` : si demain les frais passent de "montant fixe par tranche" à "pourcentage", **aucune nouvelle table ni migration lourde** — juste une nouvelle ligne de barème.
2. **Versionnement par date** (`date_debut_validite` / `date_fin_validite`) : un nouveau barème n'écrase jamais l'ancien, donc les opérations passées restent cohérentes avec les frais qui étaient appliqués au moment T.
3. **`operation` est en écriture seule après création** : aucune règle de gestion future ne doit permettre de modifier une opération historique, seulement d'en créer de nouvelles (ex: un remboursement = une nouvelle opération, pas une modification).
4. **Le calcul des frais est isolé dans une seule classe** (`FraisCalculatorService`, voir §3), jamais dupliqué dans les contrôleurs → si la règle change, on modifie un seul endroit.

---

## 2. Découpage en 2 binômes

Le sujet sépare déjà naturellement le travail en **Côté Opérateur** / **Côté Client** : c'est la meilleure frontière pour 2 binômes, car ils touchent des tables et des vues quasi disjointes.

### BINÔME 1 — Côté Opérateur (back-office)

**Modèles CI4 :** `OperateurConfigModel.php`, `TypeOperationModel.php`, `TrancheFraisModel.php`, `UtilisateurModel.php`
**Lib partagée (à fournir au Binôme 2) :** `app/Libraries/FraisCalculatorService.php`
→ méthode publique unique : `calculerFrais(int $idTypeOperation, float $montant): float`
Le Binôme 2 **ne doit jamais recalculer les frais lui-même**, il appelle uniquement ce service. Ça évite toute divergence si la règle change.

**Contrôleurs :** `AuthOperateur.php`, `Prefixes.php`, `TypesOperations.php`, `Baremes.php`, `DashboardOperateur.php`

**Routes / Pages :**
- `/admin/login`
- `/admin/prefixes`
- `/admin/types-operations`
- `/admin/baremes` (CRUD tranches de frais, avec date de validité)
- `/admin/gains` — total des frais perçus par type d'opération / période
- `/admin/comptes-clients` — liste des comptes clients (lecture seule sur la table `client`)

**Validation :**
- préfixe : format numérique 2-3 chiffres, obligatoire
- barème : montant_min < montant_max, pas de chevauchement de tranches actives, valeur >= 0
- si type_calcul = POURCENTAGE, valeur comprise entre 0 et 100

**À faire :**
- authentification agent/admin
- CRUD préfixes, types d'opération, barèmes (avec historisation, jamais de suppression physique d'un barème déjà utilisé)
- développer `FraisCalculatorService` en premier (le Binôme 2 en dépend dès le jour 1)
- dashboard gains (agrégation en lecture seule sur `operation`)
- vue "situation des comptes clients" (lecture seule sur `client`)

**Règles métier :**
- un barème désactivé (date_fin_validite renseignée) reste visible en historique mais n'est plus proposé au calcul
- le dashboard ne doit jamais écrire dans `operation` ou `client`

---

### BINÔME 2 — Côté Client

**Modèles CI4 :** `ClientModel.php`, `OperationModel.php`
**Contrôleurs :** `AuthClient.php`, `Solde.php`, `Depot.php`, `Retrait.php`, `Transfert.php`, `Historique.php`

**Routes / Pages :**
- `/client/login` — login automatique par numéro (création à la volée si préfixe valide et numéro inconnu)
- `/client/solde`
- `/client/depot`
- `/client/retrait`
- `/client/transfert`
- `/client/historique` (filtrable par type / période)

**Dépendances vers le Binôme 1 (à ne pas dupliquer) :**
- validation du préfixe → lit `operateur_config` (Binôme 1)
- calcul des frais → appelle `FraisCalculatorService::calculerFrais()` (Binôme 1)

**Validation :**
- numéro : format + préfixe valide (via config Binôme 1)
- montant > 0
- retrait/transfert : solde suffisant (montant + frais <= solde)
- transfert : destinataire existant et différent de la source

**À faire :**
- login auto + création client à la volée
- moteur transactionnel dépôt/retrait/transfert dans une transaction SQL (tout ou rien)
- écrire chaque opération dans `operation` avec soldes avant/après
- historique paginé et filtrable

**Règles métier :**
- un client au statut BLOQUE ne peut faire aucune opération
- aucune opération ne doit rendre un solde négatif
- l'historique n'est jamais supprimé ni modifié

---

## 3. Maintenabilité — règles à respecter par les deux binômes

- **Un seul point de calcul des frais** (`FraisCalculatorService`), personne ne réécrit la logique ailleurs.
- **Aucune règle de gestion en dur dans le code** (pas de `if montant > 100000` codé en dur) : tout passe par `tranche_frais` et `operateur_config`.
- **Table `operation` immuable** : pas d'`UPDATE`/`DELETE` dessus, uniquement des `INSERT`.
- **Migrations mentales, pas de suppression physique** des lignes de référentiel déjà utilisées (barème, type d'opération) — on les désactive (`actif` / `date_fin_validite`), jamais on ne les supprime, sinon les FK des anciennes opérations cassent.

---

## 4. Coordination Git à 2

- branches : `feature/binome-1-operateur`, `feature/binome-2-client`
- `base.sql` : une section commentée par binôme (`-- === BINOME 1 ===` / `-- === BINOME 2 ===`), le Binôme 1 livre `FraisCalculatorService.php` en premier pour ne pas bloquer le Binôme 2
- `Taches.md` : chaque étudiant ajoute ses lignes, ne réécrit pas celles de l'autre
- merge vers `main` + tag `v1` avant 13h

---

## 5. Checklist livraison V1 (13h, tag v1)

**Binôme 1**
- [ ] Login admin/agent
- [ ] CRUD préfixes
- [ ] CRUD types d'opération
- [ ] CRUD barèmes de frais (avec type_calcul + versionnement)
- [ ] `FraisCalculatorService.calculerFrais()` livré et testable seul
- [ ] Dashboard gains
- [ ] Vue comptes clients

**Binôme 2**
- [ ] Login auto client par numéro
- [ ] Voir solde
- [ ] Dépôt
- [ ] Retrait
- [ ] Transfert
- [ ] Historique filtrable

**Commun**
- [ ] `base.sql` unique à la raine, à jour
- [ ] `Taches.md` à jour
- [ ] Tag `v1` poussé avant 13h
