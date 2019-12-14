-- create database ryszardDB CHARACTER SET utf8 COLLATE utf8_unicode_ci

create table accounts (
    user_id int not null auto_increment,
    login varchar(255),
    password varchar(255),
    creation_date timestamp DEFAULT current_timestamp,
    last_logged timestamp DEFAULT current_timestamp,

    PRIMARY KEY (user_id)

);

create table servers (
    server_id int not null auto_increment,

    PRIMARY KEY (server_id)
);

create table classes (
    class_id int not null auto_increment,
    class_name varchar(255),

    PRIMARY KEY (class_id)
);

create table characters (
    char_id int not null auto_increment,
    user_id int,
    server_id int,
    char_class int,
    nickname varchar(255),
    currency int,
    level int,
    exp int,
    exp_to_next_lv int,
    attack int,
    defence int,
    strength int,
    intelligence int,
    vit int,
    dex int,
    luck int,
    race varchar(255),
    icon int DEFAULT 1,
    collect_date timestamp DEFAULT current_timestamp,

    PRIMARY KEY (char_id),
    FOREIGN KEY (user_id) REFERENCES accounts(user_id),
    FOREIGN KEY (char_class) REFERENCES classes(class_id),
    FOREIGN KEY (server_id) REFERENCES servers(server_id)
);

create table item_template (
    item_template_id int not null auto_increment,
    item_name varchar(255),
    item_icon varchar(255),
    item_description varchar(255),
    item_class varchar(255),
    item_type varchar(255),

    PRIMARY KEY (item_template_id)
);

create table items (
    item_id int not null auto_increment,
    item_template_id int,
    char_id int,
    value int,
    attack int,
    defence int,
    strength int,
    vit int,
    dex int,
    luck int,
    intelligence int,
    every_attrib int,
    item_status int,
    item_place int,

    PRIMARY KEY (item_id),
    FOREIGN KEY (item_template_id) REFERENCES item_template(item_template_id),
    FOREIGN KEY (char_id) REFERENCES characters(char_id)
);

create table mission_template (
    mission_template_id int not null auto_increment,
    mission_name varchar(255),
    mission_description varchar(255),

    PRIMARY KEY (mission_template_id)
);

create table missions (
    mission_id int not null auto_increment,
    mission_template_id int,
    char_id int,
    currency_reward int,
    exp_reward int,
    duration_time int,
    start_date timestamp DEFAULT current_timestamp,
    item_id int,
    mission_active boolean DEFAULT false,

    PRIMARY KEY (mission_id),
    FOREIGN KEY (item_id) REFERENCES items(item_id),
    FOREIGN KEY (mission_template_id) REFERENCES mission_template(mission_template_id),
    FOREIGN KEY (char_id) REFERENCES characters(char_id)
);

create table enemy_template (
    enemy_template_id int not null auto_increment,
    enemy_name varchar(255),
    enemy_icon varchar(255),
    enemy_class varchar(255),

    PRIMARY KEY (enemy_template_id)
);

create table mail (
    mail_id int not null auto_increment,
    mail_date timestamp DEFAULT CURRENT_TIMESTAMP,
    mail_title varchar(255),
    mail_sender int,
    mail_receiver int,
    mail_content varchar(65535),

    PRIMARY KEY (mail_id),
    FOREIGN KEY (mail_sender) REFERENCES characters(char_id),
    FOREIGN KEY (mail_receiver) REFERENCES characters(char_id)
);


INSERT INTO servers (server_id) values ("1");
INSERT INTO servers (server_id) values ("2");
INSERT INTO servers (server_id) values ("3");

INSERT INTO classes (class_name) values ("informatyk");
INSERT INTO classes (class_name) values ("mechatronik");
INSERT INTO classes (class_name) values ("elektronik");

