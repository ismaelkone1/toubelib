DROP TABLE IF EXISTS "patient";
CREATE TABLE "public"."patient" (
    "id" character varying(36) NOT NULL,
    "nom" character varying(30) NOT NULL,
    "prenom" character varying(30) NOT NULL,
    "ville" character varying(30) NOT NULL,
    "email" character varying(50) NOT NULL,
    "password" character varying(100) NOT NULL,
    "tel" character varying(15) NOT NULL,  -- Ajout de la colonne pour le téléphone
    "adresse" character varying(255) NOT NULL,  -- Ajout de la colonne pour les adresses
    CONSTRAINT "patient_id" PRIMARY KEY ("id"),
    CONSTRAINT "unique_email" UNIQUE ("email")
) WITH (oids = false);