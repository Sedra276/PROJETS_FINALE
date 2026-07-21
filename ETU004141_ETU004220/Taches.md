# Todolist - Mobile Money

## Taches transversales V1

- [x] Creer le depot Git public, structure du projet CI4, README, gitignore
- [x] Installer CodeIgniter 4 et configurer SQLite embarque
- [x] Verifier que le projet demarre correctement
- [x] Creer base.sql a la racine (section ETU004141 / section ETU004220)
- [x] Mettre en place le layout Bootstrap de base (navbar, header, footer)
- [x] Creer et tenir a jour Taches.md a la racine
- [x] Remplir le formulaire Google avec les infos du binome
- [x] Merger sur main, creer le tag v1 avant 13h

## ETU004141 - Cote operateur V1

**Authentification**
- [x] Login agent/admin (AuthOperateur)
- [x] Deconnexion
- [x] Filtre de protection des pages back-office

**Prefixes**
- [x] Liste des prefixes
- [x] Creation d'un prefixe, format 2-3 chiffres, unique
- [x] Modification d'un prefixe
- [x] Activation / desactivation d'un prefixe (jamais de suppression physique)

**Types d'operation**
- [x] Liste des types d'operation
- [x] Creation et modification (DEPOT, RETRAIT, TRANSFERT)

**Baremes de frais**
- [x] Liste des tranches, filtrable par type d'operation et par operateur
- [x] Formulaire de creation d'une tranche (type_calcul, montant_min, montant_max, valeur)
- [x] Validation montant_min < montant_max
- [x] Validation pas de chevauchement entre tranches actives du meme type et du meme operateur
- [x] Validation valeur >= 0, et si pourcentage alors valeur entre 0 et 100
- [x] Desactivation d'une tranche par date_fin_validite (historisation)
- [x] Bareme lie a l'operateur (id_operateur_config)

**Calcul des frais**
- [x] FraisCalculatorService.calculerFrais(idTypeOperation, idOperateurConfig, montant)

**Dashboard operateur**
- [x] Page "Situation gain", frais percus par type d'operation, filtrable par periode
- [x] Page "Situation des comptes clients", lecture seule
- [x] Menu / espace operateur regroupant les acces

## ETU004220 - Cote client V1

**Modeles**
- [x] Modele Client (numero_telephone, nom, prenom, solde, statut, date_creation)
- [x] Modele Operation (id_type_operation, id_client_source, id_client_destination, id_utilisateur, montant, frais_appliques, solde_avant/apres, date_operation, statut)

**Authentification**
- [x] Login automatique par numero de telephone
- [x] Verification du prefixe via operateur_config
- [x] Creation automatique du compte si numero inconnu et prefixe valide

**Solde**
- [x] Page "Voir le solde" du client connecte

**Depot**
- [x] Enregistrement de l'operation DEPOT
- [x] Mise a jour du solde (frais = 0)

**Retrait**
- [x] Verification solde suffisant (montant + frais <= solde)
- [x] Calcul des frais via FraisCalculatorService
- [x] Transaction SQL : debit solde + insertion operation, tout ou rien

**Transfert simple**
- [x] Verification solde suffisant
- [x] Verification destinataire existant et different de la source
- [x] Calcul des frais via FraisCalculatorService
- [x] Transaction SQL : debit source + credit destination + insertion operation, tout ou rien

**Historique**
- [x] Liste paginee des operations du client connecte
- [x] Filtrable par type d'operation
- [x] Filtrable par periode

**Regles de gestion**
- [x] Client BLOQUE ne peut faire aucune operation
- [x] Aucune operation ne rend un solde negatif
- [x] Table operation immuable

---

## VERSION 2 (livraison 17h10, tag v2)

### Base de donnees - a ajouter dans base.sql

-[x]  operateur_config : champ est_notre_operateur (booleen) pour distinguer notre operateur des operateurs externes (Orange, Airtel, Telma)
-[x]  Nouvelle table commission_interoperateur : id, id_operateur_config (operateur destination), pourcentage, actif -> commission additionnelle appliquee uniquement si le transfert sort vers un operateur externe
-[x]  operation : champ frais_retrait_inclus (booleen) et montant_frais_retrait_inclus, pour un transfert ou l'expediteur paie d'avance le futur retrait du destinataire
-[x]  operation : champ id_lot_envoi (nullable) pour regrouper les operations issues d'un envoi multiple

