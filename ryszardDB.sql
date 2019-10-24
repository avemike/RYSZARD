mysql -u root

create database ryszardDB CHARACTER SET utf8 COLLATE utf8_unicode_ci;

use ryszardDB

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

create table characters (
    char_id int not null auto_increment,
    user_id int,
    server_id int,
    char_class varchar(255),
    nickname varchar(255),
    currency int,
    level int,
    exp int,
    strength int,
    hp int,
    dex int,
    luck int,
    race varchar(255),

    PRIMARY KEY (char_id),
    FOREIGN KEY (user_id) REFERENCES accounts(user_id),
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
    strength int,
    hp int,
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
    enemy_template_id int,
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

INSERT INTO accounts (login, password) values ("andrzejek", "76d80224611fc919a5d54f0ff9fba446");

INSERT INTO servers (server_id) values ("1");
INSERT INTO servers (server_id) values ("2");
INSERT INTO servers (server_id) values ("3");

INSERT INTO characters (user_id, server_id, nickname, level, exp, currency, char_class) values ("1", "1", "andrzejekchar", 24, 1000, 10000, 1);

INSERT INTO mission_template (mission_description, mission_name) values ("Po długiej walce z gaborem na gołe pięści i dzwonki szkolne udało Ci się zwyciężyć", "Wyprawa do Gabora");
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
values ("1", "3", "0", "elektronik bron nr.1");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("2", "3", "0", "elektronik bron nr.2");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("3", "3", "0", "elektronik bron nr.3");

INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("4", "3", "1", "elektronik armor nr.1");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("5", "3", "1", "elektronik armor nr.2");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("6", "3", "1", "elektronik armor nr.3");


INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("13", "0", "2", "tarcza nr.1");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("14", "0", "2", "tarcza nr.2");

INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("7", "0", "3", "helm nr.1");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("8", "0", "3", "helm nr.2");

INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("11", "0", "4", "buty nr.1");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("12", "0", "4", "buty nr.2");

INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("9", "0", "5", "rekawice nr.1");
INSERT INTO item_template (item_icon, item_class, item_type, item_name)
values ("10", "0", "5", "rekawice nr.2");

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


SELECT servers.server_id, char_id FROM servers left join characters on servers.server_id = characters.server_id where user_id="1" or user_id IS NULL



    SELECT server_id, char_id, level, nickname 
    FROM (
        SELECT servers.server_id, char_id, level, nickname
        FROM servers
        JOIN characters
        ON servers.server_id = characters.server_id
        WHERE user_id=2

        UNION

        SELECT
            servers.server_id,
            NULL AS char_id,
            NULL AS LEVEL,
            NULL AS nickname
        FROM
            servers
            LEFT JOIN characters
            ON servers.server_id = characters.server_id
        WHERE user_id!=2 or user_id IS NULL
    ) t
    GROUP BY server_id


    INSERT INTO missions (char_id, currency_reward, exp_reward, duration_time, start_date, mission_active)
values ("1", "100", "200", "10", CURRENT_TIMESTAMP(), "1")
