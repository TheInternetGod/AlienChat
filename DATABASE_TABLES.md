#Create your database and execute the following SQL queries to create the necessary tables.

-- Create Users table
CREATE TABLE IF NOT EXISTS Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(60) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Messages table
CREATE TABLE IF NOT EXISTS Messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    message TEXT,
    sender_id INT NOT NULL,
    file_id INT NULL,
    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES Users(id),
    FOREIGN KEY (file_id) REFERENCES Files(id)
);

-- Create Files table
CREATE TABLE IF NOT EXISTS Files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    files VARCHAR(255) NOT NULL,
    users_id INT NOT NULL,
    time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (users_id) REFERENCES Users(id)
);