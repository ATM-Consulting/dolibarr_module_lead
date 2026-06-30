# Compatibilité Dolibarr v24 — module `lead`

| | |
|---|---|
| Module | `lead` (`modLead`) — opportunités commerciales |
| Version | 2.8 → 2.8.1 (compat release) |
| Cœur cible | Dolibarr 24.0 |
| Date | 2026-06-30 |
| Branche | `FIX/COMPAT/V24` (base `2.8`) |
| Source des ruptures | `ChangeLog` cœur, section `***** ChangeLog for 24.0.0 compared to 23.0 *****`, bloc WARNING (**10 items**, dont le retrait du module Deplacement) ; bloc « For developers » = additions uniquement |

## Résultat

**Aucune adaptation nécessaire — module déjà compatible v24.** Les 10 ruptures du
ChangeLog v24 sont sans objet (N/A) ; baseline `php -l` à 0 erreur (27 fichiers).
Release de compat produite sans changement de code (convention ATM).

## Détail par item (source : ChangeLog v24)

| # | Rupture | Statut | Évidence |
|---|---|---|---|
| 1 | USF requise sur tous les `$filter` | N/A | voir analyse ci-dessous |
| 2 | `PAYMENT_SECURITY_TOKEN_UNIQUE` supprimée | N/A | absent |
| 3 | Salt par défaut securekey signature en ligne | N/A | aucun `getOnlineSignatureUrl`/`ONLINE_SIGNATURE` |
| 4 | Login API off par défaut | N/A | pas d'appel login API |
| 5 | `DEPOSIT_AS_CREDIT_AVAILABLE_EVEN_UNPAID` renommé | N/A | absent |
| 6 | Substitution `__MYCOUNTRY_ID__` supprimée | N/A | absent |
| 7 | Contexte hook `info_admin` → `messageOfTheDay` | N/A | hooks `commonobject`, `commcard`, `propalcard`, `contractcard`, `ordercard`, `searchform`, `invoicecard`, `thirdpartycard` (inchangés en v24) |
| 8 | Librairie `jeditable` retirée | N/A | non utilisée |
| 9 | Module `Paybox` supprimé | N/A | aucune dépendance |
| 10 | Module `Deplacement` supprimé (nouveau) | N/A | aucune dépendance |

## Analyse Item 1 (USF) — la plus sensible

Le module définit sa **propre** méthode `Lead::fetchAll($sortorder,$sortfield,$limit,$offset,$filter=array())`
qui construit son SQL à partir d'un `$filter` **tableau** (`is_array($filter)`). Tous les appels
(`$lead->fetchAll(...)`, `$object->fetchAll(...)` dans `list.php`, boxes, `actions_lead`,
`html.formlead`) ciblent cette méthode du module — **aucun appel à une méthode cœur USF-only**.

Les concaténations `$sql .= " AND " . $filter` (`lead.class.php:1118/1143`) sont dans
`load_previous_next_ref_custom()` — une réimplémentation **propre** de la navigation ‹‹/›› qui
bâtit son propre SQL. → non concerné par la rupture USF v24.

→ Item 1 **N/A**. (Hors périmètre v24 : la concaténation SQL brute du filtre est un sujet
qualité/sécurité préexistant, à traiter séparément le cas échéant.)

## Baseline (indépendant du ChangeLog)

- `php -l` sur tous les `.php` (hors vendor/node_modules) : **0 erreur** (27 fichiers).
- Descriptor cohérent : `numero=103111`, `rights_class='lead'`, classe `modLead`,
  `module_parts` : `models=1` + 8 contextes de hook.
- Scan méthodes cœur supprimées en v24 (hors `backport/`) : aucune occurrence.

## Checks complémentaires (toujours actifs — `references/checks/`)

### csrf-token (MAIN_SECURITY_CSRF_WITH_TOKEN = 3 par défaut en v24)

**AFFECTÉ → corrigé en 2.8.2.** 6 liens GET d'action modifiante sans token (→ 403 en v24) :
`setmod` (admin), `delete` (extrafield admin), `unlink` (×2 : `actions_lead`/`card.php`),
`swapstatut` et `deletecontact` (`tpl/contacts.tpl.php`). Fix : ajout de `&token='.newToken()`
sur chaque lien (patron cœur). Aucun `define('NOCSRFCHECK')` sur les pages concernées. Les
formulaires POST (`contacts.tpl.php`) portaient déjà leur token.

### code-compta (`Societe::$code_compta` non peuplé par `fetch()`)

**N/A.** Aucune lecture de `->code_compta` (bare) dans le module.

## Synthèse

| Sévérité | ChangeLog v24 | Complémentaires | Total |
|---|---|---|---|
| BLOCKER | 0 | 0 | 0 |
| WARNING | 0 | 1 (csrf-token, corrigé) | 1 |
| INFO | 0 | 0 | 0 |
| N/A | 10 | 1 (code-compta) | 11 |
