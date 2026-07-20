# Todolist - Version 1 (Mobile Money)

Repartition pour un binome de 2 pers. Personne 1 s'occupe du cote operateur (back-office admin), Personne 2 s'occupe du cote client. Les taches transversales sont a faire ensemble en debut de sprint.

## Taches transversales (Binome)

- [ ] Creer le depot Git public (GitHub ou GitLab), structure du projet CI4, README, gitignore
- [ ] Installer CodeIgniter 4 et configurer SQLite embarque, verifier que le projet demarre
- [ ] Creer le fichier base.sql a la racine du projet (tables : operateur, prefixe, client, type_operation, tranche_frais, operation)
- [ ] Mettre en place le layout Bootstrap de base (navbar, header, footer)
- [ ] Creer et tenir a jour Taches.md a la racine (travaux effectues par etudiant a chaque livraison)
- [ ] Remplir le formulaire Google avec les infos du binome
- [ ] Verifier que tout fonctionne, merger sur main, creer le tag v1 avant 13h

## Cote operateur - Personne 1

- [ ] Creer le modele et la migration Operateur / Prefixe
- [ ] CRUD configuration des prefixes valables (ex : 033, 037)
- [ ] Creer le modele et la migration Type operation (depot, retrait, transfert)
- [ ] CRUD des baremes de frais par tranche de montant, lies au type d'operation, modifiables
- [ ] Validation : les tranches de montant ne doivent pas se chevaucher
- [ ] Page "Situation gain" : total des frais percus par type d'operation (retrait, transfert)
- [ ] Page "Situation des comptes clients" : liste des clients avec leur solde
- [ ] Dashboard / espace operateur regroupant les acces aux pages ci-dessus

## Cote client - Personne 2

- [ ] Creer le modele et la migration Client (numero, solde, date de creation)
- [ ] Login automatique par numero de telephone (creation auto du compte si inexistant, verification du prefixe)
- [ ] Page "Voir le solde" du client connecte
- [ ] Fonction "Faire un depot" (automatique, mise a jour du solde, enregistrement de l'operation)
- [ ] Fonction "Faire un retrait" (verification du solde, application des frais selon le bareme)
- [ ] Fonction "Faire un transfert" (verification du solde, destinataire existant, application des frais, mise a jour des deux comptes)
- [ ] Creer le modele et la migration Operation (montant, frais, date, solde avant/apres, type, emetteur, destinataire)
- [ ] Page "Historique des operations" du client connecte, filtrable par type

## Notes et hypothese

- Le solde est stocke directement sur le client et mis a jour a chaque operation.
- Le depot n'a pas de frais dans l'enonce, donc frais = 0 pour ce type d'operation.
- Le compte client est cree automatiquement au premier login, il n'y a pas d'inscription prealable.
- Ces regles de gestion peuvent encore changer selon les prochaines versions du sujet.