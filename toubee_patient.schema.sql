-- Adminer 4.8.1 PostgreSQL 16.4 (Debian 16.4-1.pgdg120+1) dump

DROP TABLE IF EXISTS "patient";
CREATE TABLE "public"."patient" (
    "id" integer NOT NULL,
    "nom" character varying(30) NOT NULL,
    "prenom" character varying(30) NOT NULL,
    "ville" character varying(30) NOT NULL,
    CONSTRAINT "patient_id" PRIMARY KEY ("id")
) WITH (oids = false);


-- 2024-10-08 15:24:10.765249+00