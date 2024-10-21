DROP DATABASE IF EXISTS Agora;
CREATE DATABASE Agora;
USE Agora;

CREATE TABLE Business
(
BusinessID int AUTO_INCREMENT NOT NULL,
BusinessName VARCHAR(50) NOT NULL,
LegalBusinessDetails VARCHAR(100) NOT NULL,
HQLocation VARCHAR (50) NOT NULL,
AdditionalLocations VARCHAR(50),
PRIMARY KEY (BusinessID)
) engine = InnoDB;

CREATE TABLE Users
(
UserID int AUTO_INCREMENT NOT NULL,
UserName VARCHAR(20) NOT NULL,
Email VARCHAR(30) NOT NULL,
Password VARCHAR(255) NOT NULL,
Role VARCHAR(30) NOT NULL,
PRIMARY KEY (UserID)
) engine = InnoDB;

CREATE TABLE Users_Business
(
UserID INT NOT NULL,
BusinessID INT NOT NULL,
Role VARCHAR(30) NOT NULL,
FOREIGN KEY (UserID) REFERENCES Users(UserID),
FOREIGN KEY (BusinessID) REFERENCES Business(BusinessID),
PRIMARY KEY (UserID, BusinessID)
) engine = InnoDB;

CREATE TABLE BusinessAccountAdministrators
(
UserID INT NOT NULL,
HQLocation VARCHAR(50) NOT NULL,
LegalBusinessDetails VARCHAR(100) NOT NULL,
FOREIGN KEY (UserID) REFERENCES Users(UserID),
PRIMARY KEY (UserID)
) engine = InnoDB;

CREATE TABLE Sellers
(
UserID INT NOT NULL,
Location VARCHAR(100) NOT NULL,
FOREIGN KEY (UserID) REFERENCES Users(UserID),
PRIMARY KEY (UserID)
) engine = InnoDB;

CREATE TABLE MasterAdmin
(
UserID INT NOT NULL,
FOREIGN KEY (UserID) REFERENCES Users(UserID),
PRIMARY KEY (UserID)
) engine = InnoDB;

CREATE TABLE Buyers
(
UserID INT NOT NULL,
FOREIGN KEY (UserID) REFERENCES Users(UserID),
PRIMARY KEY (UserID)
) engine = InnoDB;

CREATE TABLE Items
(
ItemID INT AUTO_INCREMENT NOT NULL,
ItemName VARCHAR(20) NOT NULL,
Description VARCHAR(100) NOT NULL,
Price DECIMAL(5, 2),
SellerID INT NOT NULL,
FOREIGN KEY (SellerID) REFERENCES Sellers(UserID),
PRIMARY KEY (ItemID)
) engine = InnoDB;

CREATE TABLE Buyers_Items
(
BuyerID INT NOT NULL,
ItemID INT NOT NULL,
FOREIGN KEY (BuyerID) REFERENCES Buyers(UserID),
FOREIGN KEY (ItemID) REFERENCES Items(ItemID),
PRIMARY KEY (BuyerID, ItemID)
) engine = InnoDB;

-- Insert data into Business table
INSERT INTO Business (BusinessName, LegalBusinessDetails, HQLocation, AdditionalLocations)
VALUES
('Tech Solutions', '1234567890', 'New York', 'Los Angeles'),
('Global Traders', '0987654321', 'London', 'Paris'),
('Innovative Designs', '1122334455', 'Tokyo', 'Osaka'),
('Creative Solutions', '2233445566', 'San Francisco', 'Seattle'),
('Global Innovations', '3344556677', 'Berlin', 'Munich'),
('Tech Pioneers', '4455667788', 'Sydney', 'Melbourne');

