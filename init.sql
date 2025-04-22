-- init.sql
CREATE DATABASE vector_db;

-- Use psql's -c flag equivalent
\! psql -U postgres -d School_AI_Assistant -c "CREATE EXTENSION IF NOT EXISTS vector;"

-- Continue with user setup
-- CREATE USER pgvector_user WITH PASSWORD 'your_password';