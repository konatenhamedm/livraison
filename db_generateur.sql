-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping data for table db_generateur.annee: ~0 rows (approximately)

-- Dumping data for table db_generateur.client: ~0 rows (approximately)

-- Dumping data for table db_generateur.config_app: ~1 rows (approximately)
REPLACE INTO `config_app` (`id`, `logo_id`, `favicon_id`, `image_login_id`, `logo_login_id`, `main_color_admin`, `default_color_admin`, `main_color_login`, `default_color_login`, `nom_entreprise`) VALUES
	(2, 2, 3, 4, 5, '#F60', '#32CD32', '#F60', '#32CD32', '');

-- Dumping data for table db_generateur.employe: ~1 rows (approximately)
REPLACE INTO `employe` (`id`, `fonction_id`, `civilite_id`, `service_id`, `nom`, `prenom`, `contact`, `adresse_mail`, `matricule`, `entreprise_id`) VALUES
	(3, 4, 4, NULL, 'Admin', 'Admin', '00000000', 'admin@knh.com', '00000000', 2);

-- Dumping data for table db_generateur.entreprise: ~0 rows (approximately)
REPLACE INTO `entreprise` (`id`, `denomination`, `code`) VALUES
	(2, 'Default', 'ENT1');

-- Dumping data for table db_generateur.groupe_module: ~18 rows (approximately)
REPLACE INTO `groupe_module` (`id`, `icon_id`, `titre`, `ordre`, `lien`) VALUES
	(3, 4, 'Groupe & permissions', 2, 'app_utilisateur_groupe_index'),
	(28, 6, 'Paramètres', 1, 'app_config_parametre_index'),
	(29, 6, 'Modules', 1, 'app_utilisateur_module_index'),
	(30, 6, 'Utilisateurs admin', 1, 'app_utilisateur_utilisateur_index'),
	(31, 6, 'Prestataires', 1, 'app_utilisateur_front_prestataire_index'),
	(32, 6, 'Utilisateurs simples', 1, 'app_utilisateur_front_utilisateur_simple_index'),
	(51, 6, 'Groupe module', 1, 'app_utilisateur_groupe_module_index'),
	(52, 6, 'Gestion des permissions', 1, 'app_utilisateur_module_groupe_permition_index'),
	(53, 6, 'Liste permissions', 1, 'app_utilisateur_permition_index'),
	(54, 6, 'Liste fonctions', 1, 'app_parametre_fonction_index'),
	(55, 6, 'Liste directions', 1, 'app_parametre_service_index'),
	(56, 6, 'Liste employes', 1, 'app_utilisateur_employe_index'),
	(57, 6, 'Liste civilites', 1, 'app_parametre_civilite_index'),
	(58, 6, 'Liste icons', 1, 'app_parametre_icon_index'),
	(59, 6, 'Configurations', 1, 'app_parametre_config_app_index'),
	(60, 6, 'Utilisateurs admin', 1, 'app_utilisateur_utilisateur_index'),
	(61, 6, 'Prestataires', 1, 'app_utilisateur_front_prestataire_index'),
	(62, 6, 'Utilisateurs simples', 1, 'app_utilisateur_front_utilisateur_simple_index');

-- Dumping data for table db_generateur.icon: ~16 rows (approximately)
REPLACE INTO `icon` (`id`, `code`, `image`, `libelle`) VALUES
	(4, 'bi bi-arrow-up-right-circle', NULL, 'Icon fleche croissante'),
	(5, 'bi bi-arrow-up-right-circle', NULL, 'Icon fleche croissante'),
	(6, 'bi bi-arrow-up-right-circle', NULL, 'Icon fleche croissante'),
	(7, 'bi bi-calendar-day-fill', NULL, 'calendar-day-fill'),
	(8, 'bi bi-camera-reels-fill', NULL, 'camera-reels-fill'),
	(9, 'bi bi-handbag-fill', NULL, 'handbag-fill'),
	(10, 'bi bi-inboxes-fill', NULL, 'inboxes-fill'),
	(11, 'bi bi-person-badge-fill', NULL, 'person-badge-fill'),
	(12, 'bi bi-send-plus-fill', NULL, 'send-plus-fill'),
	(13, 'bi bi-ui-checks', NULL, 'ui-checks'),
	(14, 'bi bi-shield-lock-fill', NULL, 'shield-lock-fill'),
	(15, 'bi bi-person-lines-fill', NULL, 'person-lines-fill'),
	(16, 'bi bi-graph-up-arrow', NULL, 'graph-up-arrow'),
	(17, 'bi bi-menu-button-fill', NULL, 'menu-button-fill'),
	(18, 'bi bi-gift', NULL, 'Icon fleche croissante'),
	(19, 'bi bi-basket3-fill', NULL, 'basket3-fill');

