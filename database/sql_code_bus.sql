-- MySQL Script generated by MySQL Workbench
-- 06/23/16 14:58:53
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema bus_tracker
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `bus_tracker` ;

-- -----------------------------------------------------
-- Schema bus_tracker
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `bus_tracker` DEFAULT CHARACTER SET utf8 ;
USE `bus_tracker` ;

-- -----------------------------------------------------
-- Table `bus_tracker`.`routes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bus_tracker`.`routes` ;

CREATE TABLE IF NOT EXISTS `bus_tracker`.`routes` (
  `id_routes` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(60) NOT NULL,
  `description` VARCHAR(512) NOT NULL,
  PRIMARY KEY (`id_routes`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bus_tracker`.`bus`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bus_tracker`.`bus` ;

CREATE TABLE IF NOT EXISTS `bus_tracker`.`bus` (
  `id_bus` INT NOT NULL AUTO_INCREMENT,
  `velocity` DOUBLE NOT NULL,
  `id_routes` INT NOT NULL,
  PRIMARY KEY (`id_bus`),
  INDEX `fk_bus_routes1_idx` (`id_routes` ASC),
  CONSTRAINT `fk_bus_routes1`
    FOREIGN KEY (`id_routes`)
    REFERENCES `bus_tracker`.`routes` (`id_routes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bus_tracker`.`schedules_route`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bus_tracker`.`schedules_route` ;

CREATE TABLE IF NOT EXISTS `bus_tracker`.`schedules_route` (
  `id_routes` INT NOT NULL,
  `inicio` VARCHAR(5) NOT NULL,
  `fim` VARCHAR(5) NOT NULL,
  `dias` VARCHAR(7) NOT NULL,
  PRIMARY KEY (`id_routes`, `inicio`, `fim`, `dias`),
  CONSTRAINT `fk_schedules_route_routes`
    FOREIGN KEY (`id_routes`)
    REFERENCES `bus_tracker`.`routes` (`id_routes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bus_tracker`.`points_route`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bus_tracker`.`points_route` ;

CREATE TABLE IF NOT EXISTS `bus_tracker`.`points_route` (
  `id_routes` INT NOT NULL,
  `latitude` DOUBLE NOT NULL,
  `longitude` DOUBLE NOT NULL,
  PRIMARY KEY (`id_routes`, `latitude`, `longitude`),
  CONSTRAINT `fk_points_route_routes1`
    FOREIGN KEY (`id_routes`)
    REFERENCES `bus_tracker`.`routes` (`id_routes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bus_tracker`.`last_localizations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bus_tracker`.`last_localizations` ;

CREATE TABLE IF NOT EXISTS `bus_tracker`.`last_localizations` (
  `id_bus` INT NOT NULL,
  `data` DATETIME NOT NULL,
  `latitude` DOUBLE NOT NULL,
  `longitude` DOUBLE NOT NULL,
  PRIMARY KEY (`id_bus`, `data`, `latitude`, `longitude`),
  INDEX `fk_last_localizations_bus1_idx` (`id_bus` ASC),
  CONSTRAINT `fk_last_localizations_bus1`
    FOREIGN KEY (`id_bus`)
    REFERENCES `bus_tracker`.`bus` (`id_bus`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bus_tracker`.`notifications`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bus_tracker`.`notifications` ;

CREATE TABLE IF NOT EXISTS `bus_tracker`.`notifications` (
  `id_routes` INT NOT NULL AUTO_INCREMENT,
  `id_bus` INT NOT NULL,
  `title` VARCHAR(45) NOT NULL,
  `message` VARCHAR(126) NULL,
  `data` DATETIME NULL,
  PRIMARY KEY (`id_routes`),
  INDEX `fk_notifications_bus1_idx` (`id_bus` ASC),
  CONSTRAINT `fk_notifications_bus1`
    FOREIGN KEY (`id_bus`)
    REFERENCES `bus_tracker`.`bus` (`id_bus`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bus_tracker`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bus_tracker`.`users` ;

CREATE TABLE IF NOT EXISTS `bus_tracker`.`users` (
  `id_users` INT NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id_users`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `bus_tracker`.`config_users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `bus_tracker`.`config_users` ;

CREATE TABLE IF NOT EXISTS `bus_tracker`.`config_users` (
  `id_users` INT NOT NULL,
  `type` INT NOT NULL,
  `config_userscol` VARCHAR(500) NOT NULL,
  PRIMARY KEY (`id_users`),
  CONSTRAINT `fk_config_users_users1`
    FOREIGN KEY (`id_users`)
    REFERENCES `bus_tracker`.`users` (`id_users`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
