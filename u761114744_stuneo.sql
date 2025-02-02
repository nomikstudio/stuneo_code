-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Erstellungszeit: 02. Feb 2025 um 08:44
-- Server-Version: 10.11.10-MariaDB
-- PHP-Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `u761114744_stuneo`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remember_token` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `email`, `password`, `created_at`, `remember_token`, `token_expiry`) VALUES
(1, 'Dominik', 'hintringerdominik@gmail.com', '$2y$10$EHyJ0j0uvCfQZvfWzg8LVeWVzq33oAjTV4z8P3UI1AYyRqB23ULhm', '2024-10-25 11:07:26', NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `changelog_owners`
--

CREATE TABLE `changelog_owners` (
  `id` int(11) NOT NULL,
  `version` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `changelog_owners`
--

INSERT INTO `changelog_owners` (`id`, `version`, `description`, `created_at`) VALUES
(1, 'v1.0.0-beta4', '<p><strong>Here is what we\'ve added, fixed or improved:</strong></p><p><strong>ADDED</strong></p><ul><li>Changelog</li><li>Verify option</li><li>The stream URL is now checked by the system when you edit the station. You can also play and test it after successful verification</li><li>Light / Dark mode</li></ul><p><strong>IMPROVED</strong></p><ul><li>Adding Station</li><li>Statistics</li><li>Translation</li></ul><p><strong>FIXED</strong></p><ul><li>Some styling issues</li><li>Mobile view / menu</li><li>Notifications</li></ul>', '2024-11-18 10:36:27'),
(4, 'v2.0.0-beta1', '<p><strong>This has changed in this version:</strong></p><p>- Podcast integration<br>- Clearer design<br>- Quick access<br>- Help section<br>- Live Chat<br>- We have made the statistics more accurate and revised them<br>- We have revised the start page: Podcasts have been added, welcome message has been added, better statistics<br>- We have revised the URL\'s for edit podcast and edit station: pocast/{id}, station/{id}<br>- We have updated the languages<br>- We have revised the login and become owner pages: We added “Remember this device”, we added an option to have the password displayed<br>- We have revised the search: Podcasts can also be searched, max. 3 words are displayed<br>- The RSS URL and the image URL are checked directly for the podcast<br>- You can add your Spoitfy account name in the account tab (will be used for further versions)<br>- Verify has been removed: You will now be verified directly if you have an owner account<br>- The language is now set automatically based on the browser language</p>', '2025-01-28 19:05:19');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `countries`
--

CREATE TABLE `countries` (
  `country_code` varchar(10) NOT NULL,
  `country_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `countries`
--

