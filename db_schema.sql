CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password_hash` text NOT NULL,
  `role_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `status` enum('active','disabled') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `role_id` (`role_id`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `user_ibfk_1`
    FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`),
  CONSTRAINT `user_ibfk_2`
    FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) NOT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `department` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(100) NOT NULL,
  PRIMARY KEY (`department_id`),
  UNIQUE KEY `department_name` (`department_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `task` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `department_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`task_id`),
  KEY `department_id` (`department_id`),
  KEY `fk_task_creator` (`created_by`),
  CONSTRAINT `fk_task_creator`
    FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `task_ibfk_1`
    FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `task_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `report_file` varchar(255) DEFAULT NULL,
  `report_uploaded_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `user_id` (`user_id`),
  KEY `fk_task_assignments_reviewed_by` (`reviewed_by`),
  CONSTRAINT `task_assignments_ibfk_1`
    FOREIGN KEY (`task_id`) REFERENCES `task` (`task_id`) ON DELETE CASCADE,
  CONSTRAINT `task_assignments_ibfk_2`
    FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_task_assignments_reviewed_by`
    FOREIGN KEY (`reviewed_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user_skill` (
  `skill_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `skill_description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`skill_id`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `user_skill_ibfk_1`
    FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `user_skill_ibfk_2`
    FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `activity_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(100) NOT NULL,
  `action_description` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `activity_log_ibfk_1`
    FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `activity_log_ibfk_2`
    FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;