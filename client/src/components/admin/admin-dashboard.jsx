"use client"

import { useEffect, useState } from "react"
import AdminSidebar from "./admin-sidebar"
import AdminHeader from "./admin-header"
import DashboardView from "./dashboard-view"
import UploadView from "./upload-view"
import FileDetailsView from "./file-details-view"
import { API_UPLOAD_ENDPOINT, API_CHAT_ENDPOINT } from "../../config"

export default function AdminDashboard() {
  const [sidebarOpen, setSidebarOpen] = useState(false)
  const [currentView, setCurrentView] = useState("dashboard")
  const [selectedFile, setSelectedFile] = useState(null)
  const [uploadProgress, setUploadProgress] = useState(0)
  const [isUploading, setIsUploading] = useState(false)
  const [isDeleted, setIsDeleted] = useState(false)
  const [uploadStatus, setUploadStatus] = useState(null)
  const [fetchedDocuments, setFetchedDocuments] = useState([])

  // Mock data
  const stats = {
    totalDocuments: 6,
    uploadedToday: 2,
    categories: 3,
    storageUsed: "654 MB",
  }

  const recentDocuments = [
    {
      id: 1,
      title: "Annual Report 2023.pdf",
      category: "Financial",
      date: "2024-01-15 10:30",
      size: "4.2 MB",
      status: "completed",
    },
    {
      id: 2,
      title: "Employee Handbook.docx",
      category: "HR",
      date: "2024-01-15 09:15",
      size: "2.1 MB",
      status: "completed",
    },
    {
      id: 3,
      title: "Project Proposal.pdf",
      category: "Projects",
      date: "2024-01-14 16:45",
      size: "1.8 MB",
      status: "processing",
    },
    {
      id: 4,
      title: "Meeting Notes.txt",
      category: "General",
      date: "2024-01-14 14:20",
      size: "45 KB",
      status: "completed",
    },
    {
      id: 5,
      title: "Budget Analysis.xlsx",
      category: "Financial",
      date: "2024-01-14 11:30",
      size: "3.5 MB",
      status: "failed",
    },
  ]

  const handelFetchDocuments = async () => {
    const response = await fetch(`${API_CHAT_ENDPOINT}/school-assistant/documents`, {
      method: "GET",
      credentials : "include",
      headers: {
        "Content-Type": "application/json",
      },
    })

    if (!response.ok) {
      throw new Error(`API request failed with status ${response.status}`)
    }

    const data = await response.json()

    setFetchedDocuments(data.documents)
  }


  useEffect(() => {
    handelFetchDocuments()
  }, [isDeleted])

const handleFileUpload = async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);
  const title = formData.get("title");
  const category = formData.get("category");
  const files = formData.getAll("documents");

  if (!title || files.length === 0) {
    setUploadStatus({
      type: "error",
      message: "Please provide a title and at least one file",
    });
    return;
  }

  setIsUploading(true);
  setUploadProgress(0);
  setUploadStatus(null);

  try {
    const uploadData = new FormData();
    uploadData.append("title", title);
    uploadData.append("category", category || "General");
    uploadData.append("ModelAiProvider", "nomic"); // Default provider
    
    // Append all files
    files.forEach(file => {
      uploadData.append("documents[]", file);
    });

    const response = await fetch(`${API_CHAT_ENDPOINT}/school-assistant/upload`, {
      method: "POST",
      credentials: "include",
      body: uploadData, // No Content-Type header for FormData!
    });

    if (!response.ok) {
      throw new Error(await response.text());
    }

    const result = await response.json();
    setUploadStatus({
      type: "success",
      message: "Document uploaded successfully!",
      document: result.document
    });
    handelFetchDocuments(); // Refresh document list
  } catch (error) {
    setUploadStatus({
      type: "error",
      message: error.message || "Upload failed. Please try again.",
    });
  } finally {
    setIsUploading(false);
  }
};

  const viewFile = (file) => {
    setSelectedFile(file)
    setCurrentView("file-details")
  }

  const deleteFile = async (fileId) => {

    const response = await fetch(`${API_CHAT_ENDPOINT}/school-assistant/documents/${fileId}`, {
      method: "DELETE",
      credentials: "include",
      headers: {
        "Content-Type": "application/json",
      },
    })
    if (!response.ok) {
      window.alert("Document failed to delete")
    }
    if (response.ok) {
      window.alert("Document deleted successfully")
      setIsDeleted(true)
    }
    // if (window.confirm("Are you sure you want to delete this document?")) {
    //   console.log("Deleting file:", fileId)
    //   // Here you would make an API call to delete the file
    // }
  }

  return (
    <div className="flex h-[calc(100vh-104px)] bg-gray-50">
      <AdminSidebar
        sidebarOpen={sidebarOpen}
        setSidebarOpen={setSidebarOpen}
        currentView={currentView}
        setCurrentView={setCurrentView}
        recentDocuments={fetchedDocuments}
        viewFile={viewFile}
      />

      {/* Overlay for mobile */}
      {sidebarOpen && (
        <div className="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" onClick={() => setSidebarOpen(false)} />
      )}

      {/* Main Content */}
      <div className="flex-1 lg:ml-72">
        <AdminHeader currentView={currentView} setSidebarOpen={setSidebarOpen} />

        {/* Content */}
        <main className="p-6">
          {currentView === "dashboard" && (
          <DashboardView
                stats={stats}
                recentDocuments={fetchedDocuments}
                setCurrentView={setCurrentView}
                viewFile={viewFile}
                deleteFile={deleteFile}
              />
          )
          }
          {currentView === "upload" && (
            <UploadView
              handleFileUpload={handleFileUpload}
              isUploading={isUploading}
              uploadProgress={uploadProgress}
              uploadStatus={uploadStatus}
            />
          )}
          {currentView === "file-details" && (
            <FileDetailsView selectedFile={selectedFile} setCurrentView={setCurrentView} />
          )}
        </main>
      </div>
    </div>
  )
}
