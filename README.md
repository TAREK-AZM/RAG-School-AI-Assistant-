# School_ai_assistant
this repository is and RAG ai assistent that will help the student to chat with it to find the most relevent answers about thiers shool


# School AI Assistant 🎓🤖

![Project Banner](https://via.placeholder.com/1200x400/3b82f6/ffffff?text=School+AI+Assistant) <!-- Replace with your actual banner image -->

## 🌟 Project Overview
A smart document management system that uses AI to:
- Process and analyze educational documents
- Enable semantic search through text embeddings
- Provide intelligent document recommendations
- Automate content organization

## ✨ Key Features
| Feature | Description |
|---------|-------------|
| **Document Processing** | Upload and automatically chunk documents |
| **AI Embeddings** | Generate vector representations using Groq/OpenAI |
| **Semantic Search** | Find documents by meaning, not just keywords |
| **User Management** | Secure access control for different user roles |
| **Real-time Status** | Track document processing progress |

## 🛠️ Technologies Used
<div align="center">

**Backend**  
![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-316192?style=for-the-badge&logo=postgresql&logoColor=white)
![PgVector](https://img.shields.io/badge/PgVector-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)

**AI/ML**  
![Groq](https://img.shields.io/badge/Groq-00A67E?style=for-the-badge&logo=groq&logoColor=white)
![OpenAI](https://img.shields.io/badge/OpenAI-412991?style=for-the-badge&logo=openai&logoColor=white)

**Frontend**  
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)

**Infrastructure**  
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![GitHub Actions](https://img.shields.io/badge/GitHub_Actions-2088FF?style=for-the-badge&logo=github-actions&logoColor=white)

</div>

## 🚀 Getting Started

### Prerequisites
- PHP 9.2+
- PostgreSQL 14+ with pgVector
- Composer 2.0+

### Installation
```bash
# Clone the repository
git clone https://github.com/yourusername/school-ai-assistant.git
cd school-ai-assistant

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=school_ai
DB_USERNAME=postgres
DB_PASSWORD=yourpassword

# Run migrations
php artisan migrate

# Start development server
php artisan serve