INSERT INTO `countries` (`country_code`, `country_name`) VALUES
('AD', 'Andorra'),
('AE', 'United Arab Emirates'),
('AF', 'Afghanistan'),
('AG', 'Antigua and Barbuda'),
('AL', 'Albania'),
('AM', 'Armenia'),
('AO', 'Angola'),
('AR', 'Argentina'),
('AS', 'American Samoa'),
('AT', 'Austria'),
('AU', 'Australia'),
('AZ', 'Azerbaijan'),
('BA', 'Bosnia and Herzegovina'),
('BB', 'Barbados'),
('BD', 'Bangladesh'),
('BE', 'Belgium'),
('BF', 'Burkina Faso'),
('BG', 'Bulgaria'),
('BH', 'Bahrain'),
('BI', 'Burundi'),
('BJ', 'Benin'),
('BN', 'Brunei Darussalam'),
('BO', 'Bolivia'),
('BR', 'Brazil'),
('BS', 'Bahamas'),
('BT', 'Bhutan'),
('BW', 'Botswana'),
('BY', 'Belarus'),
('BZ', 'Belize'),
('CA', 'Canada'),
('CD', 'Congo, the Democratic Republic of the'),
('CF', 'Central African Republic'),
('CG', 'Congo'),
('CH', 'Switzerland'),
('CI', 'Côte d\'Ivoire'),
('CL', 'Chile'),
('CM', 'Cameroon'),
('CN', 'China'),
('CO', 'Colombia'),
('CR', 'Costa Rica'),
('CU', 'Cuba'),
('CV', 'Cape Verde'),
('CY', 'Cyprus'),
('CZ', 'Czech Republic'),
('DE', 'Germany'),
('DJ', 'Djibouti'),
('DK', 'Denmark'),
('DM', 'Dominica'),
('DO', 'Dominican Republic'),
('DZ', 'Algeria'),
('EC', 'Ecuador'),
('EE', 'Estonia'),
('EG', 'Egypt'),
('ER', 'Eritrea'),
('ES', 'Spain'),
('ET', 'Ethiopia'),
('FI', 'Finland'),
('FJ', 'Fiji'),
('FM', 'Micronesia'),
('FR', 'France'),
('GA', 'Gabon'),
('GB', 'United Kingdom'),
('GD', 'Grenada'),
('GE', 'Georgia'),
('GH', 'Ghana'),
('GM', 'Gambia'),
('GN', 'Guinea'),
('GQ', 'Equatorial Guinea'),
('GR', 'Greece'),
('GT', 'Guatemala'),
('GW', 'Guinea-Bissau'),
('GY', 'Guyana'),
('HN', 'Honduras'),
('HR', 'Croatia'),
('HT', 'Haiti'),
('HU', 'Hungary'),
('ID', 'Indonesia'),
('IE', 'Ireland'),
('IL', 'Israel'),
('IN', 'India'),
('IQ', 'Iraq'),
('IR', 'Iran'),
('IS', 'Iceland'),
('IT', 'Italy'),
('JM', 'Jamaica'),
('JO', 'Jordan'),
('JP', 'Japan'),
('KE', 'Kenya'),
('KG', 'Kyrgyzstan'),
('KH', 'Cambodia'),
('KI', 'Kiribati'),
('KM', 'Comoros'),
('KN', 'Saint Kitts and Nevis'),
('KP', 'Korea, Democratic People\'s Republic of'),
('KR', 'Korea, Republic of'),
('KW', 'Kuwait'),
('KZ', 'Kazakhstan'),
('LA', 'Lao People\'s Democratic Republic'),
('LB', 'Lebanon'),
('LC', 'Saint Lucia'),
('LI', 'Liechtenstein'),
('LK', 'Sri Lanka'),
('LR', 'Liberia'),
('LS', 'Lesotho'),
('LT', 'Lithuania'),
('LU', 'Luxembourg'),
('LV', 'Latvia'),
('LY', 'Libya'),
('MA', 'Morocco'),
('MC', 'Monaco'),
('MD', 'Moldova'),
('ME', 'Montenegro'),
('MG', 'Madagascar'),
('MH', 'Marshall Islands'),
('ML', 'Mali'),
('MM', 'Myanmar'),
('MN', 'Mongolia'),
('MR', 'Mauritania'),
('MT', 'Malta'),
('MU', 'Mauritius'),
('MV', 'Maldives'),
('MW', 'Malawi'),
('MX', 'Mexico'),
('MY', 'Malaysia'),
('MZ', 'Mozambique'),
('NA', 'Namibia'),
('NE', 'Niger'),
('NG', 'Nigeria'),
('NI', 'Nicaragua'),
('NL', 'Netherlands'),
('NO', 'Norway'),
('NP', 'Nepal'),
('NR', 'Nauru'),
('NZ', 'New Zealand'),
('OM', 'Oman'),
('PA', 'Panama'),
('PE', 'Peru'),
('PG', 'Papua New Guinea'),
('PH', 'Philippines'),
('PK', 'Pakistan'),
('PL', 'Poland'),
('PT', 'Portugal'),
('PW', 'Palau'),
('PY', 'Paraguay'),
('QA', 'Qatar'),
('RO', 'Romania'),
('RS', 'Serbia'),
('RU', 'Russian Federation'),
('RW', 'Rwanda'),
('SA', 'Saudi Arabia'),
('SB', 'Solomon Islands'),
('SC', 'Seychelles'),
('SD', 'Sudan'),
('SE', 'Sweden'),
('SG', 'Singapore'),
('SI', 'Slovenia'),
('SK', 'Slovakia'),
('SL', 'Sierra Leone'),
('SM', 'San Marino'),
('SN', 'Senegal'),
('SO', 'Somalia'),
('SR', 'Suriname'),
('ST', 'Sao Tome and Principe'),
('SV', 'El Salvador'),
('SY', 'Syrian Arab Republic'),
('SZ', 'Swaziland'),
('TD', 'Chad'),
('TG', 'Togo'),
('TH', 'Thailand'),
('TJ', 'Tajikistan'),
('TL', 'Timor-Leste'),
('TM', 'Turkmenistan'),
('TN', 'Tunisia'),
('TO', 'Tonga'),
('TR', 'Turkey'),
('TT', 'Trinidad and Tobago'),
('TW', 'Taiwan'),
('TZ', 'Tanzania'),
('UA', 'Ukraine'),
('UG', 'Uganda'),
('US', 'United States'),
('UY', 'Uruguay'),
('UZ', 'Uzbekistan'),
('VC', 'Saint Vincent and the Grenadines'),
('VE', 'Venezuela'),
('VN', 'Viet Nam'),
('VU', 'Vanuatu'),
('WS', 'Samoa'),
('YE', 'Yemen'),
('ZA', 'South Africa'),
('ZM', 'Zambia'),
('ZW', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `download_links`
--

CREATE TABLE `download_links` (
  `id` int(11) NOT NULL,
  `os` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `download_links`
--

INSERT INTO `download_links` (`id`, `os`, `link`, `version`) VALUES
(1, 'Windows', 'https://github.com/nomikstudio/stuneo/releases/download/v0.1.0/stuneo-Setup-0.1.0.exe', '0.1.0'),
(2, 'Mac', 'https://github.com/nomikstudio/stuneo/releases/download/v0.1.0/stuneo-0.1.0.dmg', '0.1.0'),
(3, 'Linux', 'https://github.com/nomikstudio/stuneo/releases/download/v0.1.0/stuneo-0.1.0.AppImage', '0.1.0');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `favorites`
--

CREATE TABLE `favorites` (
  `favorite_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `station_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `favorites`
--

INSERT INTO `favorites` (`favorite_id`, `user_id`, `station_id`, `created_at`) VALUES
(208, 3, 72, '2025-01-01 20:16:19'),
(211, 9, 66, '2025-01-20 16:42:54'),
(212, 9, 3, '2025-01-20 17:14:06'),
(219, 3, 39, '2025-02-02 07:12:25'),
(220, 3, 64, '2025-02-02 07:13:04'),
(222, 3, 29, '2025-02-02 07:13:10'),
(225, 3, 3, '2025-02-02 07:16:07');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `favorites_podcast`
--

CREATE TABLE `favorites_podcast` (
  `favorite_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `podcast_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `favorites_podcast`
--

INSERT INTO `favorites_podcast` (`favorite_id`, `user_id`, `podcast_id`, `added_at`) VALUES
(1, 9, 12, '2025-01-20 16:50:27'),
(3, 3, 1, '2025-02-01 09:23:51'),
(4, 3, 2, '2025-02-01 16:44:28');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `favorites_tokens`
--

CREATE TABLE `favorites_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `share_token` varchar(32) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `favorites_tokens`
--

INSERT INTO `favorites_tokens` (`id`, `user_id`, `share_token`, `created_at`) VALUES
(2, 9, 'a3e432936c02c08955ff81b3b677b08d', '2025-01-20 17:00:05');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `languages`
--

CREATE TABLE `languages` (
  `language_code` varchar(10) NOT NULL,
  `language_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `languages`
--

INSERT INTO `languages` (`language_code`, `language_name`) VALUES
('am', 'Amharic'),
('ar', 'Arabic'),
('az', 'Azerbaijani'),
('bg', 'Bulgarian'),
('bn', 'Bengali'),
('cs', 'Czech'),
('da', 'Danish'),
('de', 'German'),
('el', 'Greek'),
('en', 'English'),
('es', 'Spanish'),
('et', 'Estonian'),
('fa', 'Persian'),
('fi', 'Finnish'),
('fr', 'French'),
('he', 'Hebrew'),
('hi', 'Hindi'),
('hr', 'Croatian'),
('hu', 'Hungarian'),
('hy', 'Armenian'),
('id', 'Indonesian'),
('is', 'Icelandic'),
('it', 'Italian'),
('ja', 'Japanese'),
('ka', 'Georgian'),
('km', 'Khmer'),
('ko', 'Korean'),
('lo', 'Lao'),
('lt', 'Lithuanian'),
('lv', 'Latvian'),
('mk', 'Macedonian'),
('ms', 'Malay'),
('my', 'Burmese'),
('ne', 'Nepali'),
('nl', 'Dutch'),
('no', 'Norwegian'),
('pl', 'Polish'),
('pt', 'Portuguese'),
('ro', 'Romanian'),
('ru', 'Russian'),
('si', 'Sinhala'),
('sk', 'Slovak'),
('sq', 'Albanian'),
('sr', 'Serbian'),
('sv', 'Swedish'),
('sw', 'Swahili'),
('th', 'Thai'),
('tl', 'Tagalog'),
('tr', 'Turkish'),
('uk', 'Ukrainian'),
('ur', 'Urdu'),
('vi', 'Vietnamese'),
('zh', 'Chinese');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  `is_global` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `message`, `created_at`, `expires_at`, `is_global`) VALUES
(2, 'Design issues & bugs', 'Due to the new update, new design errors and bugs have occurred, but these have no influence on the data. We will fix the problems shortly.', '2025-01-28 19:09:59', '2025-02-02 00:00:00', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `notification_read_status`
--

CREATE TABLE `notification_read_status` (
  `id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `notification_read_status`
--

INSERT INTO `notification_read_status` (`id`, `notification_id`, `owner_id`, `is_read`) VALUES
(12, 2, 1, 0),
(13, 2, 10, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `owner_podcasts`
--

CREATE TABLE `owner_podcasts` (
  `owner_id` int(11) NOT NULL,
  `podcast_id` int(11) NOT NULL,
  `assigned_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `owner_podcasts`
--

INSERT INTO `owner_podcasts` (`owner_id`, `podcast_id`, `assigned_at`) VALUES
(1, 1, '2025-01-09 21:13:11'),
(1, 12, '2025-01-15 21:29:41'),
(1, 14, '2025-01-15 21:13:07');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `owner_station`
--

CREATE TABLE `owner_station` (
  `owner_id` int(11) NOT NULL,
  `station_id` int(11) NOT NULL,
  `assigned_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `owner_station`
--

INSERT INTO `owner_station` (`owner_id`, `station_id`, `assigned_at`) VALUES
(1, 3, '2024-11-05 20:59:32'),
(1, 4, '2024-11-05 21:02:16');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `plans`
--

CREATE TABLE `plans` (
  `plan_id` int(11) NOT NULL,
  `plan_name` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `plans`
--

INSERT INTO `plans` (`plan_id`, `plan_name`, `price`, `description`) VALUES
(1, 'Free', 0.00, 'Kostenloser Plan ohne zusätzliche Features'),
(5, 'stuneo+', 2.99, 'Get Podcasts with stuneo+');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `podcasts`
--

CREATE TABLE `podcasts` (
  `podcast_id` int(11) NOT NULL,
  `rss_url` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `is_adult` tinyint(4) NOT NULL,
  `status` enum('approved','pending') NOT NULL DEFAULT 'pending',
  `category_id` int(11) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `podcasts`
--

INSERT INTO `podcasts` (`podcast_id`, `rss_url`, `title`, `description`, `image`, `is_adult`, `status`, `category_id`, `added_at`) VALUES
(1, 'https://truecrime.at/feed/mp3/', 'Spur der Verbrechen', 'Ober&ouml;sterreichs spektakul&auml;rste Kriminalf&auml;lle', 'https://truecrime.at/wp-content/uploads/sites/2/2022/09/Verbrechen_09-2022_ohne-powered-by-1024x1024.jpg.webp', 0, 'approved', 9, '2025-01-02 19:46:53'),
(2, 'https://feeds.megaphone.fm/GLT9787442816', 'Kaulitz Hills - Senf aus Hollywood', 'Bill und Tom Kaulitz sprechen in ihrem Podcast “Kaulitz Hills - Senf aus Hollywood” über tagesaktuelle Themen und geben zu relevanten oder auch mal irrelevanten Geschichten aus ihrem Leben ihren Senf ab. Dafür treffen sich die beiden Brüder wöchentlich in ihrem eigenen Musikstudio in den Hollywood Hills und wir erleben sie so ehrlich wie nie. Mit einem guten Drink in der Hand diskutieren sie über ihren eigenen wöchentlichen Pressespiegel, zerlegen den Quark, der über sie geschrieben wird und berichten von den neuesten und privaten Ereignissen aus dem Hause Kaulitz. Immer mittwochs, Cheeeeeeers!', 'https://i.scdn.co/image/ab6765630000ba8aec865db18225c0c4a3a5d52e', 0, 'approved', 5, '2025-01-06 14:24:23'),
(3, 'https://plus-ultra.podigee.io/feed/mp3', 'Plus Ultra - Der Weg in den Dreißigjährigen Krieg', 'Ende des 16. Jahrhunderts stellt das Haus Habsburg seit Generationen den Kaiser des Heiligen Römischen Reichs. Doch der amtierende Herrscher Rudolf II. hat keinen legitimen Thronfolger vorzuweisen. Der junge Erzherzog Ferdinand von Innerösterreich macht sich insgeheim Hoffnungen eines Tages zu seinem Nachfolger gekrönt zu werden. Er träumt von der Rekatholisierung des gesamten Reichs. Denn der sich rasch ausbreitende Protestantismus ist dem tiefgläubigen Katholiken ein Dorn im Auge. Und eine Gefahr für die habsburgische Dynastie.', 'https://images.podigee-cdn.net/1400x,stVNHy88kCySfVOvPJ00S8DC8LGzHgaSNO5WXqLPBQ2E=/https://main.podigee-cdn.net/uploads/u54371/af854c68-dcf8-4b02-b452-f8787331eeae.jpg', 0, 'approved', NULL, '2025-01-06 14:31:15'),
(4, 'https://inside-austria.podigee.io/feed/mp3', 'Inside Austria', 'DER STANDARD und DER SPIEGEL rekonstruieren die großen und kleinen Skandale Österreichs. WIR blicken in politische Abgründe und erklären zusammen mit den Journalistinnen und Journalisten beider Redaktionen, was die Republik bewegt. \"Inside Austria\" erscheint Samstags – überall, wo es Podcasts gibt.', 'https://images.podigee-cdn.net/400x,s4twDE7GKOIXpacXa0gh9LuMhMJO_gyaVO8AyrEa94fc=/https://main.podigee-cdn.net/uploads/u16032/87e5e713-45ef-47cb-b1fb-7fa6a47f2eb1.jpg', 0, 'approved', NULL, '2025-01-06 14:33:50'),
(5, 'https://feeds.megaphone.fm/LUCIAZITZ3608076107', 'Mord am Mittwoch', 'Hey Zusammen :)\r\nIch heiße Lucia und interessiere mich seit Jahren für Verbrechen und True Crimes! In diesem Podcast stelle ich Euch einige der spannendsten Verbrechen Deutschlands und der ganzen Welt vor, die so niemand auf dem Schirm hatte. Ich bin keine Journalistin oder Psychologin, aber ich habe super viel Spaß an der Recherche und bei den Erzählungen der Fälle. Ich wünsche Euch viel Spaß beim Zuhören! Ich freue mich immer über euer Feedback und Eure Anregungen. Falls ihr selber interessante Geschichten kennt, schreibt mir und eventuell nehme ich dazu eine Folge auf. Am besten erreicht Ihr mich unter dem Stichwort \'\'Mord am Mittwoch\'\' hier: Instagram: https://instagram.com/lucialeona_?igshid=bz5ecf5s9683.\r\nIch freu mich auf Euch, Eure Lucia :)', 'https://i.scdn.co/image/ab6765630000ba8a5c93d8759f74b5fc51f8a512', 0, 'approved', NULL, '2025-01-06 14:38:02'),
(6, 'https://feeds.simplecast.com/dnJhzmyN', 'Verbrechen', 'Warum lässt eine Frau ihren Mann erschießen? Wie kommt ein Kommissar an ein Geständnis? Und warum lügen Zeugen manchmal? Was, wenn Polizisten kriminell handeln oder Sachverständige versuchen, ihre Irrtümer zu kaschieren? Und was, wenn Unschuldige in die Mühlen der Strafjustiz geraten – und niemand ihnen glaubt …? Sabine Rückert aus der ZEIT-Chefredaktion ist Expertin für Verbrechen und deren Bekämpfung. Sie saß in großen Strafprozessen, schrieb preisgekrönte Gerichtsreportagen und ging unvorstellbaren Kriminalfällen nach. Durch ihre Berichterstattung deckte sie außerdem zwei Justizirrtümer auf. Sie beschäftigt sich mit Rechtsmedizin und Kriminalpsychiatrie ebenso wie mit Glaubwürdigkeitsbegutachtung und Profiling. Rückert kennt die Welt der Verbrechensbekämpfung von der Polizeiwache bis zum Bundesgerichtshof. Mit Andreas Sentker, dem Leiter des Wissensressorts der ZEIT, spricht Sabine Rückert über die Fälle ihres Lebens. Noch mehr Kriminalfälle sowie das Wichtigste aus Politik, Wirtschaft und Kultur finden Sie in der ZEIT und auf ZEIT ONLINE. Jetzt 4 Wochen kostenlos testen unter www.zeit.de/verbrechenpodcast', 'https://media.plus.rtl.de/podcast/verbrechen-ie6umhl1hrkmx.jpeg', 0, 'approved', NULL, '2025-01-06 14:39:31'),
(7, 'https://proxyfeed.svmaudio.com/aa/dick-und-doof', 'Dick & Doof', 'Sexy, charmant und unfassbar intelligent - all das sind Sandra und Luca nicht. Sie sind lediglich Dick und Doof...', 'https://media.plus.rtl.de/podcast/dick-doof-ihx6pjduk9o47.jpeg', 0, 'approved', NULL, '2025-01-06 14:41:01'),
(8, 'https://origin.feeds.br.de/im-namen-der-hose-der-sexpodcast-von-puls/feed.xml', 'Im Namen der Hose', 'Sex ist aufregend, chaotisch, manchmal peinlich – aber nichts ist zu verrückt, um drüber zu sprechen! Auri Sattelmair und Sebastian Heigl reden über alles, was euch beim Thema Sex bewegt: Egal, ob ihr Fragen habt, die ihr euch sonst nie zu stellen traut, oder einfach über die peinlichsten Dating-Fails lachen wollt. Gemeinsam mit euch, Expert:innen und spannenden Gäst:innen wird Klartext geredet. Und klar: Hier lassen nicht nur die Hosts die Hosen runter! Im Sexpodcast \"Im Namen der Hose\" gibt\'s keine Scham und keine doofen Fragen. Auri und Sebastian erzählen von ihren eigenen Erfahrungen, holen sich Expert:innen ins Boot und diskutieren all die Dinge, die euch interessieren: Was macht guten Sex aus? Haben andere auch solche Gedanken? Und: Ist das normal? Immer locker, immer ehrlich – und am Ende sind wir alle ein bisschen schlauer und vielleicht auch ein bisschen entspannter, wenn es ums Thema Sex geht. Jeden Samstag in der ARD Audiothek und überall, wo es Podcasts gibt.', 'https://img.br.de/54d289ba-a080-466b-bdd6-759ef118810b.jpeg', 1, 'approved', NULL, '2025-01-06 14:45:05'),
(12, 'https://www.omnycontent.com/d/playlist/e73c998e-6e60-432f-8610-ae210140c5b1/55545f64-58aa-4f15-b7c2-b2440137c8fd/2ed30d45-4fdc-4da9-a566-b2440137ca15/podcast.rss', 'Monster: BTK', '&#039;Monster: BTK&#039;, the newest installment in the &#039;Monster&#039; franchise, reveals the true story of the Wichita, Kansas serial killer who murdered at least 10 people between 1974 and 1991. Known by the moniker, BTK &ndash; Bind Torture Kill, his notoriety was bolstered by the taunting letters he sent to police, and the chilling phone calls he made to media outlets. BTK&#039;s identity was finally revealed in 2005 to the shock of his family, his community, and the world. He was the serial killer next door.\r\n\r\nFrom Tenderfoot TV &amp; iHeartPodcasts, this is &#039;Monster: BTK&#039;.', 'https://cdn-images-3.listennotes.com/podcasts/monster-btk-Mqu1iyIBrV2-fu1TMvb9IOW.1400x1400.jpg', 0, 'approved', 9, '2025-01-15 20:03:26'),
(14, 'https://feeds.megaphone.fm/severance-ben-adam', 'The Severance Podcast with Ben Stiller &amp; Adam Scott', 'Hi, we&#039;re Ben Stiller and Adam Scott. We have severed ourselves from the world for 5 years, making the workplace thriller Severance. While we have no memory of what happened during that time, we thought we should make a companion podcast for all the Innies who will have no recollection of watching, in an attempt to reintegrate them with their memory of the show.\r\nThis is the Severance Podcast with Ben Stiller and Adam Scott, an episode-by-episode, behind-the-scenes breakdown with the creators, cast, crew, and fans of the Emmy- and Peabody Award-winning TV show.', 'https://cdn-images-3.listennotes.com/podcasts/the-severance-podcast-with-ben-stiller-adam-s7ceO0Pkx9Y-GBzWeE6pnPM.1400x1400.jpg', 0, 'approved', 4, '2025-01-15 20:13:07');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `podcast_categories`
--

CREATE TABLE `podcast_categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `podcast_categories`
--

INSERT INTO `podcast_categories` (`category_id`, `name`, `description`, `created_at`) VALUES
(1, 'Technology', 'Podcasts about technology, gadgets, and innovation', '2025-01-08 20:34:14'),
(2, 'Health & Wellness', 'Podcasts about health, fitness, and mental well-being', '2025-01-08 20:34:14'),
(3, 'Education', 'Educational podcasts covering various topics', '2025-01-08 20:34:14'),
(4, 'Entertainment', 'Podcasts about movies, TV shows, and pop culture', '2025-01-08 20:34:14'),
(5, 'Business', 'Podcasts about entrepreneurship, business, and finance', '2025-01-08 20:34:14'),
(6, 'Sports', 'Podcasts about sports, athletes, and fitness', '2025-01-08 20:34:14'),
(7, 'News & Politics', 'Podcasts about current events, news, and politics', '2025-01-08 20:34:14'),
(8, 'Comedy', 'Podcasts that aim to make you laugh', '2025-01-08 20:34:14'),
(9, 'True Crime', 'Podcasts about criminal cases, mysteries, and investigations', '2025-01-08 20:35:30');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `radio_genres`
--

CREATE TABLE `radio_genres` (
  `genre_id` int(11) NOT NULL,
  `genre_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `radio_genres`
--

INSERT INTO `radio_genres` (`genre_id`, `genre_name`) VALUES
(56, 'Afro-Cuban'),
(37, 'Afrobeat'),
(16, 'Alternative'),
(24, 'Ambient'),
(60, 'Arabic'),
(49, 'Bluegrass'),
(10, 'Blues'),
(59, 'Bollywood'),
(54, 'Bossa Nova'),
(50, 'Celtic'),
(62, 'Children\'s Music'),
(53, 'Chillout'),
(5, 'Classical'),
(63, 'Comedy'),
(6, 'Country'),
(7, 'Dance'),
(23, 'Disco'),
(28, 'Drum & Bass'),
(27, 'Dubstep'),
(51, 'Easy Listening'),
(39, 'EDM'),
(47, 'Electro Swing'),
(8, 'Electronic'),
(25, 'Folk'),
(15, 'Funk'),
(40, 'Garage'),
(22, 'Gospel'),
(46, 'Grunge'),
(41, 'Hard Rock'),
(42, 'Heavy Metal'),
(3, 'Hip-Hop'),
(19, 'House'),
(17, 'Indie'),
(48, 'Industrial'),
(33, 'Instrumental'),
(35, 'J-Pop'),
(4, 'Jazz'),
(34, 'K-Pop'),
(14, 'Latin'),
(38, 'Latin Pop'),
(52, 'Lounge'),
(61, 'Meditation'),
(58, 'Merengue'),
(11, 'Metal'),
(32, 'New Age'),
(65, 'News'),
(31, 'Opera'),
(1, 'Pop'),
(18, 'Punk'),
(12, 'R&B'),
(9, 'Reggae'),
(55, 'Reggaeton'),
(2, 'Rock'),
(57, 'Salsa'),
(68, 'Schlager'),
(26, 'Ska'),
(43, 'Soft Rock'),
(13, 'Soul'),
(30, 'Soundtrack'),
(67, 'Spoken Word'),
(66, 'Sports'),
(29, 'Swing'),
(44, 'Synthpop'),
(64, 'Talk'),
(21, 'Techno'),
(20, 'Trance'),
(45, 'Trap'),
(36, 'World');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `radio_owners`
--

CREATE TABLE `radio_owners` (
  `owner_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `slug` varchar(100) DEFAULT NULL,
  `is_verified` tinyint(4) NOT NULL,
  `status` int(11) NOT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `is_2fa_enabled` tinyint(1) DEFAULT 0,
  `login_attempts` int(11) DEFAULT 0,
  `auth_code` varchar(6) DEFAULT NULL,
  `auth_code_expiry` datetime DEFAULT NULL,
  `email_language` enum('english','deutsch') NOT NULL DEFAULT 'english',
  `spotify_account` varchar(255) DEFAULT NULL,
  `remember_me_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `radio_owners`
--

INSERT INTO `radio_owners` (`owner_id`, `name`, `logo`, `email`, `password`, `phone`, `description`, `website_url`, `facebook_url`, `twitter_url`, `instagram_url`, `linkedin_url`, `created_at`, `slug`, `is_verified`, `status`, `reset_token`, `reset_token_expiry`, `is_2fa_enabled`, `login_attempts`, `auth_code`, `auth_code_expiry`, `email_language`, `spotify_account`, `remember_me_token`) VALUES
(1, 'John Doe', 'https://www.liferadio.at/img/LifeRadio_LOGO.png_11519.png', 'john@example.com', '$2y$10$EHyJ0j0uvCfQZvfWzg8LVeWVzq33oAjTV4z8P3UI1AYyRqB23ULhm', '123456789', 'Erfahrener Radiobetreiber mit Fokus auf Indie-Musik und lokale Veranstaltungen.', 'https://johndoe-radio.com', 'https://facebook.com/johndoe', 'https://twitter.com/johndoe', 'https://instagram.com/johndoe', 'https://linkedin.com/in/johndoe', '2024-10-31 11:49:19', 'john-doe', 1, 1, '', NULL, 0, 3, '', '2025-01-22 14:57:00', 'english', 'hintringerdiminik', NULL),
(10, 'Dominik', 'https://nomik.studio', 'hintringerdominik@gmail.com', '$2y$10$0F5iBfpkYpGSPLvZ0.T/uOoNaCWg81Hu02DAISB1yE9XeafrUY14q', '06642609164', 'Test', 'https://planetdomi.at', '', '', '', '', '2025-01-21 21:05:12', 'dominik', 0, 1, '', NULL, 0, 2, '', NULL, 'deutsch', NULL, '8461948208e75006167d52f6c9dbf862');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `app_email` varchar(255) NOT NULL,
  `app_logo` text NOT NULL,
  `version` varchar(255) NOT NULL,
  `help_maintenance_mode` tinyint(1) DEFAULT 0,
  `owner_maintenance_mode` tinyint(1) DEFAULT 0,
  `open_maintenance_mode` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `site_settings`
--

INSERT INTO `site_settings` (`id`, `app_name`, `app_email`, `app_logo`, `version`, `help_maintenance_mode`, `owner_maintenance_mode`, `open_maintenance_mode`) VALUES
(1, 'stuneo', 'help@stuneo.com', '', '2.0.0', 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `stations`
--

CREATE TABLE `stations` (
  `station_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `stream_url` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `country` varchar(255) NOT NULL,
  `logo_url` varchar(500) DEFAULT NULL,
  `language` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `genre_id` int(11) DEFAULT NULL,
  `api_url` varchar(255) NOT NULL,
  `status` enum('pending','approved') DEFAULT 'pending',
  `is_featured` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `stations`
--

INSERT INTO `stations` (`station_id`, `name`, `stream_url`, `description`, `country`, `logo_url`, `language`, `created_at`, `genre_id`, `api_url`, `status`, `is_featured`) VALUES
(3, 'Life Radio', 'https://stream.liferadio.at/liferadio/mp3-192/', 'Hauptsache Hits.', 'Austria', 'https://www.liferadio.at/images/fallback/default-1x1.primary.jpg', 'German', '2024-10-26 08:16:35', 1, 'https://api.streamabc.net/metadata/channel/lfr_vis0h4lbny2_67bu.json', 'approved', 1),
(4, 'WELLE 1', 'https://live.welle1.at:17256/stream', 'music radio', 'Austria', 'https://rms-austria.at/_assets/thumbnail?id=1888&thumbnail=', 'German', '2024-10-26 08:16:35', 1, 'https://live.welle1.at:17256/status-json.xsl', 'approved', 0),
(20, 'jö.live', 'https://edge62.stream.maxfive.com/max5-joelive', 'Mein Tag. Meine Musik.', 'Austria', 'https://static.mytuner.mobi/media/tvos_radios/xkercd9bsyvk.png', 'German', '2024-11-07 20:05:43', 1, '', 'approved', 0),
(21, 'kronehit', 'https://secureonair.krone.at/kronehit-hd.mp3', 'Die meiste Musik', 'Austria', 'https://static.mytuner.mobi/media/tvos_radios/829/kronehit.178c92e7.png', 'German', '2024-11-07 20:09:30', 1, '', 'approved', 0),
(22, 'Hitradio Ö3', 'http://orf-live.ors-shoutcast.at/oe3-q1a', 'Verbindet das ganze Land', 'Austria', 'https://static.mytuner.mobi/media/tvos_radios/474/hitradio-o3.a0f84a69.png', 'German', '2024-11-07 20:13:31', 1, 'http://orf-live.ors-shoutcast.at/status-json.xsl', 'approved', 0),
(23, 'Arabella', 'https://frontend.streams.arabella.at/arabella-oberoesterreich?aggregator=tunein', '80er, 90er und ganz viel WOW.', 'Austria', 'https://play-lh.googleusercontent.com/UxmGUnZZx_4y_zgJUhR8YO8htyrnj4-MHmApVclzjKYbV0TBXHWWctqkCQi1g-nMIg', 'German', '2024-11-07 20:16:42', 1, '', 'approved', 0),
(24, 'FM4', 'https://orf-live.ors-shoutcast.at/fm4-q2a', 'You\'re at home, baby!', 'Austria', 'https://woisthierderkrach.de/wp-content/uploads/2023/03/FM4-Kopie-1.png', 'German', '2024-11-07 20:19:15', 1, '', 'approved', 0),
(25, 'Antenne Oberösterreich', 'https://onair.securestream.antenneoberoesterreich.at/antooe', 'Die besten Hits', 'Austria', 'https://www.radio.at/300/antenneoesterreich.png', 'German', '2024-11-07 20:23:17', 1, '', 'approved', 0),
(26, '88.6', 'https://frontend.streamonkey.net/radio886-onair/stream/mp3', 'Ihr hört 88.6 - So rockt das Leben!', 'Austria', 'https://cdn-profiles.tunein.com/s25744/images/logog.png?t=636430', 'German', '2024-11-07 20:25:42', 41, '', 'approved', 0),
(27, 'Radio Oberösterreich', 'https://orf-live.ors-shoutcast.at/ooe-q2a', 'Weil wir OÖ lieben', 'Austria', 'https://www.radio.at/300/ooe.png?version=da5a75308be0014348aefb18027096ce', 'German', '2024-11-07 20:28:26', 1, 'http://ors-sn02.ors-shoutcast.at/status-json.xsl', 'approved', 0),
(28, 'Radio Niederösterreich', 'https://orf-live.ors-shoutcast.at/noe-q2a', 'Guten Morgen NÖ', 'Austria', 'https://www.radio.at/300/orfnoe.png', 'German', '2024-11-07 20:30:29', 1, 'http://ors-sn02.ors-shoutcast.at/status-json.xsl', 'approved', 0),
(29, 'Radio Wien', 'https://orf-live.ors-shoutcast.at/wie-q2a', 'Make Love not War', 'Austria', 'https://static.mytuner.mobi/media/tvos_radios/seRuZBshjC.png', 'German', '2024-11-07 20:32:12', 1, 'http://ors-sn04.ors-shoutcast.at/status-json.xsl', 'approved', 0),
(30, 'Radio Flamingo', 'http://stream.zeno.fm/215bpympks8uv', 'We play-You dance', 'Austria', 'https://www.radio.at/300/radioflamingo.png', 'German', '2024-11-07 20:35:48', 68, '', 'approved', 0),
(31, 'Radio Austria', 'https://onair.securestream.radioaustria.at/radioaustria', 'Alle Hits aus Österreich', 'Austria', 'https://www.radio.at/300/radioaustria.png', 'German', '2024-11-07 20:38:18', 1, '', 'approved', 0),
(32, 'Energy Österreich', 'https://scdn.nrjaudio.fm/adwz1/at/36001/mp3_128.mp3', 'HIT MUSIC ONLY!', 'Austria', 'https://cache.usercontentapp.com/logo/mjpg/62885.jpg', 'German', '2024-11-07 20:39:58', 1, '', 'approved', 0),
(33, '1LIVE', 'https://wdr-1live-live.icecastssl.wdr.de/wdr/1live/live/mp3/128/stream.mp3', 'Das junge Radio des WDR.', 'Deutschland', 'https://www1.wdr.de/radio/1live/app/livestream-cover-1live-100~_v-Podcast.jpg', 'German', '2024-12-02 10:39:57', 1, 'https://www1.wdr.de/radio/1live/', 'approved', 0),
(34, 'BBC Radio 1', 'http://a.files.bbci.co.uk/media/live/manifesto/audio/simulcast/hls/nonuk/sbr_low/ak/bbc_world_service.m3u8', 'The best new music UK.', 'United Kingdom', 'https://www.radio.at/300/bbcradio1.png?version=e97a2a113a2ed878ef627182022e7ecb', 'English', '2024-12-02 10:39:57', 2, 'https://www.bbc.co.uk/sounds/play/live:bbc_radio_one', 'approved', 0),
(35, 'France Inter', 'https://icecast.radiofrance.fr/franceinter-hifi.aac', 'La station généraliste nationale française.', 'France', 'https://upload.wikimedia.org/wikipedia/commons/a/a0/France_Inter_logo_2021.svg', 'French', '2024-12-02 10:39:57', 3, 'https://www.franceinter.fr/', 'approved', 0),
(36, 'RNE Radio 3', 'https://rtvelivestream.akamaized.net/rtvesec/rne/rne_r3_main.m3u8', 'La emisora de música y cultura alternativa de España.', 'España', 'https://cdn.creazilla.com/cliparts/7826882/radio-3-rne-spain-clipart-sm.png', 'Spanish', '2024-12-02 10:39:57', 4, 'https://www.rtve.es/radio/radio3/', 'approved', 0),
(37, 'RAI Radio 2', 'https://icestreaming.rai.it/2.mp3', 'Il canale giovane della radio italiana.', 'Italia', 'https://image-cdn-ak.spotifycdn.com/image/ab67706c0000da84164a5e3737b2596001ea8c17', 'Italian', '2024-12-02 10:39:57', 5, 'https://www.raiplayradio.it/radio2/', 'approved', 0),
(38, 'Ö1', 'https://orf-live.ors-shoutcast.at/oe1-q2a', 'Das Kulturradio.', 'Österreich', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQEW0YP_L2kw_tYhKi97n9Df26zw44KV_cm6g&s', 'German', '2024-12-02 10:39:57', 6, 'https://oe1.orf.at/', 'approved', 0),
(39, 'SRF 3', 'https://stream.srg-ssr.ch/m/drs3/mp3_128', 'Das junge Schweizer Radio.', 'Schweiz', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR3Zs_DN4PKgHGwOfYWLgQ123O-pNCEZPn0xlDVKkfATmJ7S_sXLsNDa1qtIhvezPeXBrI&usqp=CAU', 'German', '2024-12-02 10:39:57', 7, 'https://www.srf.ch/radio-srf-3', 'approved', 0),
(40, 'Triple J', 'https://live-radio01.mediahubaustralia.com/2TJW/mp3/', 'Australia’s youth radio.', 'Australia', 'https://i.iheart.com/v3/re/assets.streams/66e3e5175e06f230d23dcbe2', 'English', '2024-12-02 10:39:57', 8, 'https://www.abc.net.au/triplej/', 'approved', 0),
(41, 'CBC Radio One', 'https://26733.live.streamtheworld.com/CBOFM_CBC.mp3', 'Canada’s national news and talk radio.', 'Canada', 'https://cdn-profiles.tunein.com/s1198/images/logog.png', 'English', '2024-12-02 10:39:57', 9, 'https://www.cbc.ca/radio', 'approved', 0),
(42, 'NPR', 'https://npr-ice.streamguys1.com/live.mp3', 'American news and cultural programming.', 'USA', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQS86gsw9kOxNyjJ44GP7YeXWVeTt5eypdEZA&s', 'English', '2024-12-02 10:39:57', 10, 'https://www.npr.org/', 'approved', 0),
(61, 'Chillhop Music', 'https://channels.fluxfm.de/chillhop/stream.mp3', 'Relaxing beats and chill vibes.', 'Netherlands', 'https://cms.chillhop.com/media/78126/squarelcdc41272fb9e5ba0a6fcfd39b87b8a2e776a635c.jpg', 'Dutch', '2024-12-02 11:18:25', 53, 'https://chillhop.com/', 'approved', 0),
(62, 'Jazz24', 'https://live.amperwave.net/direct/ppm-jazz24mp3-ibc1', 'Great jazz 24/7 from Seattle.', 'USA', 'https://static.mytuner.mobi/media/tvos_radios/893/jazz24.f21bbc33.jpg', 'English', '2024-12-02 11:18:25', 4, 'https://www.jazz24.org/', 'approved', 0),
(63, 'Hot 108 Jamz', 'https://live.powerhitz.com/hot108', 'The hottest hip-hop and R&B.', 'USA', 'https://play-lh.googleusercontent.com/pcae6lY1ucKZCsf9erDmCgIXaH40k4UAm8ocaVij71VoIYCa2EzylHON0qsWgYBqjA', 'English', '2024-12-02 11:18:25', 3, 'https://www.hot108.com/', 'approved', 0),
(64, 'Classic FM', 'https://media-ssl.musicradio.com/ClassicFMMP3', 'The world’s greatest classical music.', 'United Kingdom', 'https://cdn-profiles.tunein.com/s8439/images/logog.jpg', 'English', '2024-12-02 11:18:25', 5, 'https://www.classicfm.com/', 'approved', 0),
(65, 'KEXP', 'https://kexp.streamguys1.com/kexp160.aac', 'Where the music matters.', 'USA', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTqoI2O6mwx3rjfzod1tLcOUyMnmvBJw0kkqw&s', 'English', '2024-12-02 11:18:25', 1, 'https://www.kexp.org/', 'approved', 0),
(66, 'Radio Swiss Jazz', 'https://stream.srg-ssr.ch/m/rsj/mp3_128', 'Smooth jazz from Switzerland.', 'Switzerland', 'https://www.radioswissjazz.ch/social-media/rsj-web.png', 'German', '2024-12-02 11:18:25', 4, 'https://www.radioswissjazz.ch/', 'approved', 0),
(67, 'Deep House Amsterdam', 'http://knuffelrockradio.beheerstream.com:8274/stream', 'Deep house music for your soul.', 'Netherlands', 'https://i1.sndcdn.com/artworks-000087106023-4ql17u-t500x500.jpg', 'Dutch', '2024-12-02 11:18:25', 19, 'https://www.deephouseamsterdam.com/', 'approved', 0),
(68, 'The Edge 96.ONE', 'https://edge-stream.mediaworks.nz/edge_mp3_96', 'New Zealand’s hit music station.', 'New Zealand', 'https://i1.sndcdn.com/avatars-000007372468-wt67yn-t240x240.jpg', 'English', '2024-12-02 11:18:25', 25, 'https://www.theedge.co.nz/', 'approved', 0),
(69, 'FIP', 'https://direct.fipradio.fr/live/fip-midfi.mp3', 'Eclectic music from France.', 'France', 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/16/FIP_logo_2021.svg/1200px-FIP_logo_2021.svg.png', 'French', '2024-12-02 11:18:25', 8, 'https://www.fip.fr/', 'approved', 0),
(70, 'Groove Salad', 'https://somafm.com/groovesalad256.pls', 'A perfect mix of ambient beats.', 'USA', 'https://cdn-profiles.tunein.com/s2591/images/logog.jpg', 'English', '2024-12-02 11:18:25', 7, 'https://somafm.com/groovesalad/', 'approved', 0),
(71, 'Radio Paradise', 'https://stream.radioparadise.com/aac-320', 'Eclectic commercial-free music.', 'USA', 'https://www.radio.at/300/radioparadise.png', 'English', '2024-12-02 11:18:25', 8, 'https://radioparadise.com/', 'approved', 0),
(72, 'LoungeFM', 'https://s35.derstream.net/digital.mp3', 'Relaxing and groovy tunes.', 'Austria', 'https://www.lounge.fm/wp-content/uploads/2008/08/loungefm_logo_black.jpg', 'German', '2024-12-02 11:18:25', 53, 'https://www.loungefm.com/', 'approved', 0),
(73, 'Bossa Nova Brazil', 'https://stream.streamgenial.stream/87eu2988rm0uv', 'The best of Bossa Nova.', 'Brazil', 'https://www.radio.de/300/bossanovabrazil.png', 'Braislian', '2024-12-02 11:18:25', 1, 'https://bossanova.com/', 'approved', 0),
(74, 'SmoothLounge', 'https://icecast.smoothjazz.com/smoothlounge', 'Smooth lounge music 24/7.', 'USA', 'https://smoothjazz.com/logo.png', 'English', '2024-12-02 11:18:25', 31, 'https://smoothjazz.com/', 'approved', 0),
(75, 'BigFM HipHop', 'https://streams.bigfm.de/bigfm-hiphop-128-mp3', 'HipHop non-stop.', 'Germany', 'https://bigfm.de/logo.png', 'German', '2024-12-02 11:18:25', 32, 'https://bigfm.de/', 'approved', 0),
(76, 'NRJ Hits', 'https://cdn.nrjaudio.fm/audio1/fr/30001/mp3_128.mp3', 'Hit music only.', 'France', 'https://www.nrj.fr/logo.png', 'French', '2024-12-02 11:18:25', 33, 'https://www.nrj.fr/', 'approved', 0),
(77, 'SomaFM Indie Pop Rocks', 'https://icecast.somafm.com/indiepop', 'Indie pop 24/7.', 'USA', 'https://somafm.com/logo.png', 'English', '2024-12-02 11:18:25', 34, 'https://somafm.com/indiepoprocks/', 'approved', 0),
(78, 'Chilltrax', 'https://stream.chilltrax.com/chilltrax', 'Your online chillout music.', 'USA', 'https://www.chilltrax.com/logo.png', 'English', '2024-12-02 11:18:25', 35, 'https://www.chilltrax.com/', 'approved', 0),
(79, 'Bayern 1', 'https://streams.br.de/bayern2_2.m3u', 'Die besten Oldies und Hits.', 'Germany', 'https://www.radio.at/175/bayern1fran.png?version=d665047a501dc2ea350eae79278dd12a', 'German', '2024-12-02 11:18:25', 1, 'https://www.br.de/radio/bayern1/', 'approved', 0),
(80, 'Jazz FM', 'https://media-ssl.musicradio.com/JazzFM', 'The world’s greatest jazz.', 'United Kingdom', 'https://www.jazzfm.com/logo.png', 'English', '2024-12-02 11:18:25', 37, 'https://www.jazzfm.com/', 'approved', 0),
(81, 'Radio U1 Tirol', 'https://live.u1-radio.at/', 'Dein Tiroler Heimatradio', 'Austria', 'https://www.sonicweb-radio.de/images/stations/1871-radiou1tirol.png', 'German', '2025-01-22 08:22:25', 1, '', 'approved', 0),
(82, 'Deutschlandfunk', 'https://st01.sslstream.dlf.de/dlf/01/128/mp3/stream.mp3?aggregator=web', 'Alles von Relevanz', 'Germany', 'https://bilder.deutschlandfunk.de/12/1d/ab/f6/121dabf6-aee7-472e-b0d1-40e522129808/deutschlandfunkbildmarkefarbesrgb-102-480x270.png', 'German', '2025-01-22 08:29:58', 65, '', 'approved', 0),
(83, 'Klassik Radio', 'https://live.streams.klassikradio.de/klassikradio-deutschland/stream/mp3', 'Klassische Musik hören', 'Germany', 'https://upload.wikimedia.org/wikipedia/commons/1/1d/Logo_klassikradio.svg', 'German', '2025-01-22 09:21:47', 5, '', 'approved', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `ticket_number` varchar(50) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('Open','In Progress','Closed') DEFAULT 'Open',
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `tickets`
--

INSERT INTO `tickets` (`id`, `ticket_number`, `user_id`, `title`, `description`, `created_at`, `updated_at`, `status`, `image_path`) VALUES
(9, 'TST-466533', 9, 'Test', 'Test', '2025-01-26 16:49:02', '2025-01-26 16:49:02', 'Open', NULL),
(10, 'TST-101970', 9, 'test2', 'Mein Test 2', '2025-01-26 16:58:06', '2025-01-26 16:58:06', 'Open', 'uploads/screenshot_6796699e7314b1.05627713.jpg');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `topics`
--

CREATE TABLE `topics` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `view_count` int(11) DEFAULT 0,
  `slug` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `topics`
--

INSERT INTO `topics` (`id`, `title`, `description`, `content`, `created_at`, `view_count`, `slug`) VALUES
(17, 'How I register on stuneo', 'In this topic, we explain you, how to register on stuneo.', '<p><strong>To register on tunespace, you need a valid email.&nbsp;</strong><br>If you have one, please follow the steps below:</p><ol><li>Go to <a href=\"https://open.stuneo.com\">https://open.stuneo.com</a></li><li>Click “Create an account” on the bottom</li><li>Now you see the steps´</li><li>Step 1 is the personal info stuff, eg. Firstname</li><li>Step 2 is the account info, eg. Username</li><li>Step 3 is language &amp; country</li><li>If you filled out all informations, you can click on “Regsiter”</li></ol><p>If the signin up was successful, you see a notification on the top left and you get a welcome email.</p><p>Enjoy listening!!<br><br><strong>PS:</strong> If you need any help, please contact <a href=\"mailto:help@stuneo.com\">help@stuneo.com</a> directly, for opening a ticket on stuneo help, you need a active stuneo account.</p>', '2024-11-18 19:45:43', 0, 'how-i-register-on-tunespace'),
(18, 'I forgot my password', 'If you forgot your password, please make sure you follow these steps.', '<p><strong>In this topic, we show you, how easy it is, if you forgot your password.</strong><br>Please follow these steps:</p><ol><li>Go to <a href=\"https://open.stuneo.com\">https://open.stuneo.com</a></li><li>Click on the “Forgot password?” link on the bottom</li><li>Please enter your email - Use only the email, that was / is in use for your tunespace account, otherwise the password reset do not work</li><li>You should get an email</li></ol><p><strong>Depending on the size of the requests, it may take some time before the e-mail is successfully delivered.&nbsp;</strong><br><strong>Please also note the success or error message at the top right</strong></p><p>If you need any assistent, please contact <a href=\"mailto:help@stuneo.com\">help@stuneo.com</a> directly, you need to remember your password to login and open a ticket on stuneo help.</p>', '2024-11-18 19:51:01', 0, 'i-forgot-my-password'),
(19, 'How to become a owner', 'In this topic, we explain you, how to becom a radio owner on stuneo', '<h4><strong>Criteria for Becoming a Sender Owner on tunespace</strong></h4><p><strong>1. Proof of Ownership:</strong><br>&nbsp; - Applicants must prove they own or are authorized to manage the station.<br>&nbsp; - Accepted proofs include:<br>&nbsp; - Official station documents (e.g., business registration, copyright ownership)<br>&nbsp; - Contracts or licensing agreements.</p><p><strong>2. Technical Requirements:</strong><br>&nbsp; - The station must have a stable stream URL (e.g., HTTP/HTTPS, MP3/AAC).</p><p><strong>3. Quality Standards:</strong><br>&nbsp; - The station must not broadcast illegal or harmful content (e.g., hate speech, copyright infringement).<br>&nbsp; - A minimum audio quality standard (e.g., at least 128 kbps for streams) is required.</p><p><strong>4. Station Activity:</strong><br>&nbsp; - The station must be active and broadcast content regularly (e.g., at least 20 hours per week).</p><p><strong>5. Contact Information:</strong><br><strong>&nbsp;&nbsp;</strong> -<strong> </strong>Applicants must provide a valid email address and phone number to be reachable in case of issues.</p><p><strong>6. Registered Account on stuneo:</strong><br><strong>&nbsp;&nbsp;</strong> &nbsp;- Applicants must have a registered and verified user account on stuneo.<br>&nbsp; &nbsp; - Only logged-in users can apply as station owners.</p><p><strong>7. Reputation Check:</strong><br><strong>&nbsp; &nbsp;&nbsp;</strong>-<strong> </strong>stuneo reserves the right to review applicants for prior violations of policies or suspicious activity (e.g., spam).</p><p><strong>8. Language of the Station:</strong><br>&nbsp; &nbsp; -<strong> </strong>The station must broadcast in at least one language supported by tunespace.</p><p><strong>9. Geographical Restrictions:</strong><br><strong>&nbsp; &nbsp;&nbsp;</strong> -<strong> </strong>If the station has geographical restrictions, these must be clearly specified</p><p>If you meet the criteria, you can register <a href=\"https://owner.stuneo.com/become-owner\">here</a>. Depending on the number of registrations, there may be a wait. Once your account has been successfully activated, you will receive access to the Owner Portal. Please note that we check every submission manually</p>', '2024-11-18 20:24:24', 0, 'how-to-become-a-owner');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `topic_feedback`
--

CREATE TABLE `topic_feedback` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_helpful` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `plan_id` int(11) DEFAULT NULL,
  `language` varchar(10) DEFAULT NULL,
  `country` varchar(10) DEFAULT NULL,
  `remember_token` varchar(64) DEFAULT NULL,
  `is_2fa_enabled` tinyint(1) DEFAULT 0,
  `2fa_code` varchar(6) DEFAULT NULL,
  `2fa_expires_at` datetime DEFAULT NULL,
  `system_language` varchar(5) DEFAULT 'en_US',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email_confirmed` tinyint(1) DEFAULT 0,
  `confirm_token` varchar(255) DEFAULT NULL,
  `stripe_customer_id` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `first_login` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `lastname`, `username`, `email`, `password`, `created_at`, `plan_id`, `language`, `country`, `remember_token`, `is_2fa_enabled`, `2fa_code`, `2fa_expires_at`, `system_language`, `reset_token`, `reset_token_expiry`, `updated_at`, `email_confirmed`, `confirm_token`, `stripe_customer_id`, `birthdate`, `first_login`) VALUES
(2, 'Rene', 'Paitz', 'Rene', 'rene.paitz@gmail.com', '$2y$10$HxGeyZjyXOlzT.KOzXYqA.ma7pXXj8xAZaoJvGXYp7mTPwr9pFcDq', '2024-11-11 13:08:54', 1, 'de', 'AT', 'a2aabdb9c7c3a67aed618b6fbbcf5385', 0, NULL, NULL, 'de_DE', NULL, NULL, '2025-02-01 17:42:14', 0, NULL, '', '0000-00-00', 0),
(3, 'Hans', 'Peter', 'hans', 'dominikhintringer2005@gmail.com', '$2y$10$9YFss1vfrJi3uNSDYt.MAO6mI5CRLlisMMP3XjraNQGLgFYenacPi', '2024-11-20 10:04:19', 1, 'de', 'AT', NULL, 0, NULL, NULL, 'en_US', NULL, NULL, '2025-02-02 09:38:33', 0, NULL, '', '2005-01-17', 0),
(4, 'd', 'dd', 'dddddddd', 'ddddd@d.com', '$2y$10$yO/VYpYw5j0/3Y4znue15eeinTJ/uvKoVTCUv8Mf4NJcDXxgUOmtG', '2024-12-02 03:33:15', 1, 'en', 'CA', NULL, 0, NULL, NULL, 'en_US', NULL, NULL, '2024-12-02 05:33:15', 0, NULL, '', '0000-00-00', 1),
(5, 'Wolfy', 'Blair', 'WolfyBlair', 'hhapexhh@gmail.com', '$2y$10$pdHeoM8nVbA1RNc7CqjAd.x07sQIxzh3B9CKXacq.99QN6dF/Dm8y', '2024-12-02 07:21:46', 1, 'en', 'US', NULL, 0, NULL, NULL, 'en_US', NULL, NULL, '2024-12-02 09:21:46', 0, NULL, '', '0000-00-00', 1),
(9, 'Dominik', 'Hintringer', 'dominik', 'hintringerdominik@gmail.com', '$2y$10$HR/Vi2dhhukdUbpj6VS7A.9/hd4XhbbYZECsPbyo4sJ/odS6OA3iO', '2025-01-20 14:47:41', 1, 'de', 'AT', NULL, 0, NULL, NULL, 'de_DE', NULL, NULL, '2025-01-20 17:49:57', 0, NULL, '', '2005-01-17', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_history`
--

CREATE TABLE `user_history` (
  `history_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `station_id` int(11) NOT NULL,
  `listened_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `user_history`
--

INSERT INTO `user_history` (`history_id`, `user_id`, `station_id`, `listened_at`) VALUES
(1, 3, 3, '2025-01-08 19:21:41'),
(2, 3, 41, '2025-01-08 19:33:48'),
(3, 3, 3, '2025-01-08 19:34:01'),
(4, 3, 3, '2025-01-08 19:36:01'),
(5, 3, 20, '2025-01-20 17:17:38'),
(6, 3, 3, '2025-01-20 17:17:40'),
(7, 3, 39, '2025-01-20 17:19:11'),
(8, 3, 21, '2025-01-21 20:27:35'),
(9, 2, 3, '2025-02-01 13:13:47'),
(10, 2, 3, '2025-02-01 13:13:51'),
(11, 2, 20, '2025-02-01 16:27:15'),
(12, 2, 20, '2025-02-01 16:56:02'),
(13, 2, 20, '2025-02-01 16:56:04'),
(14, 3, 72, '2025-02-02 07:20:20'),
(15, 3, 3, '2025-02-02 07:20:22'),
(16, 3, 42, '2025-02-02 08:03:34'),
(17, 3, 33, '2025-02-02 08:04:04'),
(18, 3, 26, '2025-02-02 08:06:44'),
(19, 3, 26, '2025-02-02 08:06:52'),
(20, 3, 33, '2025-02-02 08:26:57'),
(21, 3, 33, '2025-02-02 08:27:06'),
(22, 3, 33, '2025-02-02 08:27:07'),
(23, 3, 33, '2025-02-02 08:27:09');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_listens`
--

CREATE TABLE `user_listens` (
  `user_id` int(11) NOT NULL,
  `station_id` int(11) NOT NULL,
  `listen_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `user_listens`
--

INSERT INTO `user_listens` (`user_id`, `station_id`, `listen_date`) VALUES
(1, 3, '2024-11-05 19:58:34'),
(1, 4, '2024-11-05 19:58:34'),
(1, 20, '2024-11-07 21:41:40'),
(1, 21, '2024-11-07 21:42:42'),
(1, 22, '2024-11-07 21:41:43'),
(1, 23, '2024-11-07 21:40:52'),
(1, 24, '2024-11-07 21:41:37'),
(1, 26, '2024-11-11 18:57:11'),
(1, 27, '2024-11-07 21:40:25'),
(1, 29, '2024-11-07 21:40:33'),
(1, 30, '2024-11-19 23:51:38'),
(1, 31, '2024-11-07 21:40:31'),
(1, 32, '2024-11-07 21:41:19'),
(1, 33, '2024-12-02 11:46:36'),
(1, 34, '2024-12-02 12:06:30'),
(1, 35, '2024-12-02 11:40:43'),
(1, 36, '2024-12-02 12:06:58'),
(1, 37, '2024-12-02 11:40:56'),
(1, 38, '2024-12-02 11:54:12'),
(1, 39, '2024-12-02 11:41:05'),
(1, 40, '2024-12-02 11:41:14'),
(1, 41, '2024-12-02 11:53:21'),
(1, 42, '2024-12-02 11:49:58'),
(1, 62, '2024-12-02 12:58:51'),
(1, 64, '2024-12-02 12:59:24'),
(1, 69, '2024-12-02 12:59:01'),
(2, 3, '2025-02-01 14:11:22'),
(2, 20, '2025-02-01 17:27:16'),
(3, 3, '2024-12-09 00:46:51'),
(3, 4, '2024-12-29 18:12:49'),
(3, 5, '2025-01-26 18:36:48'),
(3, 20, '2024-12-29 18:14:47'),
(3, 21, '2024-12-29 18:20:59'),
(3, 23, '2024-12-29 19:11:36'),
(3, 24, '2025-01-01 15:46:52'),
(3, 25, '2024-12-29 22:22:37'),
(3, 26, '2025-01-06 14:57:34'),
(3, 30, '2024-12-30 18:20:50'),
(3, 31, '2024-12-30 18:20:54'),
(3, 32, '2024-12-30 18:20:58'),
(3, 33, '2024-12-29 18:51:44'),
(3, 37, '2025-01-01 21:16:27'),
(3, 39, '2025-01-20 18:19:14'),
(3, 40, '2025-01-06 16:00:01'),
(3, 41, '2025-01-08 20:33:49'),
(3, 42, '2025-02-02 09:03:39'),
(3, 61, '2024-12-29 19:10:25'),
(3, 63, '2024-12-29 22:46:31'),
(3, 64, '2024-12-29 22:20:02'),
(3, 65, '2024-12-29 22:20:53'),
(3, 66, '2025-01-01 21:11:49'),
(5, 3, '2024-11-12 17:33:15'),
(9, 3, '2025-01-20 16:09:22');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_podcast_progress`
--

CREATE TABLE `user_podcast_progress` (
  `progress_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `podcast_id` int(11) NOT NULL,
  `episode_guid` varchar(255) NOT NULL,
  `current_time_user` int(11) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `user_podcast_progress`
--

INSERT INTO `user_podcast_progress` (`progress_id`, `user_id`, `podcast_id`, `episode_guid`, `current_time_user`, `last_updated`) VALUES
(1, 3, 1, 'podlove-2025-01-02t09:49:22+00:00-699727a727f1751', 639, '2025-01-26 17:38:02'),
(213, 3, 1, 'podlove-2024-11-28t05:22:46+00:00-b743e0ec77cbedf', 7, '2025-01-08 14:39:55'),
(2589, 9, 12, '6c9700a7-a8b0-4e93-9799-b25f01303a23', 994, '2025-01-20 17:12:55'),
(3952, 3, 2, 'ae09cca2-3391-11ef-9de2-e795aa8eb8bd', 0, '2025-02-01 16:44:12'),
(3955, 2, 7, 'gid://art19-episode-locator/V0/KnX6_vY7wAOmRLbA8er68-U0u201rEi1K1L-Oeab5ZU', 209, '2025-02-02 07:48:40'),
(4000, 3, 7, 'gid://art19-episode-locator/V0/KnX6_vY7wAOmRLbA8er68-U0u201rEi1K1L-Oeab5ZU', 568, '2025-02-01 16:48:33');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indizes für die Tabelle `changelog_owners`
--
ALTER TABLE `changelog_owners`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indizes für die Tabelle `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`country_code`);

--
-- Indizes für die Tabelle `download_links`
--
ALTER TABLE `download_links`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`favorite_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indizes für die Tabelle `favorites_podcast`
--
ALTER TABLE `favorites_podcast`
  ADD PRIMARY KEY (`favorite_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `podcast_id` (`podcast_id`);

--
-- Indizes für die Tabelle `favorites_tokens`
--
ALTER TABLE `favorites_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `share_token` (`share_token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`language_code`);

--
-- Indizes für die Tabelle `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `notification_read_status`
--
ALTER TABLE `notification_read_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_notification_owner` (`notification_id`,`owner_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indizes für die Tabelle `owner_podcasts`
--
ALTER TABLE `owner_podcasts`
  ADD PRIMARY KEY (`owner_id`,`podcast_id`),
  ADD KEY `podcast_id` (`podcast_id`);

--
-- Indizes für die Tabelle `owner_station`
--
ALTER TABLE `owner_station`
  ADD PRIMARY KEY (`owner_id`,`station_id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indizes für die Tabelle `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indizes für die Tabelle `podcasts`
--
ALTER TABLE `podcasts`
  ADD PRIMARY KEY (`podcast_id`),
  ADD UNIQUE KEY `rss_url` (`rss_url`),
  ADD KEY `fk_podcast_category` (`category_id`);

--
-- Indizes für die Tabelle `podcast_categories`
--
ALTER TABLE `podcast_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indizes für die Tabelle `radio_genres`
--
ALTER TABLE `radio_genres`
  ADD PRIMARY KEY (`genre_id`),
  ADD UNIQUE KEY `genre_name` (`genre_name`);

--
-- Indizes für die Tabelle `radio_owners`
--
ALTER TABLE `radio_owners`
  ADD PRIMARY KEY (`owner_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indizes für die Tabelle `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `stations`
--
ALTER TABLE `stations`
  ADD PRIMARY KEY (`station_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indizes für die Tabelle `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_number` (`ticket_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indizes für die Tabelle `topic_feedback`
--
ALTER TABLE `topic_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indizes für die Tabelle `user_history`
--
ALTER TABLE `user_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indizes für die Tabelle `user_listens`
--
ALTER TABLE `user_listens`
  ADD PRIMARY KEY (`user_id`,`station_id`);

--
-- Indizes für die Tabelle `user_podcast_progress`
--
ALTER TABLE `user_podcast_progress`
  ADD PRIMARY KEY (`progress_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`episode_guid`),
  ADD KEY `podcast_id` (`podcast_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `changelog_owners`
--
ALTER TABLE `changelog_owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT für Tabelle `download_links`
--
ALTER TABLE `download_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `favorites`
--
ALTER TABLE `favorites`
  MODIFY `favorite_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=226;

--
-- AUTO_INCREMENT für Tabelle `favorites_podcast`
--
ALTER TABLE `favorites_podcast`
  MODIFY `favorite_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `favorites_tokens`
--
ALTER TABLE `favorites_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `notification_read_status`
--
ALTER TABLE `notification_read_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT für Tabelle `plans`
--
ALTER TABLE `plans`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `podcasts`
--
ALTER TABLE `podcasts`
  MODIFY `podcast_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT für Tabelle `podcast_categories`
--
ALTER TABLE `podcast_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT für Tabelle `radio_genres`
--
ALTER TABLE `radio_genres`
  MODIFY `genre_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT für Tabelle `radio_owners`
--
ALTER TABLE `radio_owners`
  MODIFY `owner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT für Tabelle `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `stations`
--
ALTER TABLE `stations`
  MODIFY `station_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT für Tabelle `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT für Tabelle `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT für Tabelle `topic_feedback`
--
ALTER TABLE `topic_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT für Tabelle `user_history`
--
ALTER TABLE `user_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT für Tabelle `user_podcast_progress`
--
ALTER TABLE `user_podcast_progress`
  MODIFY `progress_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4570;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`station_id`) REFERENCES `stations` (`station_id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `favorites_podcast`
--
ALTER TABLE `favorites_podcast`
  ADD CONSTRAINT `favorites_podcast_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_podcast_ibfk_2` FOREIGN KEY (`podcast_id`) REFERENCES `podcasts` (`podcast_id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `favorites_tokens`
--
ALTER TABLE `favorites_tokens`
  ADD CONSTRAINT `favorites_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints der Tabelle `notification_read_status`
--
ALTER TABLE `notification_read_status`
  ADD CONSTRAINT `notification_read_status_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_read_status_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `radio_owners` (`owner_id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `owner_podcasts`
--
ALTER TABLE `owner_podcasts`
  ADD CONSTRAINT `owner_podcasts_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `radio_owners` (`owner_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `owner_podcasts_ibfk_2` FOREIGN KEY (`podcast_id`) REFERENCES `podcasts` (`podcast_id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `owner_station`
--
ALTER TABLE `owner_station`
  ADD CONSTRAINT `owner_station_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `radio_owners` (`owner_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `owner_station_ibfk_2` FOREIGN KEY (`station_id`) REFERENCES `stations` (`station_id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `podcasts`
--
ALTER TABLE `podcasts`
  ADD CONSTRAINT `fk_podcast_category` FOREIGN KEY (`category_id`) REFERENCES `podcast_categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `stations`
--
ALTER TABLE `stations`
  ADD CONSTRAINT `stations_ibfk_1` FOREIGN KEY (`genre_id`) REFERENCES `radio_genres` (`genre_id`);

--
-- Constraints der Tabelle `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `topic_feedback`
--
ALTER TABLE `topic_feedback`
  ADD CONSTRAINT `topic_feedback_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `topic_feedback_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `user_history`
--
ALTER TABLE `user_history`
  ADD CONSTRAINT `user_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_history_ibfk_2` FOREIGN KEY (`station_id`) REFERENCES `stations` (`station_id`);

--
-- Constraints der Tabelle `user_podcast_progress`
--
ALTER TABLE `user_podcast_progress`
  ADD CONSTRAINT `user_podcast_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_podcast_progress_ibfk_2` FOREIGN KEY (`podcast_id`) REFERENCES `podcasts` (`podcast_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
