CREATE TABLE ItemDictionary(
    TypeI BIGINT PRIMARY KEY AUTO_INCREMENT,
    IName TEXT
);

CREATE TABLE Items(
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    TypeI BIGINT,
    INumber BIGINT NOT NULL,
    IYear BIGINT,
    IState TINYINT,
    UNIQUE (INumber),
    CHECK (IYear >= 2000),
    FOREIGN KEY (TypeI) REFERENCES ItemDictionary(TypeI)
);

CREATE TABLE LocationDictionary(
    TypeL BIGINT PRIMARY KEY AUTO_INCREMENT,
    LName TEXT
);

CREATE TABLE Locations(
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    TypeL BIGINT,
    LCS BIGINT,
    FCS VARCHAR(255),
    LClass BIGINT,
    LPosition BIGINT,
    LDescription LONGTEXT,
    FOREIGN KEY (TypeL) REFERENCES LocationDictionary(TypeL)
);

CREATE TABLE History(
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    ItemID BIGINT NOT NULL,
    LocationID BIGINT NOT NULL,
    FirstSeen DATE,
    LastSeen DATE,
    HIndex TINYINT,
    FOREIGN KEY (ItemID) REFERENCES Items(id),
    FOREIGN KEY (LocationID) REFERENCES Locations(id)
);

CREATE TABLE MaintenanceLog(
    id INT PRIMARY KEY AUTO_INCREMENT,
    ItemID BIGINT NOT NULL,
    LocationID BIGINT NOT NULL,
    FailureDate DATE,
    FailureCode BIGINT,
    FailureDesc LONGTEXT,
    RepairDesc LONGTEXT,
    FOREIGN KEY (ItemID) REFERENCES Items(id),
    FOREIGN KEY (LocationID) REFERENCES Locations(id)
);