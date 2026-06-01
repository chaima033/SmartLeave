# SmartLeave / TalentFlow AI

Application Laravel 12 de recrutement avec deux roles: candidat et recruteur, et un assistant IA branche sur Gemini.

## Fonctionnalites

- Inscription, connexion et deconnexion
- Gestion du profil candidat et du profil entreprise
- Creation et gestion de CV
- Recherche et consultation d'offres d'emploi
- Postulation aux offres et suivi des candidatures
- Gestion des candidatures cote recruteur
- Consultation des CV par les recruteurs
- Assistant IA contextualise avec historique conversationnel

## Stack

- PHP 8.2+
- Laravel 12
- Blade + Tailwind CSS
- SQLite par defaut, compatible MySQL et PostgreSQL
- Gemini API pour l'assistant IA

## Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
```

## Execution

```bash
php artisan serve
```

En developpement:

```bash
npm run dev
```

## Variables .env

- `APP_URL`
- `DB_CONNECTION`
- `GEMINI_API_KEY`
- `GEMINI_MODEL` optionnel

## Comptes de demonstration

- Candidat: `karim@example.com`
- Recruteur: `recrutement@novalink.io`
- Mot de passe: `password`

## API Chatbot

```http
POST /api/chatbot
```

Exemple:

```json
{
    "message": "Quels CV sont disponibles ?",
    "mode": "candidate",
    "user_id": 1
}
```

Reponse:

```json
{
    "reply": "..."
}
```

## Tests

```bash
php artisan test
```

## Qualite

- Validation centralisee via Form Requests
- Historisation des echanges IA dans `ai_messages`
- Pages d'erreur personnalisees `403`, `404`, `500`

## Documentation

- [Architecture](docs/architecture.md)
- [Modele de donnees](docs/database-erd.md)
- [Prompt IA](docs/chatbot-prompt.md)
- [Dialogues de demonstration](docs/demo-dialogues.md)
- [Plan de rapport](docs/report-outline.md)
- [Matrice de verification](docs/requirements-matrix.md)
