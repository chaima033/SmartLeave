# Modele Entite-Relation

```mermaid
erDiagram
    USERS ||--o| CANDIDATE_PROFILES : has
    USERS ||--o| COMPANY_PROFILES : has
    USERS ||--o{ RESUMES : owns
    USERS ||--o{ JOB_OFFERS : publishes
    USERS ||--o{ APPLICATIONS : submits
    USERS ||--o{ AI_MESSAGES : writes

    COMPANY_PROFILES ||--o{ JOB_OFFERS : groups
    JOB_OFFERS ||--o{ APPLICATIONS : receives
    RESUMES ||--o{ APPLICATIONS : referenced_by

    USERS {
        bigint id PK
        string name
        string email
        string role
        string phone
        string headline
        string location
    }

    CANDIDATE_PROFILES {
        bigint id PK
        bigint user_id FK
        text summary
        json skills
        string website
    }

    COMPANY_PROFILES {
        bigint id PK
        bigint user_id FK
        string company_name
        string company_industry
        string company_website
        text company_description
    }

    RESUMES {
        bigint id PK
        bigint user_id FK
        string title
        json skills
        boolean is_primary
    }

    JOB_OFFERS {
        bigint id PK
        bigint recruiter_id FK
        bigint company_profile_id FK
        string title
        string slug
        string status
        json skills
    }

    APPLICATIONS {
        bigint id PK
        bigint job_offer_id FK
        bigint candidate_id FK
        bigint resume_id FK
        string status
        json resume_snapshot
    }

    AI_MESSAGES {
        bigint id PK
        bigint user_id FK
        string mode
        text prompt
        text response
    }
```

## Relations clefs

- Un utilisateur peut avoir un profil candidat ou recruteur selon son role.
- Un candidat peut creer plusieurs CV, dont un CV principal.
- Un recruteur publie plusieurs offres.
- Une offre recoit plusieurs candidatures.
- Chaque echange IA est conserve pour maintenir le contexte.
