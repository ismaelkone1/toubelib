 -- Adminer 4.8.1 PostgreSQL 16.4 (Debian 16.4-1.pgdg120+1) dump

DROP TABLE IF EXISTS "praticien";
CREATE TABLE "public"."praticien" (
    "id" integer NOT NULL,
    "nom" character varying(30) NOT NULL,
    "prenom" character varying(30) NOT NULL,
    "tel" character(10) NOT NULL,
    "adresse" character varying(30) NOT NULL,
    "specialite_id" integer NOT NULL,
    CONSTRAINT "praticien_id" PRIMARY KEY ("id")
) WITH (oids = false);


DROP TABLE IF EXISTS "specialite";
CREATE TABLE "public"."specialite" (
    "id" integer NOT NULL,
    "label" character varying(3) NOT NULL,
    "description" character varying(75) NOT NULL,
    CONSTRAINT "specialite_pkey" PRIMARY KEY ("id")
) WITH (oids = false);


ALTER TABLE ONLY "public"."praticien" ADD CONSTRAINT "praticien_specialite_id_fkey" FOREIGN KEY (specialite_id) REFERENCES specialite(id) NOT DEFERRABLE;

-- 2024-10-08 15:02:44.653951+00