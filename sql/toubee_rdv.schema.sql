-- Adminer 4.8.1 PostgreSQL 16.4 (Debian 16.4-1.pgdg120+1) dump

DROP TABLE IF EXISTS "rdv";
CREATE TABLE "public"."rdv" (
    "id" character varying(36) NOT NULL,
    "id_praticien" character varying(36) NOT NULL,
    "id_patient" character varying(36) NOT NULL,
    "id_spe" character varying(36) NOT NULL,
    "type" character varying(20) NOT NULL,
    "statut" character varying(20) NOT NULL,
    CONSTRAINT "rdv_id" PRIMARY KEY ("id")
) WITH (oids = false);


-- 2024-10-08 15:23:55.410038+00