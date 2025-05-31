# RAG Chat Dashboard - React

A modern React application for RAG-powered chat and document management that connects to Laravel and FastAPI backends.

## 🚀 Quick Start

1. **Install dependencies:**
   \`\`\`bash
   npm install
   \`\`\`

2. **Environment Setup:**
   The environment variables are already configured in Vercel:
   - `REACT_APP_CHAT_API_URL` - Your Laravel/FastAPI chat endpoint
   - `REACT_APP_UPLOAD_API_URL` - Your Laravel/FastAPI upload endpoint

3. **Start the development server:**
   \`\`\`bash
   npm start
   \`\`\`

4. **Open your browser:**
   Navigate to `http://localhost:3000`

## 🔌 API Integration

### Chat API
- **Endpoint:** `REACT_APP_CHAT_API_URL`
- **Method:** POST
- **Request Format:**
  \`\`\`json
  {
    "question": "User's question here"
  }
  \`\`\`
- **Expected Response:**
  \`\`\`json
  {
    "answer": "AI response text",
    "sources": [
      {
        "document_title": "Document Name",
        "content": "Relevant excerpt from document"
      }
    ]
  }
  \`\`\`

### Upload API
- **Endpoint:** `REACT_APP_UPLOAD_API_URL`
- **Method:** POST
- **Content-Type:** multipart/form-data
- **Form Fields:**
  - `title`: Document title
  - `category`: Document category (optional)
  - `documents[]`: File uploads (multiple files supported)

- **Expected Response:**
  \`\`\`json
  {
    "message": "Upload successful",
    "document_id": "unique_document_id",
    "status": "processing"
  }
  \`\`\`

## 🏗️ Project Structure

\`\`\`
src/
├── components/
│   ├── admin/           # Admin dashboard components
│   ├── chat/            # Chat interface components
│   ├── ui/              # Reusable UI components
│   └── layout.jsx       # Main layout component
├── lib/
│   └── utils.js         # Utility functions
├── config.js            # API configuration
├── App.jsx              # Main app component
├── index.js             # Entry point
└── index.css            # Global styles
\`\`\`

## 🎨 Features

- **Chat Interface**: Real-time chat with RAG-powered AI responses
- **Document Upload**: Multi-file upload with progress tracking
- **Responsive Design**: Works on all screen sizes
- **Error Handling**: Comprehensive error handling for API failures
- **File Validation**: Client-side file type and size validation
- **React Router**: Client-side routing for navigation

## 🔧 Customization

### Updating API Endpoints
Update the environment variables in your Vercel project settings or modify the `src/config.js` file for local development.

### Styling
The project uses Tailwind CSS with a custom design system. Modify `src/index.css` or individual component styles as needed.

### Adding New Features
- Add new routes in `src/App.jsx`
- Create new components in the appropriate directories
- Update the navigation in `src/components/layout.jsx`

## 📦 Build for Production

\`\`\`bash
npm run build
\`\`\`

This creates a `build` folder with optimized production files ready for deployment.

## 🚀 Deployment

The project is configured for deployment on Vercel with the environment variables already set up. Simply push your changes to trigger a new deployment.
