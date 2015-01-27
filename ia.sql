-- -----------------------------------------------------
-- Table `mydb`.`verbos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`verbos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `palavra` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`adjetivos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`adjetivos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `palavra` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`pronomes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`pronomes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `palavra` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`substantivos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`substantivos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `palavra` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`pronomes_adjetivos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`pronomes_adjetivos` (
  `pronome_id` INT UNSIGNED NOT NULL,
  `adjetivo_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`pronome_id`, `adjetivo_id`),
  INDEX `fk_pronomes_has_adjetivos_adjetivos1_idx` (`adjetivo_id` ASC),
  INDEX `fk_pronomes_has_adjetivos_pronomes_idx` (`pronome_id` ASC),
  CONSTRAINT `fk_pronomes_has_adjetivos_pronomes`
    FOREIGN KEY (`pronome_id`)
    REFERENCES `mydb`.`pronomes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_pronomes_has_adjetivos_adjetivos1`
    FOREIGN KEY (`adjetivo_id`)
    REFERENCES `mydb`.`adjetivos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`palavras`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`palavras` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `palavra` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`frases`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`frases` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `frase` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`verbos_frases`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`verbos_frases` (
  `verbo_id` INT UNSIGNED NOT NULL,
  `frase_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`verbo_id`, `frase_id`),
  INDEX `fk_verbos_has_frases_frases1_idx` (`frase_id` ASC),
  INDEX `fk_verbos_has_frases_verbos1_idx` (`verbo_id` ASC),
  CONSTRAINT `fk_verbos_has_frases_verbos1`
    FOREIGN KEY (`verbo_id`)
    REFERENCES `mydb`.`verbos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_verbos_has_frases_frases1`
    FOREIGN KEY (`frase_id`)
    REFERENCES `mydb`.`frases` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`frases_palavras`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`frases_palavras` (
  `frase_id` INT UNSIGNED NOT NULL,
  `palavra_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`frase_id`, `palavra_id`),
  INDEX `fk_frases_has_palavras_palavras1_idx` (`palavra_id` ASC),
  INDEX `fk_frases_has_palavras_frases1_idx` (`frase_id` ASC),
  CONSTRAINT `fk_frases_has_palavras_frases1`
    FOREIGN KEY (`frase_id`)
    REFERENCES `mydb`.`frases` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_frases_has_palavras_palavras1`
    FOREIGN KEY (`palavra_id`)
    REFERENCES `mydb`.`palavras` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`frases_substantivos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`frases_substantivos` (
  `frase_id` INT UNSIGNED NOT NULL,
  `substantivo_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`frase_id`, `substantivo_id`),
  INDEX `fk_frases_has_substantivos_substantivos1_idx` (`substantivo_id` ASC),
  INDEX `fk_frases_has_substantivos_frases1_idx` (`frase_id` ASC),
  CONSTRAINT `fk_frases_has_substantivos_frases1`
    FOREIGN KEY (`frase_id`)
    REFERENCES `mydb`.`frases` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_frases_has_substantivos_substantivos1`
    FOREIGN KEY (`substantivo_id`)
    REFERENCES `mydb`.`substantivos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`frases_adjetivos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`frases_adjetivos` (
  `frase_id` INT UNSIGNED NOT NULL,
  `adjetivo_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`frase_id`, `adjetivo_id`),
  INDEX `fk_frases_has_adjetivos_adjetivos1_idx` (`adjetivo_id` ASC),
  INDEX `fk_frases_has_adjetivos_frases1_idx` (`frase_id` ASC),
  CONSTRAINT `fk_frases_has_adjetivos_frases1`
    FOREIGN KEY (`frase_id`)
    REFERENCES `mydb`.`frases` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_frases_has_adjetivos_adjetivos1`
    FOREIGN KEY (`adjetivo_id`)
    REFERENCES `mydb`.`adjetivos` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`frases_pronomes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`frases_pronomes` (
  `frase_id` INT UNSIGNED NOT NULL,
  `pronome_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`frase_id`, `pronome_id`),
  INDEX `fk_frases_has_pronomes_pronomes1_idx` (`pronome_id` ASC),
  INDEX `fk_frases_has_pronomes_frases1_idx` (`frase_id` ASC),
  CONSTRAINT `fk_frases_has_pronomes_frases1`
    FOREIGN KEY (`frase_id`)
    REFERENCES `mydb`.`frases` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_frases_has_pronomes_pronomes1`
    FOREIGN KEY (`pronome_id`)
    REFERENCES `mydb`.`pronomes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`frases_respostas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`frases_respostas` (
  `frase_id` INT UNSIGNED NOT NULL,
  `frase_resposta_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`frase_id`, `frase_resposta_id`),
  INDEX `fk_frases_has_frases_frases2_idx` (`frase_resposta_id` ASC),
  INDEX `fk_frases_has_frases_frases1_idx` (`frase_id` ASC),
  CONSTRAINT `fk_frases_has_frases_frases1`
    FOREIGN KEY (`frase_id`)
    REFERENCES `mydb`.`frases` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_frases_has_frases_frases2`
    FOREIGN KEY (`frase_resposta_id`)
    REFERENCES `mydb`.`frases` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;