# BFLP IT Bootcamp

Christhoper Marcelino Mamahit  
BFLP IT 24

#### Script DDL
```
CREATE DATABASE `bflp_bootcamp`;

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL,
  `availability` varchar(100) NOT NULL,
  `age` int NOT NULL,
  `location` varchar(100) NOT NULL,
  `years_experience` int NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
);
```