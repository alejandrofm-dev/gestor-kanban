CREATE DATABASE IF NOT EXISTS agenda_tareas;
USE agenda_tareas;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('owner','admin','user') DEFAULT 'user',
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de tareas
CREATE TABLE IF NOT EXISTS tasks(
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    due_date DATE,
    priority ENUM('high','medium','low') DEFAULT 'medium',
    status ENUM('pending','in_progress','completed') DEFAULT 'pending',
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabla de etiquetas (tags)
CREATE TABLE IF NOT EXISTS tags(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    color VARCHAR(7) DEFAULT '#6c757d', -- Opcional: código hex para el color del tag
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla intermedia (relación N:M entre tasks y tags)
CREATE TABLE IF NOT EXISTS task_tags(
    task_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (task_id, tag_id),
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- Inserción datos iniciales para facilitar las pruebas
INSERT INTO users (username, email, password, rol) VALUES 
('admin', 'admin@gmail.com', '21232f297a57a5a743894a0e4a801fc3', 'admin'),
('alex', 'alex@gmail.com', '534b44a19bf18d20b71ecc4eb77c572f', 'user');

INSERT INTO tasks (title, description, due_date, priority, status, user_id) VALUES 
('Tarea 1', 'Tarea 1 descripcion', '2026-12-31', 'high', 'pending', 1),
('Tarea 2', 'Tarea 2 descripcion', '2026-12-31', 'medium', 'in_progress', 1),
('Task 3', 'Task 3 description', '2026-12-31', 'low', 'completed', 1);

-- Inserción de etiquetas de prueba
INSERT INTO tags (name, color) VALUES 
('Urgente', '#ff0000'),
('Trabajo', '#007bff'),
('Personal', '#28a745');

-- Asignación de etiquetas a las tareas (Tabla intermedia)
INSERT INTO task_tags (task_id, tag_id) VALUES 
(1, 1), -- Tarea 1 -> Urgente
(1, 2), -- Tarea 1 -> Trabajo
(2, 2), -- Tarea 2 -> Trabajo
(3, 3); -- Task 3  -> Personal