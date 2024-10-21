# toubelib : ASSAL Hugo KONE Ismael Martinez Galo 

## fichier .ini à définir pour les bases de données, patient, praticien, rdv
driver=pgsql
host=toubeelib.db
database=patient
username=
password=

## fichier toubeelibdb.env 
POSTGRES_USER=
POSTGRES_PASSWORD=
POSTGRES_DB=toubeelib  
POSTGRES_MULTIPLE_DATABASES=patients,praticiens,rdv

## fichier toubeelib.env
JWT_SECRET_KEY = 'secret'

La fonctionnalité "Contrôle d’autorisation pour accéder à un praticien" n'a pas pu être terminée.

# Données de test : 

 routes :
 POST
    /auth/signin
    ## body 
    {
        "email": "utilisateur@example.com",
        "password": "totot"
    }

POST
    /rdvs
    ## body 
    {
    "idPatient": "8o9p0q1r-2131-4151-6171-9202n3o4p5q6",
    "creneau": "2024-10-13T10:00:00",
    "praticien": "4g5h6i7j-8901-1121-3141-6171k9l0m1n2",
    "specialitee": "1d2e3f4g-5678-9101-1121-3141h6i7j8k9",
    "type": "Consultation",
    "statut": "Confirmé"
    }

GET
    /rdvs/7j8k9l0m-1121-3141-5161-9201n3o4p5q6

PATCH
    /rdvs/7j8k9l0m-1121-3141-5161-9201n3o4p5q6
    ## body 
    {
    "patient" : "3c4d5e6f-7890-1121-3141-5161c7d8e9f0",
    "specialitee" : "3f4g5h6i-7890-1121-3141-5161j8k9l0m1"
    }

DELETE
    /rdvs/7j8k9l0m-1121-3141-5161-9201n3o4p5q6

GET
    /praticiens/4g5h6i7j-8901-1121-3141-6171k9l0m1n2/disponibilites?debut=2024-06-01T08:00:00&fin=2024-06-01T18:00:00

GET
    /praticiens/4g5h6i7j-8901-1121-3141-6171k9l0m1n2

GET
    /praticiens

GET
    /praticiens/4g5h6i7j-8901-1121-3141-6171k9l0m1n2/planning?debut=2022-06-01T08:00:00&fin=2025-06-01T18:00:00&specialitee=CAR&type=Consultation

GET
    /patients/8o9p0q1r-2131-4151-6171-9202n3o4p5q6/rdvs