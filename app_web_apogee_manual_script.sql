/*
--*********************************************************************
--**            The data bases of web application apogee             **
--**            By : + ELHAMDAOUI abdelmajid                         **
--**                 + SAKAKINI   hafsa                              **
--**				 + ELFISSI    ilham                              **
--*********************************************************************
*/
drop table if exists ACTUALITE;
drop table if exists ADMINISTRATEUR;
drop table if exists nom_module;
drop table if exists fil_bac;
drop table if exists etudiant_dip;
drop table if exists reclamation;
drop table if exists etudiant;
drop table if exists modul;
drop table if exists semestre;
drop table if exists professeur;
drop table if exists filiere;
drop table if exists departement;
drop table if exists baccalaureat;
drop table if exists diplome;

/*
--table diplome
*/
create table diplome(
					 nom_dip varchar(100) primary key
					 );
/*
--table baccalaureat
*/
create table baccalaureat(
						 nom_bac varchar(50) primary key
						 );
/*
--table Departement
*/
create table departement(
                        id_d int primary key  AUTO_INCREMENT,
						nom_d varchar(50) not null,
						cin_chef varchar(20)					
						);
/*					
--table filiere
*/
create table filiere(
					id_f int primary key  AUTO_INCREMENT,
					nom_f varchar(100) not null,
					cin_cor varchar(20),
					id_d int not null,
					constraint un_nom_f unique(nom_f),
					constraint fk_fil_dep foreign key(id_d) references departement(id_d) on update cascade on delete cascade
					);
/*
--table professeur
*/
create table professeur(
						cin_p varchar(20) primary key,
						nom_p varchar(50) not null,
						prenom_p varchar(50) not null,
						sexe_p varchar(3) not null,
						email_p varchar(100) not null,
						ntel_p varchar(15) not null,
						adresse_p text not null,
						pwd_p varchar(200) not null,/*-- default concat(cin_p,nom_p)*/
						dateN_p date not null,
						id_f int not null,
						id_f_cor int,
						id_d_chef int,
						photo_p longblob not null, 
						nationalite varchar(50) not null,
						constraint ck_prof_sexe check(sexe_p in ('H','F')),
						constraint fk_prof_fil foreign key(id_f) references filiere(id_f) on update cascade on delete cascade,
						constraint fk_prof_fil2 foreign key(id_f_cor) references filiere(id_f) on delete set null on update cascade,
						constraint fk_prof_dep foreign key(id_d_chef) references departement(id_d) on delete set null on update cascade
						);
/*
--add the constraints in the tables departement and filiere.
*/
alter table departement
          add constraint fk_dep_prof foreign key(cin_chef) references professeur(cin_p) on delete set null on update cascade;
alter table filiere
          add constraint fk_fil_prof foreign key(cin_cor) references professeur(cin_p) on delete set null on update cascade;
/*
--table semestre
*/
create table semestre(
					nom_s varchar(10) primary key,
					nom_s_eq  varchar(10),
					constraint fk_sem_eq foreign key(nom_s_eq) references semestre(nom_s) on delete set null on update cascade
					);
/*
--table modul
*/
  create table modul(
                     id_m varchar(5) primary key, /*--id=M1,M2,....M23..*/
                     nom_s varchar(10) not null,
					 id_eq varchar(10),
					 constraint fk_mod_sem foreign key(nom_s) references semestre(nom_s) on delete set null on update cascade,
					 constraint fk_mod_mod foreign key(id_eq) references modul(id_m) on delete set null on update cascade
					 );

/*
--table etudiant
*/
create table etudiant(
                     cne_e varchar(15) primary key,
					 cin_e varchar(20) not null,
					 nom_e varchar(50) not null,
					 prenom_e varchar(50) not null,
					 dateN_e date not null,
					 lieuN_e text not null,
					 email_e varchar(100) not null,
					 nTel_e varchar(15),
					 adresse_e text not null,
					 numins_e varchar(15),
					 dateins_e date not null,
					 sexe_e varchar(3) not null,
					 nationalite varchar(50) not null,
					 id_f int not null,
					 photo_e longblob not null,
					 nom_bac varchar(50) not null,
					 moyenne_bac decimal(5,2) not null,
					 date_bac date not null,
					 type_bac varchar(10) default 'normal',
					 nat_bac varchar(50) default 'marocaine',
					 pwd_e varchar(200),
					 constraint fk_etd_fil foreign key(id_f) references filiere(id_f) on delete cascade on update cascade,
					 constraint ck_etd_sexe check(sexe_e in ('H','F')),
					 constraint fk_etd_bac foreign key(nom_bac) references baccalaureat(nom_bac)  on update cascade,
					 constraint ck_etd_type_bac check (type_bac in('libre','normal'))
					 );						 