-- Dumping data for table db_generateur.ilot: ~0 rows (approximately)

-- Dumping data for table db_generateur.lot: ~0 rows (approximately)

-- Dumping data for table db_generateur.messenger_messages: ~4 rows (approximately)
REPLACE INTO `messenger_messages` (`id`, `body`, `headers`, `queue_name`, `created_at`, `available_at`, `delivered_at`) VALUES
	(1, 'O:36:\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\":2:{s:44:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\";a:1:{s:46:\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\";a:1:{i:0;O:46:\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\":1:{s:55:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\";s:21:\\"messenger.bus.default\\";}}}s:45:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\";O:51:\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\":2:{s:60:\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\";O:28:\\"Symfony\\\\Component\\\\Mime\\\\Email\\":6:{i:0;s:6:\\"803131\\";i:1;s:5:\\"utf-8\\";i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\":2:{s:46:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\";a:3:{s:2:\\"to\\";a:1:{i:0;O:47:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:2:\\"To\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:58:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\";a:1:{i:0;O:30:\\"Symfony\\\\Component\\\\Mime\\\\Address\\":2:{s:39:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\";s:13:\\"admin@knh.com\\";s:36:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\";s:0:\\"\\";}}}}s:7:\\"subject\\";a:1:{i:0;O:48:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:7:\\"Subject\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:55:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\";s:19:\\"Authentication Code\\";}}s:4:\\"from\\";a:1:{i:0;O:47:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:4:\\"From\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:58:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\";a:1:{i:0;O:30:\\"Symfony\\\\Component\\\\Mime\\\\Address\\":2:{s:39:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\";s:21:\\"konatefvaly@gmail.com\\";s:36:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\";s:12:\\"konate hamed\\";}}}}}s:49:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\";i:76;}i:1;N;}}s:61:\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\";N;}}', '[]', 'default', '2023-09-05 09:17:02', '2023-09-05 09:17:02', NULL),
	(2, 'O:36:\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\":2:{s:44:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\";a:1:{s:46:\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\";a:1:{i:0;O:46:\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\":1:{s:55:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\";s:21:\\"messenger.bus.default\\";}}}s:45:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\";O:51:\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\":2:{s:60:\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\";O:28:\\"Symfony\\\\Component\\\\Mime\\\\Email\\":6:{i:0;s:6:\\"654812\\";i:1;s:5:\\"utf-8\\";i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\":2:{s:46:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\";a:3:{s:2:\\"to\\";a:1:{i:0;O:47:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:2:\\"To\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:58:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\";a:1:{i:0;O:30:\\"Symfony\\\\Component\\\\Mime\\\\Address\\":2:{s:39:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\";s:13:\\"admin@knh.com\\";s:36:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\";s:0:\\"\\";}}}}s:7:\\"subject\\";a:1:{i:0;O:48:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:7:\\"Subject\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:55:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\";s:19:\\"Authentication Code\\";}}s:4:\\"from\\";a:1:{i:0;O:47:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:4:\\"From\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:58:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\";a:1:{i:0;O:30:\\"Symfony\\\\Component\\\\Mime\\\\Address\\":2:{s:39:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\";s:21:\\"konatefvaly@gmail.com\\";s:36:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\";s:12:\\"konate hamed\\";}}}}}s:49:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\";i:76;}i:1;N;}}s:61:\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\";N;}}', '[]', 'default', '2023-09-05 09:20:54', '2023-09-05 09:20:54', NULL),
	(3, 'O:36:\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\":2:{s:44:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\";a:1:{s:46:\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\";a:1:{i:0;O:46:\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\":1:{s:55:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\";s:21:\\"messenger.bus.default\\";}}}s:45:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\";O:51:\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\":2:{s:60:\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\";O:28:\\"Symfony\\\\Component\\\\Mime\\\\Email\\":6:{i:0;s:6:\\"950390\\";i:1;s:5:\\"utf-8\\";i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\":2:{s:46:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\";a:3:{s:2:\\"to\\";a:1:{i:0;O:47:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:2:\\"To\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:58:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\";a:1:{i:0;O:30:\\"Symfony\\\\Component\\\\Mime\\\\Address\\":2:{s:39:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\";s:13:\\"admin@knh.com\\";s:36:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\";s:0:\\"\\";}}}}s:7:\\"subject\\";a:1:{i:0;O:48:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:7:\\"Subject\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:55:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\";s:19:\\"Authentication Code\\";}}s:4:\\"from\\";a:1:{i:0;O:47:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:4:\\"From\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:58:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\";a:1:{i:0;O:30:\\"Symfony\\\\Component\\\\Mime\\\\Address\\":2:{s:39:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\";s:21:\\"konatefvaly@gmail.com\\";s:36:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\";s:12:\\"konate hamed\\";}}}}}s:49:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\";i:76;}i:1;N;}}s:61:\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\";N;}}', '[]', 'default', '2023-09-05 09:46:16', '2023-09-05 09:46:16', NULL),
	(4, 'O:36:\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\":2:{s:44:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\";a:1:{s:46:\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\";a:1:{i:0;O:46:\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\":1:{s:55:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\";s:21:\\"messenger.bus.default\\";}}}s:45:\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\";O:51:\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\":2:{s:60:\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\";O:28:\\"Symfony\\\\Component\\\\Mime\\\\Email\\":6:{i:0;s:6:\\"481222\\";i:1;s:5:\\"utf-8\\";i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\":2:{s:46:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\";a:3:{s:2:\\"to\\";a:1:{i:0;O:47:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:2:\\"To\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:58:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\";a:1:{i:0;O:30:\\"Symfony\\\\Component\\\\Mime\\\\Address\\":2:{s:39:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\";s:13:\\"admin@knh.com\\";s:36:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\";s:0:\\"\\";}}}}s:7:\\"subject\\";a:1:{i:0;O:48:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:7:\\"Subject\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:55:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\";s:19:\\"Authentication Code\\";}}s:4:\\"from\\";a:1:{i:0;O:47:\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\":5:{s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\";s:4:\\"From\\";s:56:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\";i:76;s:50:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\";N;s:53:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\";s:5:\\"utf-8\\";s:58:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\";a:1:{i:0;O:30:\\"Symfony\\\\Component\\\\Mime\\\\Address\\":2:{s:39:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\";s:21:\\"konatefvaly@gmail.com\\";s:36:\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\";s:12:\\"konate hamed\\";}}}}}s:49:\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\";i:76;}i:1;N;}}s:61:\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\";N;}}', '[]', 'default', '2023-09-08 11:59:11', '2023-09-08 11:59:11', NULL);

