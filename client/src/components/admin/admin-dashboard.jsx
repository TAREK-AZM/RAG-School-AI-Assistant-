"use client"

import { useState } from "react"
import AdminSidebar from "./admin-sidebar"
import AdminHeader from "./admin-header"
import DashboardView from "./dashboard-view"
import UploadView from "./upload-view"
import FileDetailsView from "./file-details-view"
import { API_UPLOAD_ENDPOINT } from "../../config"

export default function AdminDashboard() {
  const [sidebarOpen, setSidebarOpen] = useState(false)
  const [currentView, setCurrentView] = useState("dashboard")
  const [selectedFile, setSelectedFile] = useState(null)
  const [uploadProgress, setUploadProgress] = useState(0)
  const [isUploading, setIsUploading] = useState(false)
  const [uploadStatus, setUploadStatus] = useState(null)

  // Mock data
  const stats = {
    totalDocuments: 156,
    uploadedToday: 8,
    categories: 12,
    storageUsed: "1.2 GB",
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

  const handleFileUpload = async (e) => {
    e.preventDefault()

    const formData = new FormData(e.target)
    const title = formData.get("title")
    const category = formData.get("category")
    const files = formData.getAll("documents")

    if (!title || files.length === 0) {
      setUploadStatus({
        type: "error",
        message: "Please provide a title and at least one file",
      })
      return
    }

    setIsUploading(true)
    setUploadProgress(0)
    setUploadStatus(null)

    try {
      // Create a FormData object to send files
      const uploadData = new FormData()
      uploadData.append("title", title)
      uploadData.append("category", category)

      // Add all files
      for (let i = 0; i < files.length; i++) {
        uploadData.append("documents[]", files[i])
      }

      // Use XMLHttpRequest to track upload progress
      const xhr = new XMLHttpRequest()

      xhr.upload.addEventListener("progress", (event) => {
        if (event.lengthComputable) {
          const progress = Math.round((event.loaded / event.total) * 100)
          setUploadProgress(progress)
        }
      })

      xhr.addEventListener("load", () => {
        if (xhr.status >= 200 && xhr.status < 300) {
          const response = JSON.parse(xhr.responseText)
          setUploadStatus({
            type: "success",
            message: "Document uploaded successfully!",
            document: {
              title: title,
              category: category || "General",
            },
          })
        } else {
          setUploadStatus({
            type: "error",
            message: "Upload failed. Please try again.",
          })
        }
        setIsUploading(false)
      })

      xhr.addEventListener("error", () => {
        setUploadStatus({
          type: "error",
          message: "Upload failed. Please check your connection and try again.",
        })
        setIsUploading(false)
      })

      xhr.open("POST", API_UPLOAD_ENDPOINT)
      xhr.send(uploadData)
    } catch (error) {
      console.error("Error uploading file:", error)
      setUploadStatus({
        type: "error",
        message: "Upload failed. Please try again.",
      })
      setIsUploading(false)
    }
  }

  const viewFile = (file) => {
    setSelectedFile(file)
    setCurrentView("file-details")
  }

  const deleteFile = (fileId) => {
    if (window.confirm("Are you sure you want to delete this document?")) {
      console.log("Deleting file:", fileId)
      // Here you would make an API call to delete the file
    }
  }

  return (
    <div className="flex h-[calc(100vh-104px)] bg-gray-50">
      <AdminSidebar
        sidebarOpen={sidebarOpen}
        setSidebarOpen={setSidebarOpen}
        currentView={currentView}
        setCurrentView={setCurrentView}
        recentDocuments={recentDocuments}
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
              recentDocuments={recentDocuments}
              setCurrentView={setCurrentView}
              viewFile={viewFile}
              deleteFile={deleteFile}
            />
          )}
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