INSERT INTO mission_template (mission_description, mission_name) values ("Po wyrecytowaniu, na baczność, wszystkich wzorów skróconego mnożenia siadasz w ławce i patrzysz na masakre \"kolegów\" z klasy", "Lekcja matematyki");
INSERT INTO mission_template (mission_description, mission_name) values ("Pan kondesator wręczył Ci swój złoty śrubokręt na znak szacunku", "Wyprawa do Kondensatora");
INSERT INTO mission_template (mission_description, mission_name) values ("Dostałeś obietnicę, że dostaniesz nowy identyfikator, przyjdź go odebrać za dwa lata", "Sekretariat");
INSERT INTO mission_template (mission_description, mission_name) values ("Udało Ci się jakoś stamtąd uciec recytując fragmenty \"Pana Tadeusza\" z pamięci", "Sala 102/Legowisko diabła");
INSERT INTO mission_template (mission_description, mission_name) values ("Po krótkiej rozgrzewce i dwóch godzinach grania w siatkówkę, wychodzisz z tego prawie cało", "Wizyta u WFistów");
INSERT INTO mission_template (mission_description, mission_name) values ("Pan Kierownik nakrzyczał na nas za używanie makaronów", "Wizyta kierownika");



INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("1", "1", "0", "informatyk bron nr.1");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("2", "1", "0", "informatyk bron nr.2");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("3", "1", "0", "informatyk bron nr.3");

INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("4", "1", "1", "informatyk armor nr.1");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("5", "1", "1", "informatyk armor nr.2");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("6", "1", "1", "informatyk armor nr.3");


INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("1", "2", "0", "mechatronik bron nr.1");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("2", "2", "0", "mechatronik bron nr.2");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("3", "2", "0", "mechatronik bron nr.3");

INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("4", "2", "1", "mechatronik armor nr.1");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("5", "2", "1", "mechatronik armor nr.2");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("6", "2", "1", "mechatronik armor nr.3");


INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("1", "3", "0", "Wkrętak krzyżowy Philips");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("2", "3", "0", "Multimetr Winiarskiego mamy");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("3", "3", "0", "elektronik bron nr.3");

INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("4", "3", "1", "Szal z kabla DEVIsnow 30T/230V,45M,1350W 89846012");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("5", "3", "1", "elektronik armor nr.2");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("6", "3", "1", "elektronik armor nr.3");


INSERT INTO item_template (item_icon, item_description, item_class, item_type, item_name)
values ("1", "", "0", "6", "Szczotka sprzątaczki");
INSERT INTO item_template (item_icon, item_description, item_class, item_type, item_name)
values ("2", "", "0", "6", "Krzesło kierownika");
INSERT INTO item_template (item_icon, item_description, item_class, item_type, item_name)
values ("3", "", "0", "6", "Kreda spod 102");
INSERT INTO item_template (item_icon, item_description, item_class, item_type, item_name)
values ("4", "", "0", "6", "Rozkładane egzaminowe krzesło");
INSERT INTO item_template (item_icon, item_description, item_class, item_type, item_name)
values ("5", "", "0", "6", "Zepsuta drukarka");
INSERT INTO item_template (item_icon, item_description, item_class, item_type, item_name)
values ("6", "", "0", "6", "Płyta główna z 205");

INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("7", "0", "3", "Kosz na śmieci Michała mamy");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("8", "0", "3", "Garnek z bufetu szkolnego");

INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("9", "0", "5", "Opaska antystatyczna Popiela");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("10", "0", "5", "rekawice nr.2");

INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("11", "0", "4", "Drewniane klapki, które nie działają");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("12", "0", "4", "Zagubiony crocs twojego starego");

INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("13", "0", "2", "Router Cipsco z CKP");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("14", "0", "2", "tarcza nr.2");


INSERT INTO enemy_template (enemy_name)
values ("Pan Gabor");
INSERT INTO enemy_template (enemy_name)
values ("Elektrozbigniew");
INSERT INTO enemy_template (enemy_name)
values ("Ktoś jeszcze");
INSERT INTO enemy_template (enemy_name)
values ("Więcej przeciwników");
INSERT INTO enemy_template (enemy_name)
values ("Trzeba dodać");


INSERT INTO accounts (login, password) values ("andrzejek", "76d80224611fc919a5d54f0ff9fba446");
INSERT INTO `characters` (`user_id`, `server_id`, `char_class`, `nickname`, `currency`, `level`, `exp`, `exp_to_next_lv`, `attack`, `defence`, `strength`, `intelligence`, `vit`, `dex`, `luck`, `race`, `icon`, `collect_date`) VALUES
(1, 1, 1, 'Hejka', 4000, 5, 200, 2200, 10, 10, 10, 10, 100, 10, 10, 'czlowiek', 2, '2019-12-14 12:44:18');