-- Dumping data for table db_generateur.module: ~2 rows (approximately)
REPLACE INTO `module` (`id`, `titre`, `ordre`) VALUES
	(4, 'Configuration', 1),
	(5, 'Gestion utilisateurs', 2);

-- Dumping data for table db_generateur.module_groupe_permition: ~13 rows (approximately)
REPLACE INTO `module_groupe_permition` (`id`, `permition_id`, `module_id`, `groupe_module_id`, `groupe_user_id`, `ordre`, `ordre_groupe`, `menu_principal`) VALUES
	(3, 4, 4, 3, 4, 1, 2, 1),
	(4, 4, 4, 28, 4, 1, 3, 1),
	(5, 4, 4, 51, 4, 1, 3, 0),
	(6, 4, 4, 52, 4, 1, 3, 0),
	(7, 4, 4, 53, 4, 1, 3, 0),
	(8, 4, 4, 54, 4, 1, 3, 0),
	(9, 4, 4, 55, 4, 1, 3, 0),
	(10, 4, 4, 56, 4, 1, 3, 0),
	(11, 4, 4, 57, 4, 1, 3, 0),
	(12, 4, 4, 58, 4, 1, 3, 0),
	(13, 4, 4, 59, 4, 1, 3, 0),
	(14, 4, 5, 60, 4, 1, 3, 1),
	(15, 4, 5, 62, 4, 1, 3, 1);

-- Dumping data for table db_generateur.param_civilite: ~0 rows (approximately)
REPLACE INTO `param_civilite` (`id`, `libelle`, `code`) VALUES
	(4, 'Monsieur', 'M.');

