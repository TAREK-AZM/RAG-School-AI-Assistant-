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

**Frontend**  
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)


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

# Setup environment
cp .env.example .env
php artisan key:generate

## 🚀 Create docker container for pgVector to store embeddings "the esiest way"
# run the docker-compose file
docker-compose up -d    # or docker-compose up --build

# Configure database in .env
# set up the .env to match the following values of the container you created
DB_CONNECTION=pgsql
DB_HOST=localhost  # Use the container IP you found
DB_PORT=5433       # Host port you mapped in docker-compose
DB_DATABASE=School_AI_Assistant
DB_USERNAME=postgres
DB_PASSWORD=password

# Run migrations
php artisan migrate

# Start development server
php artisan serve


##  🛠️🛠️ the Keys you need to get the project running 🛠️🛠️
# 🌟🌟🌟 Groq 🌟🌟🌟 that will be used for generating answers
GROQ_API_KEY=sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
# 🌟🌟🌟 Nomic 🌟🌟🌟 that will be used for generating embeddings
# url 🌟: https://atlas.nomic.ai/
 https://atlas.nomic.ai/

NOMIC_API_KEY=sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
GROQ_MODEL=llama3-70b-8192
# Vector DB configuration
VECTORDB_CHUNK_SIZE=1000
VECTORDB_TOP_K=5
VECTORDB_SIMILARITY_THRESHOLD=0.7
```