### ETU004141 - Cote operateur V2

**Configuration multi-operateurs**
-[x]  Ajouter les prefixes des autres operateurs (032 Orange, 031 Airtel, 034 Telma...)
-[x]  Marquer un seul operateur comme est_notre_operateur = vrai, les autres = faux
-[x]  Formulaire de configuration de commission_interoperateur par operateur externe (pourcentage)
-[x]  Validation : pourcentage entre 0 et 100
-[x]  La commission ne s'applique que si le prefixe destinataire est externe (033 vers 032 par exemple) ; un transfert interne (033 vers 037) reste au bareme normal sans commission

**Dashboard gains V2**
-[x]  Bloc "gains interne" : retraits + transferts internes uniquement
-[x]  Bloc "autres operateurs" : total separe par operateur externe (Orange, Airtel, Telma chacun sa ligne), pas un seul total externe
-[x]  Filtrable par periode

**Nouvelle page**
-[x]  Page "Situation des montants a envoyer a chaque operateur"
-[x]  Pour chaque transfert externe valide, cumuler le montant NET envoye (pas les frais/commission) par operateur destinataire
-[x]  Affichage sous forme de tableau : operateur / montant total a reverser
-[x]  Filtrable par periode
-[x]  Lecture seule sur operation, aucune ecriture

### ETU004220 - Cote client V2

**Transfert - commission interoperateur**
-[x]  Detecter si le prefixe du destinataire est interne ou externe (via operateur_config)
-[x]  Si externe : ajouter la commission_interoperateur au montant du au bareme normal
-[x]  Transaction SQL : debit source (montant + frais + commission si externe) + credit destination (montant net) + insertion operation, tout ou rien

**Transfert - option frais de retrait inclus**
-[x]  Case a cocher "inclure les frais de retrait" sur le formulaire de transfert
-[x]  Si coche : calculer le frais de retrait applicable au destinataire via FraisCalculatorService et l'ajouter au montant debite chez l'expediteur
-[x]  Le destinataire recoit le montant plein, sans frais a payer lors de son futur retrait (a tracer, ex: statut "frais retrait deja paye" sur l'operation ou sur le solde credite)
-[x]  Enregistrer le detail complet sur l'operation (montant, frais transfert, commission si externe, frais retrait inclus)
-[x]  Transaction SQL globale incluant tous ces montants, tout ou rien

**Transfert - envoi multiple**
-[x]  Formulaire avec plusieurs couples (numero destinataire, montant)
-[x]  Pour chaque destinataire, determiner interne/externe et calculer ses propres frais/commission individuellement (pas un frais global unique)
-[x]  Calculer le debit total = somme des montants + somme des frais/commissions de chaque destinataire
-[x]  Verifier le solde source suffisant sur ce total avant toute ecriture
-[x]  Verifier que chaque destinataire existe et est different de la source
-[x]  Transaction SQL unique : debit source une fois pour le total, credit chaque destinataire, insertion d'une operation par destinataire liee au meme id_lot_envoi, tout ou rien (si un seul destinataire est invalide, on annule tout le lot)

**Historique**
-[x]  Regrouper l'affichage des operations partageant le meme id_lot_envoi (envoi multiple)
-[x]  Afficher le detail par destinataire (numero, montant) dans le lot

## Checklist livraison V2 (avant 17h10, tag v2)

**ETU004141**
-[x]  Config prefixes autres operateurs + marquage notre operateur
-[x]  Commission interoperateur configurable par operateur externe
-[x]  Situation gain separee (interne / par operateur externe)
-[x]  Page situation des montants a envoyer a chaque operateur

**ETU004220**
-[x]  Commission interoperateur appliquee sur transfert externe (transaction SQL)
-[x]  Option frais de retrait inclus sur transfert (transaction SQL)
-[x]  Envoi multiple avec calcul par destinataire (transaction SQL unique tout ou rien)
-[x]  Historique groupe par lot d'envoi

**Commun**
-[x]  base.sql a jour avec les nouvelles tables/champs V2
-[x]  Taches.md a jour
-[x]  Tag v2 pousse avant 17h10


## Alea ETU004220
Promotion en % pour le transfert au meme operateur que le notre 
    -creation table promotion 