/*
--table reclamation
*/
create table reclamation(
						id_r int primary key  AUTO_INCREMENT,
						type_r text not null,/*--fait check ,recommandé*/
						contenu_r text not null,
						date_r date not null,
						etat_r varchar(12) default 'non traité',
						cne_e varchar(15) not null,
						constraint fk_rec_etd foreign key(cne_e) references etudiant(cne_e) on delete cascade on update cascade,
						constraint ck_rec_etat check (etat_r in ('traite','non traite'))
						);
/*
--table etudiant_dip
*/
create table etudiant_dip(
						nom_dip varchar(100),
						cne_e varchar(15),
						date_obt date not null,
						type_dip varchar(100) not null,
						mention_dip varchar(20) not null,
						constraint fk_etd_dip_etd foreign key(cne_e) references etudiant(cne_e) on delete cascade on update cascade ,
						constraint fk_etd_dip_dip foreign key(nom_dip) references diplome(nom_dip)  on update cascade,
						constraint pk_etudiant_dip primary key(cne_e,nom_dip),
						constraint ck_type_dip check (type_dip in('fondamental','professionel','spécialiste')),
						constraint ck_mention_dip check (mention_dip in('Passable','Assez bien','Bien','Très bien'))
						);

/*
--table inscription
*/
create table inscription(
						cne_e varchar(15) not null,
						id_m varchar(5) not null,
						note_N decimal(5,2) default null,
						note_R decimal(5,2) default null,
						date_ins date not null,
						nb_ins int default 1,
						etat_V varchar(10),
						constraint fk_insc_etd foreign key(cne_e) references etudiant(cne_e) on delete cascade on update cascade,
						constraint fk_insc_mod foreign key(id_m) references modul(id_m),
						constraint pk_insc primary key(cne_e,id_m),
						constraint ck_etat_v check (etat_V in('VM','VAR','RI','VC','ABS')),
						constraint ck_nb_ins check(nb_ins<=3)
						);

/*
--table des baccalaureats accéptées en filières
*/
create table fil_bac( 
					nom_bac varchar(50),
					id_f int,
					constraint FK_fil_bac_bac foreign key(nom_bac) references baccalaureat(nom_bac) on delete cascade on update cascade,/*et on update cascade*/
					constraint FK_fil_bac_fil foreign key(id_f) references filiere(id_f) on delete cascade on update cascade,
					constraint PK_fil_bac primary key(nom_bac,id_f)
					);
/*
*/
create table nom_module( 
						id_m varchar(5),
						id_f int,
						cin_p varchar(20),
						nom_mod varchar(255) not null,
						constraint fk_nom_mod_mod foreign key(id_m) references modul(id_m) on delete cascade on update cascade,
						constraint fk_nom_mod_fil foreign key(id_f) references filiere(id_f) on delete cascade on update cascade,
						constraint fk_nom_mod_prof foreign key(cin_p) references professeur(cin_p) on delete set null on update cascade,
						constraint pk_nom_mod primary key(id_m,id_f)
						);

/*
-- Table : ADMINISTRATEUR  
*/
create table ADMINISTRATEUR
(
   NOM_AD               varchar(50) not null,
   PRENOM_AD            varchar(50) not null,
   dateN_ad             date not null,
   PWD_AD               varchar(50) not null,
   LOGIN_AD             varchar(100) not null,
   EMAIL_AD             varchar(100) not null,
   FONCTION_AD          varchar(50) not null,
   CIN_AD               varchar(20) not null,
   PHOTO_AD             longblob not null,
   sexe_ad              varchar(3) not null,
   adresse_ad              text not null,
   ntel_ad              varchar(15) not null,
   nationalite varchar(50) not null,
   id_f   int,
   constraint fk_adm_fil foreign key(id_f) references filiere(id_f) on delete set null on update cascade,
   constraint un_login_ad unique(login_ad),
   constraint ck_admin_sexe check(sexe_ad in('H','F')),
   primary key (CIN_AD)
);

/*
-- Table : Actualite  
*/
create table ACTUALITE(
                 id_acc int primary key  AUTO_INCREMENT,
				 titre_acc varchar(255) not null,
				 contenu_acc text not null,
				 image_acc  longblob,
				 date_acc date not null,
				 cin_p varchar(20),
				 id_m varchar(5),
				 id_f int,
				 cin_ad varchar(20),
				 constraint fk_acc_prof foreign key(cin_p) references professeur(cin_p) on delete set null on update cascade,
				 constraint fk_acc_fil foreign key(id_f) references filiere(id_f),
				 constraint fk_acc_mod foreign key(id_m) references modul(id_m),
				 constraint fk_acc_nm_mod foreign key(id_m,id_f) references nom_module(id_m,id_f) on delete set null on update cascade,
				 constraint fk_acc_admin foreign key(cin_ad) references administrateur(cin_ad) on delete set null on update cascade
				 );
