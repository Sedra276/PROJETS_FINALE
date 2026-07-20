# Todolist - Version 1 (Mobile Money)

## Taches transversales

- [ ] Creer le depot Git public, structure du projet CI4, README, gitignore
- [ ] Installer CodeIgniter 4 et configurer SQLite embarque
- [ ] Creer base.sql a la racine (une section par etudiant : ETU004141 / ETU004220)
- [ ] Mettre en place le layout Bootstrap de base (navbar, header, footer)
- [ ] Creer et tenir a jour Taches.md a la racine
- [ ] Remplir le formulaire Google avec les infos du binome
- [ ] Merger sur main, creer le tag v1 avant 13h

## ETU004141 - Cote operateur

- [x] Login agent/admin (AuthOperateur)
- [x] CRUD prefixes, format 2-3 chiffres, jamais de suppression physique (Prefixes)
- [x] CRUD types d'operation (TypesOperations)
- [x] CRUD baremes de frais, montant_min < montant_max, pas de chevauchement, valeur >= 0, historisation par date_fin_validite (Baremes)
- [x] FraisCalculatorService.calculerFrais() - point unique de calcul des frais
- [x] Dashboard gains, total des frais par type d'operation (DashboardOperateur::afficherGains)
- [x] Vue comptes clients, lecture seule (DashboardOperateur::afficherComptesClients)

## ETU004220 - Cote client

- [ ] Modele Client (numero, solde, statut, date_creation)
- [ ] Modele Operation (montant, frais, soldes avant/apres, date, type, source, destination)
- [ ] Login automatique par numero, verification du prefixe, creation auto si inconnu
- [ ] Page "Voir le solde"
- [ ] Depot (frais = 0, mise a jour du solde)
- [ ] Retrait (verification du solde, frais via FraisCalculatorService)
- [ ] Transfert (verification solde + destinataire, frais via FraisCalculatorService, transaction SQL tout ou rien)
- [ ] Historique paginee, filtrable par type et periode

## Checklist livraison V1 (avant 13h, tag v1)

**ETU004141**
- [x] Login admin/agent
- [x] CRUD prefixes
- [x] CRUD types d'operation
- [x] CRUD baremes de frais
- [x] FraisCalculatorService livre
- [x] Dashboard gains
- [x] Vue comptes clients

**ETU004220**
- [ ] Login auto client
- [ ] Voir le solde
- [ ] Depot
- [ ] Retrait
- [ ] Transfert
- [ ] Historique filtrable

**Commun**
- [ ] base.sql unique a la racine
- [ ] Taches.md a jour
- [ ] Tag v1 pousse avant 13h
