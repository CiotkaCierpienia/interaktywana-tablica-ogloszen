/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     2011-03-31 15:24:20                          */
/*==============================================================*/

create database projekt;

ALTER DATABASE DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

drop table if exists asoc_ogl_stud;

drop table if exists asoc_stud_grupa;

drop table if exists grupa;

drop table if exists konsultacje;

drop table if exists oceny;

drop table if exists ogloszenia;

drop table if exists prowadzacy;

drop table if exists przedmioty;

drop table if exists slownik_ocen;

drop table if exists student;

drop table if exists typy_ocen;

/*==============================================================*/
/* Table: asoc_ogl_stud                                         */
/*==============================================================*/
create table asoc_ogl_stud
(
   id_asoc_ogl_stud     int not null auto_increment,
   id_ogloszenia        smallint not null,
   indeks               int not null,
   primary key (id_asoc_ogl_stud)
);

/*==============================================================*/
/* Table: asoc_stud_grupa                                       */
/*==============================================================*/
create table asoc_stud_grupa
(
   id_asoc_stud_grupa   int not null auto_increment,
   id_grupy             int not null,
   indeks               int not null,
   primary key (id_asoc_stud_grupa)
);

/*==============================================================*/
/* Table: grupa                                                 */
/*==============================================================*/
create table grupa
(
   id_grupy             int not null auto_increment,
   id_przedmiotu        int not null,
   kod_grupy            varchar(8) not null,
   forma                enum('wyklad','cwiczenia','laboratorium','projekt','seminarium') not null,
   primary key (id_grupy)
);

/*==============================================================*/
/* Table: konsultacje                                           */
/*==============================================================*/
create table konsultacje
(
   id_konsultacji       smallint not null auto_increment,
   id_osoby             smallint not null,
   dzien                enum('poniedzialek','wtorek','sroda','czwartek','piatek','sobota','niedziela') not null,
   od_                  time not null,
   do_                  time not null,
   primary key (id_konsultacji)
);

/*==============================================================*/
/* Table: oceny                                                 */
/*==============================================================*/
create table oceny
(
   id_oceny             int not null auto_increment,
   id_soceny            int,
   id_typu              int,
   id_asoc_stud_grupa   int,
   ocena                float(2,1),
   inf_dod              varchar(500),
   data_wprowadzenia    date,
   primary key (id_oceny)
);

/*==============================================================*/
/* Table: ogloszenia                                            */
/*==============================================================*/
create table ogloszenia
(
   id_ogloszenia        int not null auto_increment,
   id_osoby             smallint not null,
   ogloszenie           text not null,
   data                 date not null,
   data_wygasniecia     date,
   priorytet            int(3) not null,
   primary key (id_ogloszenia)
);

/*==============================================================*/
/* Table: prowadzacy                                            */
/*==============================================================*/
create table prowadzacy
(
   id_osoby             smallint not null auto_increment,
   imie                 varchar(50) not null,
   nazwisko             varchar(50) not null,
   stopien_naukowy      varchar(20) not null,
   status               enum('jest','nie ma','jest zajety'),
   email                varchar(50) not null,
   nr_telefonu          int,
   haslo                varchar(20) not null,
   potwierdzony         bool,
   primary key (id_osoby)
);

/*==============================================================*/
/* Table: przedmioty                                            */
/*==============================================================*/
create table przedmioty
(
   id_przedmiotu        int not null auto_increment,
   id_osoby             smallint not null,
   przedmiot            varchar(100) not null,
   primary key (id_przedmiotu)
);

/*==============================================================*/
/* Table: slownik_ocen                                          */
/*==============================================================*/
create table slownik_ocen
(
   id_soceny            int not null auto_increment,
   ocena                float(2,1) not null,
   primary key (id_soceny)
);

/*==============================================================*/
/* Table: student                                               */
/*==============================================================*/
create table student
(
   indeks               int not null auto_increment,
   imie                 varchar(50) not null,
   nazwisko             varchar(50) not null,
   primary key (indeks)
);

/*==============================================================*/
/* Table: typy_ocen                                             */
/*==============================================================*/
create table typy_ocen
(
   id_typu              int not null auto_increment,
   nazwa_typu           varchar(30) not null,
   primary key (id_typu)
);

alter table asoc_ogl_stud add constraint fk_asco_ogl_stud_has_ogloszenia foreign key (id_ogloszenia)
      references ogloszenia (id_ogloszenia) on delete restrict on update restrict;

alter table asoc_ogl_stud add constraint fk_asoc_ogl_stud_has_student foreign key (indeks)
      references student (indeks) on delete restrict on update restrict;

alter table asoc_stud_grupa add constraint fk_asoc_stud_grupa_has_grupa foreign key (id_grupy)
      references grupa (id_grupy) on delete restrict on update restrict;

alter table asoc_stud_grupa add constraint fk_asoc_stud_grupa_has_student foreign key (indeks)
      references student (indeks) on delete restrict on update restrict;

alter table grupa add constraint fk_grupa_has_prowadzacy foreign key (id_przedmiotu)
      references przedmioty (id_przedmiotu) on delete restrict on update restrict;

alter table konsultacje add constraint fk_konsultacje_has_prowadzacy foreign key (id_osoby)
      references prowadzacy (id_osoby) on delete restrict on update restrict;

alter table oceny add constraint fk_oceny_has_asoc_stud_grupa foreign key (id_asoc_stud_grupa)
      references asoc_stud_grupa (id_asoc_stud_grupa) on delete restrict on update restrict;

alter table oceny add constraint fk_oceny_has_slownik_ocen foreign key (id_soceny)
      references slownik_ocen (id_soceny) on delete restrict on update restrict;

alter table oceny add constraint fk_oceny_has_typy_ocen foreign key (id_typu)
      references typy_ocen (id_typu) on delete restrict on update restrict;

alter table ogloszenia add constraint fk_ogloszenia_has_prowadzacy foreign key (id_osoby)
      references prowadzacy (id_osoby) on delete restrict on update restrict;

alter table przedmioty add constraint fk_przedmioty_has_prowdzacy foreign key (id_osoby)
      references prowadzacy (id_osoby) on delete restrict on update restrict;
