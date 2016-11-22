-- MySQL Script generated by MySQL Workbench
-- 11/22/16 16:19:59
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema ccnt
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `ccnt` ;

-- -----------------------------------------------------
-- Schema ccnt
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `ccnt` DEFAULT CHARACTER SET utf8 ;
USE `ccnt` ;

-- -----------------------------------------------------
-- Table `ccnt`.`ccn_etablissement`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ccnt`.`ccn_etablissement` ;

CREATE TABLE IF NOT EXISTS `ccnt`.`ccn_etablissement` (
  `eta_id` INT NOT NULL AUTO_INCREMENT,
  `eta_nom` VARCHAR(100) NULL,
  `eta_adresse` VARCHAR(100) NULL,
  `eta_telReservation` VARCHAR(20) NULL,
  `eta_telDirection` VARCHAR(20) NULL,
  `eta_email` VARCHAR(100) NULL,
  `eta_siteWeb` VARCHAR(100) NULL,
  `eta_adresseInfo` VARCHAR(100) NULL,
  `eta_codePostal` INT NULL,
  `eta_localite` VARCHAR(100) NULL,
  `eta_nbHeure` INT NULL,
  PRIMARY KEY (`eta_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ccnt`.`ccn_departement`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ccnt`.`ccn_departement` ;

CREATE TABLE IF NOT EXISTS `ccnt`.`ccn_departement` (
  `dep_id` INT NOT NULL AUTO_INCREMENT,
  `dep_nom` VARCHAR(45) NULL,
  `dep_eta_id` INT NOT NULL,
  PRIMARY KEY (`dep_id`, `dep_eta_id`),
  INDEX `fk_Departement_Etablissement1_idx` (`dep_eta_id` ASC),
  CONSTRAINT `fk_Departement_Etablissement1`
    FOREIGN KEY (`dep_eta_id`)
    REFERENCES `ccnt`.`ccn_etablissement` (`eta_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ccnt`.`ccn_personne`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ccnt`.`ccn_personne` ;

CREATE TABLE IF NOT EXISTS `ccnt`.`ccn_personne` (
  `per_id` INT NOT NULL AUTO_INCREMENT,
  `per_nom` VARCHAR(50) NULL,
  `per_prenom` VARCHAR(50) NULL,
  `per_mail` VARCHAR(100) NULL,
  `per_mdp` VARCHAR(128) NULL,
  `per_token` VARCHAR(128) NULL,
  `per_dateNaissance` DATE NULL,
  `per_adresse` VARCHAR(100) NULL,
  `per_InfoSuppAdresse` VARCHAR(100) NULL,
  `per_codePostal` INT NULL,
  `per_ville` VARCHAR(50) NULL,
  `per_admin` TINYINT(1) NULL,
  `per_telFixe` VARCHAR(20) NULL,
  `per_telMobile` VARCHAR(20) NULL,
  `per_dep_id` INT NOT NULL,
  PRIMARY KEY (`per_id`, `per_dep_id`),
  INDEX `fk_Personne_Departement1_idx` (`per_dep_id` ASC),
  CONSTRAINT `fk_Personne_Departement1`
    FOREIGN KEY (`per_dep_id`)
    REFERENCES `ccnt`.`ccn_departement` (`dep_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ccnt`.`ccn_appartient`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ccnt`.`ccn_appartient` ;

CREATE TABLE IF NOT EXISTS `ccnt`.`ccn_appartient` (
  `app_eta_id` INT NOT NULL,
  `app_per_id` INT NOT NULL,
  PRIMARY KEY (`app_eta_id`, `app_per_id`),
  INDEX `fk_Etablissement_has_Personne_Personne1_idx` (`app_per_id` ASC),
  INDEX `fk_Etablissement_has_Personne_Etablissement_idx` (`app_eta_id` ASC),
  CONSTRAINT `fk_Etablissement_has_Personne_Etablissement`
    FOREIGN KEY (`app_eta_id`)
    REFERENCES `ccnt`.`ccn_etablissement` (`eta_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Etablissement_has_Personne_Personne1`
    FOREIGN KEY (`app_per_id`)
    REFERENCES `ccnt`.`ccn_personne` (`per_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ccnt`.`ccn_OuvertureInfo`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ccnt`.`ccn_OuvertureInfo` ;

CREATE TABLE IF NOT EXISTS `ccnt`.`ccn_OuvertureInfo` (
  `ouv_id` INT NOT NULL AUTO_INCREMENT,
  `ouv_jour` VARCHAR(20) NULL,
  `ouv_debut` DATETIME NULL,
  `ouv_fin` DATETIME NULL,
  `ouv_eta_id` INT NOT NULL,
  PRIMARY KEY (`ouv_id`, `ouv_eta_id`),
  INDEX `fk_InfoOuverture_Etablissement1_idx` (`ouv_eta_id` ASC),
  CONSTRAINT `fk_InfoOuverture_Etablissement1`
    FOREIGN KEY (`ouv_eta_id`)
    REFERENCES `ccnt`.`ccn_etablissement` (`eta_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ccnt`.`ccn_FermetureInfo`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ccnt`.`ccn_FermetureInfo` ;

CREATE TABLE IF NOT EXISTS `ccnt`.`ccn_FermetureInfo` (
  `fer_id` INT NOT NULL AUTO_INCREMENT,
  `fer_date` DATE NULL,
  `fer_Eta_id` INT NOT NULL,
  PRIMARY KEY (`fer_id`, `fer_Eta_id`),
  INDEX `fk_InfoFermeture_Etablissement1_idx` (`fer_Eta_id` ASC),
  CONSTRAINT `fk_InfoFermeture_Etablissement1`
    FOREIGN KEY (`fer_Eta_id`)
    REFERENCES `ccnt`.`ccn_etablissement` (`eta_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
