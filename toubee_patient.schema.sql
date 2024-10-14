DROP TABLE IF EXISTS "patient";
CREATE TABLE "public"."patient" (
    "id" character varying(36) NOT NULL,
    "nom" character varying(30) NOT NULL,
    "prenom" character varying(30) NOT NULL,
    "ville" character varying(30) NOT NULL,
    CONSTRAINT "patient_id" PRIMARY KEY ("id")
) WITH (oids = false);