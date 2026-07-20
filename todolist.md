# Todolist - Version 1 (Mobile Money)

## Taches transversales

- [x] Creer le depot Git public, structure du projet CI4, README, gitignore
- [x] Installer CodeIgniter 4 et configurer SQLite embarque
- [x] Verifier que le projet demarre correctement
- [x] Creer base.sql a la racine (section ETU004141 / section ETU004220)
- [x] Mettre en place le layout Bootstrap de base (navbar, header, footer)
- [x] Creer et tenir a jour Taches.md a la racine
- [x] Remplir le formulaire Google avec les infos du binome
- [x] Merger sur main, creer le tag v1 avant 13h

## ETU004141 - Cote operateur

**Authentification**
- [x] Login agent/admin (AuthOperateur)
- [x] Deconnexion
- [x] Filtre de protection des pages back-office (AuthOperateurFilter)

**Prefixes**
- [x] Liste des prefixes (Prefixes::listerPrefixes)
- [x] Creation d'un prefixe, format 2-3 chiffres, unique
- [x] Modification d'un prefixe
- [x] Activation / desactivation d'un prefixe (jamais de suppression physique)

**Types d'operation**
- [x] Liste des types d'operation (TypesOperations)
- [x] Creation et modification (DEPOT, RETRAIT, TRANSFERT)

**Baremes de frais**
- [x] Liste des tranches, filtrable par type d'operation et par operateur (Baremes::listerBaremes)
- [x] Formulaire de creation d'une tranche (type_calcul MONTANT_FIXE ou POURCENTAGE, montant_min, montant_max, valeur)
- [x] Validation montant_min < montant_max
- [x] Validation pas de chevauchement entre tranches actives du meme type d'operation et du meme operateur
- [x] Validation valeur >= 0, et si pourcentage alors valeur entre 0 et 100
- [x] Desactivation d'une tranche par date_fin_validite (historisation, jamais de suppression)
- [x] Bareme lie a l'operateur en plus du type d'operation (id_operateur_config)

**Calcul des frais**
- [x] FraisCalculatorService.calculerFrais(idTypeOperation, idOperateurConfig, montant) - point unique de calcul

**Dashboard operateur**
- [x] Page "Situation gain", frais percus par type d'operation, filtrable par periode (DashboardOperateur::afficherGains)
- [x] Page "Situation des comptes clients", lecture seule (DashboardOperateur::afficherComptesClients)
- [x] Menu / espace operateur regroupant les acces

## ETU004220 - Cote client

**Modeles**
- [x] Modele Client (numero_telephone, nom, prenom, solde, statut, date_creation)
- [x] Modele Operation (id_type_operation, id_client_source, id_client_destination, id_utilisateur, montant, frais_appliques, solde_avant/apres, date_operation, statut)

**Authentification**
- [x] Login automatique par numero de telephone
- [x] Verification du prefixe via operateur_config (ETU004141)
- [x] Creation automatique du compte si numero inconnu et prefixe valide

**Solde**
- [x] Page "Voir le solde" du client connecte

**Depot**
- [x] Enregistrement de l'operation DEPOT
- [x] Mise a jour du solde (frais = 0)

**Retrait**
- [x] Verification solde suffisant (montant + frais <= solde)
- [x] Calcul des frais via FraisCalculatorService (jamais recalcule a la main)
- [x] Mise a jour du solde, enregistrement avec solde avant/apres

**Transfert**
- [x] Verification solde suffisant
- [x] Verification destinataire existant et different de la source
- [x] Calcul des frais via FraisCalculatorService
- [x] Mise a jour des deux comptes dans une transaction SQL tout ou rien

**Historique**
- [x] Liste paginee des operations du client connecte
- [x] Filtrable par type d'operation
- [x] Filtrable par periode

**Regles de gestion**
- [x] Client BLOQUE ne peut faire aucune operation
- [x] Aucune operation ne rend un solde negatif
- [x] Table operation immuable, aucune modification ni suppression

## Checklist livraison V1 (avant 13h, tag v1)

**ETU004141**
- [x] Login admin/agent
- [x] CRUD prefixes
- [x] CRUD types d'operation
- [x] CRUD baremes de frais (avec type_calcul, versionnement, par operateur)
- [x] FraisCalculatorService livre et testable seul
- [x] Dashboard gains
- [x] Vue comptes clients

**ETU004220**
- [x] Login auto client par numero
- [x] Voir le solde
- [x] Depot
- [x] Retrait
- [x] Transfert
- [x] Historique filtrable

**Commun**
- [x] base.sql unique a la racine, a jour
- [x] Taches.md a jour
- [x] Tag v1 pousse avant 13h
