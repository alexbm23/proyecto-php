CREATE TABLE summoner (
    puuid VARCHAR(255) PRIMARY KEY, -- Clave primaria única para identificar al summoner
    username VARCHAR(100) NOT NULL, -- Nombre del summoner
    tag VARCHAR(10) NOT NULL,       -- Etiqueta del summoner
    foto VARCHAR(255),              -- URL de la foto del summoner
    idUser INT NOT NULL,            -- Relación con el id de la tabla users
    FOREIGN KEY (idUser) REFERENCES users(id) ON DELETE CASCADE
);