-- Dumping data for table db_generateur.param_fichier: ~17 rows (approximately)
REPLACE INTO `param_fichier` (`id`, `size`, `path`, `alt`, `date_creation`, `url`) VALUES
	(2, 8190, 'media_entreprise', 'logo_tt.png', '2023-07-03 12:22:56', 'png'),
	(3, 8190, 'media_entreprise', 'logo_tt.png', '2023-07-03 12:22:56', 'png'),
	(4, 577696, 'media_entreprise', 'bg_login.png', '2023-07-03 12:22:56', 'png'),
	(5, 8190, 'media_entreprise', 'logo_tt.png', '2023-07-03 12:22:56', 'png'),
	(6, 278554, 'demande', 'mpdf_2.pdf', '2023-07-04 18:30:42', 'pdf'),
	(7, 1587243, 'demande', 'nouveau_gouvernement.pdf', '2023-07-10 16:20:47', 'pdf'),
	(8, 3565992, 'demande', 'master_pdf_merged.pdf', '2023-07-11 08:20:06', 'pdf'),
	(9, 108103, 'media_entreprise', 'soro.pdf', '2023-07-25 10:37:58', 'pdf'),
	(10, 108103, 'media_entreprise', 'soro.pdf', '2023-07-25 12:08:08', 'pdf'),
	(11, 108103, 'media_entreprise', 'soro.pdf', '2023-07-25 12:36:39', 'pdf'),
	(12, 108103, 'media_entreprise', 'soro.pdf', '2023-07-25 12:49:48', 'pdf'),
	(13, 108103, 'media_entreprise', 'soro.pdf', '2023-07-25 12:55:49', 'pdf'),
	(14, 808812, 'media_entreprise', 'location_de_vehicule_vtc_1.png', '2023-09-08 16:47:54', 'png'),
	(15, 808812, 'media_entreprise', 'location_de_vehicule_vtc_1.png', '2023-09-08 16:47:54', 'png'),
	(16, 808812, 'media_entreprise', 'location_de_vehicule_vtc_1.png', '2023-09-08 16:47:54', 'png'),
	(17, 808812, 'media_entreprise', 'location_de_vehicule_vtc_1.png', '2023-09-08 16:47:54', 'png'),
	(18, 808812, 'media_entreprise', 'location_de_vehicule_vtc_1.png', '2023-09-27 13:19:01', 'png');

-- Dumping data for table db_generateur.param_fonction: ~0 rows (approximately)
REPLACE INTO `param_fonction` (`id`, `libelle`, `code`) VALUES
	(4, 'Administrateur', 'ADM');

-- Dumping data for table db_generateur.param_service: ~0 rows (approximately)

-- Dumping data for table db_generateur.permition: ~0 rows (approximately)
REPLACE INTO `permition` (`id`, `code`, `libelle`) VALUES
	(4, 'CRUD', 'Tous les droits');

-- Dumping data for table db_generateur.prix: ~0 rows (approximately)

-- Dumping data for table db_generateur.reset_password_request: ~0 rows (approximately)

-- Dumping data for table db_generateur.statut: ~0 rows (approximately)

-- Dumping data for table db_generateur.test: ~0 rows (approximately)

-- Dumping data for table db_generateur.user_front_prestataire: ~0 rows (approximately)

-- Dumping data for table db_generateur.user_front_utilisateur: ~1 rows (approximately)
REPLACE INTO `user_front_utilisateur` (`id`, `username`, `roles`, `password`, `email`, `discr`, `reference`, `lattitude`, `longitude`, `date_creation`, `date_desactivation`) VALUES
	(1, 'konate', '[]', '$2y$13$CMFsOTEBgY7BKn88RkooGOT0soQ0GIQCgUyB1fAOlz.MwvqrtxJQi', 'konatenhamed@gmail.com', 'utilisateursimple', '23US09001', '555', '999', '2023-09-27 13:18:57', NULL);

-- Dumping data for table db_generateur.user_front_utilisateur_simple: ~1 rows (approximately)
REPLACE INTO `user_front_utilisateur_simple` (`id`, `nom`, `prenoms`, `contact`, `genre_id`, `photo_id`) VALUES
	(1, 'Konate', 'Hamed', '78887541', 4, 18);

-- Dumping data for table db_generateur.user_groupe: ~0 rows (approximately)
REPLACE INTO `user_groupe` (`id`, `name`, `description`, `roles`) VALUES
	(4, 'Super Administrateur', '', '["ROLE_SUPER_ADMIN", "ROLE_ADMIN"]');

-- Dumping data for table db_generateur.user_utilisateur: ~0 rows (approximately)
REPLACE INTO `user_utilisateur` (`id`, `employe_id`, `groupe_id`, `username`, `roles`, `password`) VALUES
	(3, 3, 4, 'admin', '[]', '$2y$13$O0r6w02ahiHO6KNfTJakruRxaXz2HlvgfkPjznKhJh/890PSg/6WS');

-- Dumping data for table db_generateur.vente: ~0 rows (approximately)

-- Dumping data for table db_generateur.versement: ~0 rows (approximately)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
