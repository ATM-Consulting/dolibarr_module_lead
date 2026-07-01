# CLAUDE.md — module Dolibarr `lead`

## Description

Module Dolibarr (éditeur **ATM Consulting**) de gestion des **opportunités commerciales
(leads)** : objet Lead rattaché aux tiers/propositions/commandes/contrats/factures, avec
statuts (gagné/perdu), types, montant prospecté, et widgets de tableau de bord.

Hooks (`module_parts['hooks']`) : `commonobject`, `commcard`, `propalcard`, `contractcard`,
`ordercard`, `searchform`, `invoicecard`, `thirdpartycard`. `models=1`.

## Stack & compatibilité

- PHP (voir `phpmin` du descriptor), Dolibarr ≥ valeur `need_dolibarr_version`.
- Tables propres : `llx_lead`, `llx_c_lead_status`, `llx_c_lead_type`, `llx_lead_extrafields`.
- `backport/` : classes cœur vendorisées pour Dolibarr anciens (rétro-compat).

## Structure

| Chemin | Rôle |
|---|---|
| `core/modules/modLead.class.php` | Descriptor (`numero=103111`, `version`, `module_parts`) |
| `class/lead.class.php` | Objet métier Lead ; `fetchAll(...,$filter=array())` à filtre **tableau** (SQL maison) ; `load_previous_next_ref_custom()` (navigation ‹‹/››, SQL maison) |
| `class/actions_lead.class.php` | Classe de hook (cartes, widgets) |
| `class/html.formlead.class.php` | Formulaires de sélection Lead |
| `core/boxes/` | Widgets dashboard (leads en retard / courants) |
| `lead/` | Pages du module (`list.php`, fiches…) |
| `lib/`, `langs/`, `doc/`, `sql/`, `ChangeLog.md` | Utilitaires, traductions, docs, tables, journal |

## Build / test

- Pas de suite de tests automatisée embarquée.
- Vérif syntaxe : `find . -name '*.php' -not -path './vendor/*' -exec php -l {} \;` (gate = 0 erreur).
- Gates qualité ATM au commit (ordre fixe) : `lint → tests → claudemd → docs → techatm → local`
  via `~/.config/team-ai/githooks/commit-msg` (dry-run : `TAC_CHECK_ONLY=1 …`).

## Conventions

- **Code, commentaires, logs et identifiants en anglais.** Les chaînes utilisateur passent par `langs/`.
- `fetchAll`/`load_previous_next_ref_custom` construisent leur propre SQL : préférer des requêtes
  préparées / `$db->escape` (dette préexistante de concaténation de filtre à surveiller).
- Comparaisons strictes (`===`), échappement à l'affichage.
- **Releases de compatibilité** : une passe de compat majeure produit *toujours* une release,
  même sans changement de code. Bump **PATCH** du `$this->version` dans le descriptor +
  une ligne en tête du bloc `## Release X.Y` du `ChangeLog.md`, au format existant :
  `- FIX : Compat V<NN> - **DD/MM/YYYY** - X.Y.Z`.
- **Branches / PR** : ligne de version `X.Y` (la plus récente = base), branche de fix
  `FIX/COMPAT/V<NN>`, PR vers la ligne de version **et** PR `PULLUP:` vers `main`.
- Repo : `git@github.com:ATM-Consulting/dolibarr_module_lead.git`, branche par défaut `main`.
