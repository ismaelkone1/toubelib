-- Insertion dans la table "specialite"
INSERT INTO public.specialite (id, label, description)
VALUES 
('1d2e3f4g-5678-9101-1121-3141h6i7j8k9', 'CAR', 'Cardiologue spécialisé en soins cardiaques'),
('2e3f4g5h-6789-1011-2131-4151i7j8k9l0', 'DER', 'Dermatologue spécialisé en maladies de la peau'),
('3f4g5h6i-7890-1121-3141-5161j8k9l0m1', 'ORT', 'Orthopédiste spécialisé en soins des os');

-- Insertion dans la table "praticien"
INSERT INTO public.praticien (id, nom, prenom, tel, adresse, specialite_id)
VALUES 
('4g5h6i7j-8901-1121-3141-6171k9l0m1n2', 'Lemoine', 'Sophie', '0102030405', '123 Rue de la Paix, Paris', '1d2e3f4g-5678-9101-1121-3141h6i7j8k9'),
('5h6i7j8k-9011-2131-4151-7181l0m1n2o3', 'Petit', 'Paul', '0607080910', '456 Rue Nationale, Lyon', '2e3f4g5h-6789-1011-2131-4151i7j8k9l0'),
('6i7j8k9l-0121-3141-5161-8191m2n3o4p5', 'Morel', 'Anne', '0506070809', '789 Rue de la République, Marseille', '3f4g5h6i-7890-1121-3141-5161j8k9l0m1');