-- Insert data into Users table
INSERT INTO Users (UserName, Email, Password, Role)
VALUES
('Alice', 'alice@example.com', '123', 'Buyer'),
('Bob', 'bob@example.com', '456', 'Seller'),
('Charlie', 'charlie@example.com', '234', 'Business Account Administrator'),
('David', 'david@example.com', '345', 'Master Admin'),
('Eve', 'eve@example.com', '567', 'Buyer'),
('Frank', 'frank@example.com', '678', 'Seller'),
('Grace', 'grace@example.com', '789', 'Business Account Administrator'),
('Hank', 'hank@example.com', '890', 'Master Admin'),
('Ivy', 'ivy@example.com', '001', 'Buyer'),
('Jack', 'jack@example.com', '011', 'Seller');

-- Insert data into Users_Business table
INSERT INTO Users_Business (UserID, BusinessID, Role)
VALUES
(1, 1, 'Buyer'),
(2, 2, 'Seller'),
(3, 3, 'Business Account Administrator'),
(5, 4, 'Buyer'),
(6, 5, 'Seller'),
(7, 6, 'Business Account Administrator'),
(8, 1, 'Master Admin'),
(9, 2, 'Buyer'),
(10, 3, 'Seller');

-- Insert data into BusinessAccountAdministrators table
INSERT INTO BusinessAccountAdministrators (UserID, HQLocation, LegalBusinessDetails)
VALUES
(3, 'Tokyo', '1122334455'),
(7, 'Berlin', '3344556677');

-- Insert data into Sellers table
INSERT INTO Sellers (UserID, Location)
VALUES
(2, 'London'),
(6, 'Munich'),
(10, 'Melbourne');

-- Insert data into MasterAdmin table
INSERT INTO MasterAdmin (UserID)
VALUES
(4),
(8);

-- Insert data into Buyers table
INSERT INTO Buyers (UserID)
VALUES
(1),
(5),
(9);

-- Insert data into Items table
INSERT INTO Items (ItemName, Description, Price, SellerID)
VALUES
('Product A', 'High-quality product', 100.00, 2),
('Product B', 'Affordable product', 50.00, 2),
('Product C', 'Premium product', 150.00, 2),
('Product D', 'Eco-friendly product', 75.00, 6),
('Product E', 'Luxury product', 200.00, 10),
('Product F', 'Budget product', 25.00, 6),
('Product G', 'Innovative product', 120.00, 10);

-- Insert data into Buyers_Items table
INSERT INTO Buyers_Items (BuyerID, ItemID)
VALUES
(1, 1),
(1, 2),
(5, 3),
(5, 4),
(9, 5),
(9, 6);

SELECT * FROM Business;
SELECT * FROM Users;
SELECT * FROM Users_Business;
SELECT * FROM BusinessAccountAdministrators;
SELECT * FROM Sellers;
SELECT * FROM MasterAdmin;
SELECT * FROM Buyers;
SELECT * FROM Items;
SELECT * FROM Buyers_Items;

-- Update Query 
-- Update the email of a user in the Users table
UPDATE Users
SET Email = 'newemail@example.com'
WHERE UserID = 1;

-- Simple Query
-- Retrieve all items listed by a specific seller
SELECT ItemName, Description, Price
FROM Items
WHERE SellerID = 10;

-- Complex Query
-- Retrieve a list of buyers with the total amount spent by each buyer, sorted by the buyer’s name
SELECT Buyers.UserID, Users.UserName, SUM(Items.Price) AS TotalSpent
FROM Buyers
JOIN Users ON Buyers.UserID = Users.UserID
JOIN Buyers_Items ON Buyers.UserID = Buyers_Items.BuyerID
JOIN Items ON Buyers_Items.ItemID = Items.ItemID
GROUP BY Buyers.UserID, Users.UserName
ORDER BY Users.UserName;


-- Search Query
-- Search for items with a specific keyword in their name or description
-- user searches for "Luxury Handbags"
SELECT ItemName, Description, Price
FROM Items
WHERE ItemName LIKE '%luxury%'
OR ItemName LIKE '%handbag%'
OR Description LIKE '%luxury%'
OR Description LIKE '%handbag%'
ORDER BY Price DESC;

