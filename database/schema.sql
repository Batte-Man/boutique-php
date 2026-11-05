-- Schéma de la base it_shop, reconstitué à partir des requêtes de l'application.
-- Le script SQL d'origine n'a pas été conservé. Les produits ci-dessous sont des
-- données de démonstration (noms, prix et notes indicatifs).

CREATE DATABASE IF NOT EXISTS it_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE it_shop;

CREATE TABLE IF NOT EXISTS admin (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    login             VARCHAR(50)  NOT NULL UNIQUE,
    mot_de_passe_hash VARCHAR(255) NOT NULL,          -- password_hash() ; 255 caractères recommandés
    role              ENUM('gerant', 'assistant') NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS produits (
    id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom                VARCHAR(150)  NOT NULL,
    description        TEXT          NOT NULL,
    prix               DECIMAL(10,2) NOT NULL CHECK (prix > 0),
    quantite_en_stock  INT UNSIGNED  NOT NULL DEFAULT 0,
    marque             VARCHAR(100)  NOT NULL,
    categorie          VARCHAR(100)  NOT NULL,
    evaluation_moyenne DECIMAL(2,1)  NOT NULL DEFAULT 0 CHECK (evaluation_moyenne BETWEEN 0 AND 5),
    statut             ENUM('disponible', 'indisponible') NOT NULL DEFAULT 'disponible',
    photo_produit      VARCHAR(255)  NOT NULL,
    date_ajout         DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_statut_date (statut, date_ajout)
) ENGINE=InnoDB;

INSERT INTO produits (nom, description, prix, quantite_en_stock, marque, categorie, evaluation_moyenne, statut, photo_produit, date_ajout) VALUES
('Anker PowerCore 20000', 'Produit de démonstration : Anker PowerCore 20000.', 49.99, 40, 'Anker', 'Accessoires', 4.0, 'disponible', 'anker_powercore.jpg', NOW() - INTERVAL 40 DAY),
('Apple Watch Series 7', 'Produit de démonstration : Apple Watch Series 7.', 429.00, 1, 'Apple', 'Objets connectés', 4.5, 'disponible', 'apple_watch.jpg', NOW() - INTERVAL 39 DAY),
('Bose Home Speaker 500', 'Produit de démonstration : Bose Home Speaker 500.', 299.95, 15, 'Bose', 'Audio', 4.4, 'disponible', 'bose_home_speaker.jpg', NOW() - INTERVAL 38 DAY),
('Bose QuietComfort 35 II', 'Produit de démonstration : Bose QuietComfort 35 II.', 279.95, 8, 'Bose', 'Audio', 4.0, 'disponible', 'bose_qc35.jpg', NOW() - INTERVAL 37 DAY),
('Bose SoundLink Revolve', 'Produit de démonstration : Bose SoundLink Revolve.', 199.95, 34, 'Bose', 'Audio', 4.0, 'disponible', 'bose_soundlink.jpg', NOW() - INTERVAL 36 DAY),
('Canon EOS M50', 'Produit de démonstration : Canon EOS M50.', 649.00, 37, 'Canon', 'Photo', 4.7, 'disponible', 'canon_eos_m50.jpg', NOW() - INTERVAL 35 DAY),
('Canon EOS R5', 'Produit de démonstration : Canon EOS R5.', 3999.00, 2, 'Canon', 'Photo', 3.8, 'disponible', 'canon_eos_r5.jpg', NOW() - INTERVAL 34 DAY),
('DJI Mavic Air 2', 'Produit de démonstration : DJI Mavic Air 2.', 849.00, 5, 'DJI', 'Photo', 4.4, 'indisponible', 'dji_mavic_air.jpg', NOW() - INTERVAL 33 DAY),
('Dyson Airwrap', 'Produit de démonstration : Dyson Airwrap.', 549.00, 14, 'Dyson', 'Maison', 3.8, 'disponible', 'dyson_airwrap.jpg', NOW() - INTERVAL 32 DAY),
('Dyson V11', 'Produit de démonstration : Dyson V11.', 599.00, 35, 'Dyson', 'Maison', 4.4, 'disponible', 'dyson_v11.jpg', NOW() - INTERVAL 31 DAY),
('Amazon Echo Dot', 'Produit de démonstration : Amazon Echo Dot.', 59.99, 34, 'Amazon', 'Objets connectés', 4.7, 'disponible', 'echo_dot.jpg', NOW() - INTERVAL 30 DAY),
('Fitbit Charge 5', 'Produit de démonstration : Fitbit Charge 5.', 149.95, 14, 'Fitbit', 'Objets connectés', 4.8, 'disponible', 'fitbit_charge5.jpg', NOW() - INTERVAL 29 DAY),
('Fitbit Versa 3', 'Produit de démonstration : Fitbit Versa 3.', 229.95, 37, 'Fitbit', 'Objets connectés', 4.5, 'disponible', 'fitbit_versa.jpg', NOW() - INTERVAL 28 DAY),
('Samsung Galaxy Buds Pro', 'Produit de démonstration : Samsung Galaxy Buds Pro.', 229.00, 0, 'Samsung', 'Audio', 4.2, 'disponible', 'galaxy_buds_pro.jpg', NOW() - INTERVAL 27 DAY),
('Samsung Galaxy S21', 'Produit de démonstration : Samsung Galaxy S21.', 859.00, 27, 'Samsung', 'Smartphones', 4.6, 'disponible', 'galaxy_s21.jpg', NOW() - INTERVAL 26 DAY),
('Samsung Galaxy Tab S7', 'Produit de démonstration : Samsung Galaxy Tab S7.', 699.00, 17, 'Samsung', 'Tablettes', 4.2, 'disponible', 'galaxy_tab_s7.jpg', NOW() - INTERVAL 25 DAY),
('Garmin Forerunner 945', 'Produit de démonstration : Garmin Forerunner 945.', 599.99, 13, 'Garmin', 'Objets connectés', 4.6, 'disponible', 'garmin_forerunner_945.jpg', NOW() - INTERVAL 24 DAY),
('GoPro HERO9 Black', 'Produit de démonstration : GoPro HERO9 Black.', 429.99, 6, 'GoPro', 'Photo', 4.0, 'disponible', 'gopro_hero9.jpg', NOW() - INTERVAL 23 DAY),
('GoPro MAX', 'Produit de démonstration : GoPro MAX.', 529.99, 24, 'GoPro', 'Photo', 4.0, 'disponible', 'gopro_max.jpg', NOW() - INTERVAL 22 DAY),
('HP Envy 13', 'Produit de démonstration : HP Envy 13.', 999.00, 22, 'HP', 'Ordinateurs', 4.6, 'disponible', 'hp_envy_13.jpg', NOW() - INTERVAL 21 DAY),
('Instant Pot Duo', 'Produit de démonstration : Instant Pot Duo.', 99.99, 38, 'Instant Pot', 'Maison', 4.5, 'indisponible', 'instant_pot_duo.jpg', NOW() - INTERVAL 20 DAY),
('Apple iPad Pro 11\"', 'Produit de démonstration : Apple iPad Pro 11\".', 899.00, 2, 'Apple', 'Tablettes', 4.8, 'disponible', 'ipad_pro.jpg', NOW() - INTERVAL 19 DAY),
('JBL Flip 5', 'Produit de démonstration : JBL Flip 5.', 119.99, 34, 'JBL', 'Audio', 4.0, 'disponible', 'jbl_flip5.jpg', NOW() - INTERVAL 18 DAY),
('Kindle Paperwhite', 'Produit de démonstration : Kindle Paperwhite.', 139.99, 24, 'Amazon', 'Tablettes', 4.0, 'disponible', 'kindle_paperwhite.jpg', NOW() - INTERVAL 17 DAY),
('KitchenAid Artisan', 'Produit de démonstration : KitchenAid Artisan.', 599.00, 35, 'KitchenAid', 'Maison', 4.5, 'disponible', 'kitchenaid_artisan.jpg', NOW() - INTERVAL 16 DAY),
('Logitech MX Master 3', 'Produit de démonstration : Logitech MX Master 3.', 109.99, 40, 'Logitech', 'Accessoires', 4.6, 'disponible', 'logitech_mx_master3.jpg', NOW() - INTERVAL 15 DAY),
('Apple MacBook Air M1', 'Produit de démonstration : Apple MacBook Air M1.', 1129.00, 36, 'Apple', 'Ordinateurs', 4.4, 'disponible', 'macbook_air.jpg', NOW() - INTERVAL 14 DAY),
('Nespresso Vertuo', 'Produit de démonstration : Nespresso Vertuo.', 169.00, 4, 'Nespresso', 'Maison', 3.8, 'disponible', 'nespresso_vertuo.jpg', NOW() - INTERVAL 13 DAY),
('Nintendo Switch', 'Produit de démonstration : Nintendo Switch.', 329.99, 14, 'Nintendo', 'Jeux vidéo', 4.5, 'disponible', 'nintendo_switch.jpg', NOW() - INTERVAL 12 DAY),
('Philips Hue Starter Kit', 'Produit de démonstration : Philips Hue Starter Kit.', 159.99, 5, 'Philips', 'Objets connectés', 4.4, 'disponible', 'philips_hue.jpg', NOW() - INTERVAL 11 DAY),
('Sony PlayStation 5', 'Produit de démonstration : Sony PlayStation 5.', 549.99, 6, 'Sony', 'Jeux vidéo', 4.7, 'disponible', 'ps5.jpg', NOW() - INTERVAL 10 DAY),
('Razer BlackWidow V3', 'Produit de démonstration : Razer BlackWidow V3.', 139.99, 17, 'Razer', 'Accessoires', 4.8, 'disponible', 'razer_blackwidow_v3.jpg', NOW() - INTERVAL 9 DAY),
('Ring Video Doorbell', 'Produit de démonstration : Ring Video Doorbell.', 99.99, 40, 'Ring', 'Objets connectés', 4.6, 'disponible', 'ring_video_doorbell.jpg', NOW() - INTERVAL 8 DAY),
('Samsung QLED 55\"', 'Produit de démonstration : Samsung QLED 55\".', 899.00, 10, 'Samsung', 'TV', 4.6, 'indisponible', 'samsung_qled.jpg', NOW() - INTERVAL 7 DAY),
('Sony Alpha 7 III', 'Produit de démonstration : Sony Alpha 7 III.', 1799.00, 22, 'Sony', 'Photo', 4.4, 'disponible', 'sony_alpha7.jpg', NOW() - INTERVAL 6 DAY),
('Sony Bravia 65\"', 'Produit de démonstration : Sony Bravia 65\".', 1299.00, 17, 'Sony', 'TV', 4.0, 'disponible', 'sony_bravia.jpg', NOW() - INTERVAL 5 DAY),
('Sony WH-1000XM4', 'Produit de démonstration : Sony WH-1000XM4.', 349.00, 38, 'Sony', 'Audio', 4.2, 'disponible', 'sony_wh1000xm4.jpg', NOW() - INTERVAL 4 DAY),
('Microsoft Surface Pro 7', 'Produit de démonstration : Microsoft Surface Pro 7.', 949.00, 34, 'Microsoft', 'Ordinateurs', 4.4, 'disponible', 'surface_pro.jpg', NOW() - INTERVAL 3 DAY),
('Nintendo Switch Lite', 'Produit de démonstration : Nintendo Switch Lite.', 219.99, 10, 'Nintendo', 'Jeux vidéo', 4.8, 'disponible', 'switch_lite.jpg', NOW() - INTERVAL 2 DAY),
('Tile Pro', 'Produit de démonstration : Tile Pro.', 34.99, 24, 'Tile', 'Accessoires', 4.5, 'disponible', 'tile_pro.jpg', NOW() - INTERVAL 1 DAY);

-- Comptes administrateurs : les créer avec  php database/create_admin.php <login> <gerant|assistant>