/*					
--*******************************************************************
--*************** insertion des données statiques *******************
--*******************************************************************
*/
/*
insertions de données de semestre
*/
INSERT INTO semestre (nom_s) VALUES
('S1'),
('S2'),
('S3'),
('S4'),
('S5'),
('S6');
/*
insertions de données de baccalaureat
*/
INSERT INTO baccalaureat (nom_bac) VALUES
('AAP'),
('LA'),
('SEG'),
('SH'),
('SMA'),
('SMB'),
('SMPC'),
('STE'),
('STM'),
('SVT');
/*
insertions de données de diplome
*/
INSERT INTO diplome (nom_dip) VALUES
('DEUG'),
('DEUP'),
('Doctorat'),
('Licence'),
('Master');
/*
insertions de données de modul
*/
INSERT INTO modul (id_m,nom_s,id_eq) VALUES
('M01', 'S1', NULL),
('M02', 'S1', NULL),
('M03', 'S1', NULL),
('M04', 'S1', NULL),
('M05', 'S1', NULL),
('M06', 'S1', NULL),
('M07', 'S1', NULL),
('M08', 'S2', NULL),
('M09', 'S2', NULL),
('M10', 'S2', NULL),
('M11', 'S2', NULL),
('M12', 'S2', NULL),
('M13', 'S2', NULL),
('M14', 'S2', NULL),
('M15', 'S3', NULL),
('M16', 'S3', NULL),
('M17', 'S3', NULL),
('M18', 'S3', NULL),
('M19', 'S3', NULL),
('M20', 'S3', NULL),
('M21', 'S4', NULL),
('M22', 'S4', NULL),
('M23', 'S4', NULL),
('M24', 'S4', NULL),
('M25', 'S4', NULL),
('M26', 'S4', NULL),
('M27', 'S5', NULL),
('M28', 'S5', NULL),
('M29', 'S5', NULL),
('M30', 'S5', NULL),
('M31', 'S5', NULL),
('M32', 'S5', NULL),
('M33', 'S6', NULL),
('M34', 'S6', NULL),
('M35', 'S6', NULL),
('M36', 'S6', NULL),
('M37', 'S6', NULL),
('M38', 'S6', NULL);

update modul set id_eq='M15' where id_m='M01';
update modul set id_eq='M16' where id_m='M02';
update modul set id_eq='M17' where id_m='M03';
update modul set id_eq='M18' where id_m='M04';
update modul set id_eq='M19' where id_m='M05';
update modul set id_eq='M20' where id_m='M06';
update modul set id_eq='M21' where id_m='M08';
update modul set id_eq='M22' where id_m='M09';
update modul set id_eq='M23' where id_m='M10';
update modul set id_eq='M24' where id_m='M11';
update modul set id_eq='M25' where id_m='M12';
update modul set id_eq='M26' where id_m='M13';
update modul set id_eq='M27' where id_m='M15';
update modul set id_eq='M28' where id_m='M16';
update modul set id_eq='M29' where id_m='M17';
update modul set id_eq='M30' where id_m='M18';
update modul set id_eq='M31' where id_m='M19';
update modul set id_eq='M32' where id_m='M20';
update modul set id_eq='M33' where id_m='M21';
update modul set id_eq='M34' where id_m='M22';
update modul set id_eq='M35' where id_m='M23';
update modul set id_eq='M36' where id_m='M24';
update modul set id_eq='M37' where id_m='M25';
update modul set id_eq='M38' where id_m='M26';

/*
insertions de données de departement
*/
INSERT INTO departement (nom_d,cin_chef) VALUES
('Sc math info', NULL),
('sc matiére physique chimie', NULL),
('sc economie', NULL),
('droit', NULL),
('langue arabe', NULL),
('langue française', NULL),
('SVT', NULL),
('géographie', NULL),
('langue anglaise', NULL);

/*
insertions de données de filiere
*/
INSERT INTO filiere (nom_f, cin_cor, id_d) VALUES
('SMI', NULL, 1),
('SMA', NULL, 1),
('SMC', NULL, 2),
('SMP', NULL, 2),
('DP', NULL, 4),
('DG', NULL, 4),
('BIO', NULL, 7),
('GIO', NULL, 7);
/*
insertions de données de fil_bac
*/
INSERT INTO fil_bac (nom_bac,id_f) VALUES
('SMA', 1),
('SMB', 1),
('SMPC',1),
('STE', 1),
('STM', 1),
('SVT', 1),
('SMA', 2),
('SMB', 2),
('SMPC',2),
('STE', 2),
('STM', 2),
('SVT', 2),
('SMA', 3),
('SMB', 3),
('SMPC',3),
('STE', 3),
('STM', 3),
('SVT', 3),
('SMA', 4),
('SMB', 4),
('SMPC',4),
('STE', 4),
('STM', 4),
('SVT', 4),
('SMA', 5),
('SMB', 5),
('SMPC',5),
('STE', 5),
('STM', 5),
('SVT', 5),
('SMA', 6),
('SMB', 6),
('SMPC',6),
('STE', 6),
('STM', 6),
('SVT', 6),
('SMA', 7),
('SMB', 7),
('SMPC',7),
('STE', 7),
('STM', 7),
('SVT', 7),
('SMA', 8),
('SMB', 8),
('SMPC',8),
('STE', 8),
('STM', 8),
('SVT', 8);
