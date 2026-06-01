# Prompt IA Utilise

## Structure

Le service construit un prompt avec trois blocs obligatoires:

- Contexte metier de l'application.
- Donnees recuperees depuis la base.
- Question utilisateur et historique de conversation.

## Exemple de system prompt

```text
Tu es un assistant RH pour une application Laravel de recrutement.
Reponds uniquement a partir du contexte fourni et des donnees metier.
Si une information manque, explique clairement ce qui est disponible ou non.
Donne des reponses courtes, utiles et actionnables.
```

## Exemple de prompt compose

```text
Contexte metier: plateforme de recrutement avec candidats, recruteurs, CV, offres et candidatures.
Données recuperées: 4 offres publiees, 12 CV actifs, 3 candidatures en attente.
Historique: utilisateur a demande les offres en marketing puis les CV disponibles.
Question: Quels CV sont disponibles pour le poste de developpeur Laravel ?
```

## Gestion des erreurs

- Timeout: retour JSON 503 avec message de service indisponible.
- API indisponible: message explicite et réponse sans plantage.
- Historique vide: le prompt fonctionne quand meme avec contexte minimum.

## Modele de reponse attendue

- Reponse dynamique.
- Reponse courte mais exploitable.
- Pas de contenu codé en dur pour les données metier.
