CREATE DATABASE IF NOT EXISTS mentari
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE mentari;

CREATE TABLE schools (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    code VARCHAR(50) NULL,
    address VARCHAR(255) NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY schools_code_unique (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP NULL DEFAULT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'counselor', 'admin') NOT NULL DEFAULT 'student',
    level VARCHAR(50) NULL,
    avatar_initial CHAR(1) NULL,
    streak_days INT UNSIGNED NOT NULL DEFAULT 0,
    last_activity_date DATE NULL,
    can_take_screening BOOLEAN NOT NULL DEFAULT TRUE,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY users_email_unique (email),
    KEY users_school_id_foreign (school_id),
    CONSTRAINT users_school_id_foreign
        FOREIGN KEY (school_id) REFERENCES schools (id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE mood_options (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(50) NOT NULL,
    emoji VARCHAR(16) NOT NULL,
    label VARCHAR(80) NOT NULL,
    description VARCHAR(255) NULL,
    color CHAR(10) NOT NULL,
    score TINYINT UNSIGNED NOT NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY mood_options_key_unique (`key`),
    KEY mood_options_active_sort_index (is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE mood_entries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    mood_option_id BIGINT UNSIGNED NOT NULL,
    entry_date DATE NOT NULL,
    note TEXT NULL,
    energy TINYINT UNSIGNED NOT NULL,
    stress TINYINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY mood_entries_user_date_unique (user_id, entry_date),
    KEY mood_entries_user_date_index (user_id, entry_date),
    KEY mood_entries_mood_option_id_foreign (mood_option_id),
    CONSTRAINT mood_entries_user_id_foreign
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE,
    CONSTRAINT mood_entries_mood_option_id_foreign
        FOREIGN KEY (mood_option_id) REFERENCES mood_options (id)
        ON DELETE RESTRICT,
    CONSTRAINT mood_entries_energy_check CHECK (energy BETWEEN 0 AND 10),
    CONSTRAINT mood_entries_stress_check CHECK (stress BETWEEN 0 AND 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE education_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(80) NOT NULL,
    title VARCHAR(120) NOT NULL,
    description VARCHAR(255) NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY education_categories_slug_unique (slug),
    KEY education_categories_active_sort_index (is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE education_contents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    education_category_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    type ENUM('article', 'infographic', 'video') NOT NULL,
    read_time_minutes SMALLINT UNSIGNED NULL,
    read_time_label VARCHAR(50) NULL,
    summary TEXT NOT NULL,
    body LONGTEXT NULL,
    media_url VARCHAR(255) NULL,
    accent_color CHAR(10) NULL,
    published_at TIMESTAMP NULL DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    KEY education_contents_category_active_index (education_category_id, is_active),
    KEY education_contents_published_active_index (published_at, is_active),
    CONSTRAINT education_contents_category_id_foreign
        FOREIGN KEY (education_category_id) REFERENCES education_categories (id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE screening_questions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    number SMALLINT UNSIGNED NOT NULL,
    scale ENUM('depression', 'anxiety', 'stress') NOT NULL,
    text TEXT NOT NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY screening_questions_number_unique (number),
    KEY screening_questions_scale_active_index (scale, is_active),
    KEY screening_questions_active_sort_index (is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE recommendations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    severity ENUM('normal', 'mild', 'moderate', 'severe', 'extremely_severe') NULL,
    description TEXT NOT NULL,
    duration_minutes SMALLINT UNSIGNED NULL,
    duration_label VARCHAR(50) NULL,
    priority VARCHAR(50) NULL,
    accent_color CHAR(10) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    KEY recommendations_category_active_index (category, is_active),
    KEY recommendations_category_severity_is_active_index (category, severity, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE screening_results (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    taken_at TIMESTAMP NOT NULL,
    depression_score SMALLINT UNSIGNED NOT NULL,
    depression_severity ENUM('normal', 'mild', 'moderate', 'severe', 'extremely_severe') NOT NULL,
    anxiety_score SMALLINT UNSIGNED NOT NULL,
    anxiety_severity ENUM('normal', 'mild', 'moderate', 'severe', 'extremely_severe') NOT NULL,
    stress_score SMALLINT UNSIGNED NOT NULL,
    stress_severity ENUM('normal', 'mild', 'moderate', 'severe', 'extremely_severe') NOT NULL,
    summary TEXT NOT NULL,
    recommendation_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    KEY screening_results_user_taken_index (user_id, taken_at),
    KEY screening_results_recommendation_id_foreign (recommendation_id),
    CONSTRAINT screening_results_user_id_foreign
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE,
    CONSTRAINT screening_results_recommendation_id_foreign
        FOREIGN KEY (recommendation_id) REFERENCES recommendations (id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE screening_answers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    screening_result_id BIGINT UNSIGNED NOT NULL,
    screening_question_id BIGINT UNSIGNED NOT NULL,
    score TINYINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY screening_answers_result_question_unique (screening_result_id, screening_question_id),
    KEY screening_answers_question_id_foreign (screening_question_id),
    CONSTRAINT screening_answers_result_id_foreign
        FOREIGN KEY (screening_result_id) REFERENCES screening_results (id)
        ON DELETE CASCADE,
    CONSTRAINT screening_answers_question_id_foreign
        FOREIGN KEY (screening_question_id) REFERENCES screening_questions (id)
        ON DELETE RESTRICT,
    CONSTRAINT screening_answers_score_check CHECK (score BETWEEN 0 AND 3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE community_posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    school_id BIGINT UNSIGNED NULL,
    tag VARCHAR(80) NULL,
    content TEXT NOT NULL,
    is_pinned TINYINT(1) NOT NULL DEFAULT 0,
    likes_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    KEY community_posts_school_pinned_created_index (school_id, is_pinned, created_at),
    KEY community_posts_user_created_index (user_id, created_at),
    CONSTRAINT community_posts_user_id_foreign
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE,
    CONSTRAINT community_posts_school_id_foreign
        FOREIGN KEY (school_id) REFERENCES schools (id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE community_post_likes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    community_post_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY community_post_likes_post_user_unique (community_post_id, user_id),
    KEY community_post_likes_user_created_index (user_id, created_at),
    CONSTRAINT community_post_likes_post_id_foreign
        FOREIGN KEY (community_post_id) REFERENCES community_posts (id)
        ON DELETE CASCADE,
    CONSTRAINT community_post_likes_user_id_foreign
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE risk_alerts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    screening_result_id BIGINT UNSIGNED NULL,
    level ENUM('stable', 'attention', 'urgent') NOT NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    recommendation TEXT NOT NULL,
    dismissed_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    KEY risk_alerts_user_level_created_index (user_id, level, created_at),
    KEY risk_alerts_dismissed_index (dismissed_at),
    KEY risk_alerts_screening_result_id_foreign (screening_result_id),
    CONSTRAINT risk_alerts_user_id_foreign
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE,
    CONSTRAINT risk_alerts_screening_result_id_foreign
        FOREIGN KEY (screening_result_id) REFERENCES screening_results (id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
