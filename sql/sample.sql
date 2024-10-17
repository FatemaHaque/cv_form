CREATE TABLE users(
    Id int PRIMARY KEY AUTO_INCREMENT,
    Username varchar(200),
    Email varchar(200),
    Age int,
    Password varchar(200)
);

CREATE TABLE `cv_uploads` (
   Id int(11) PRIMARY KEY AUTO_INCREMENT,
   Username  varchar(200),
   cv_name varchar(255) NOT NULL,
   cv_path varchar(255) NOT NULL
  
  
);
