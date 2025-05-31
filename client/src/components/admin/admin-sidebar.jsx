"use client"

import { FileText, LayoutDashboard, Upload, X } from "lucide-react"
import { Button } from "../ui/button"
import { ScrollArea } from "../ui/scroll-area"

export default function AdminSidebar({
  sidebarOpen,
  setSidebarOpen,
  currentView,
  setCurrentView,
  recentDocuments,
  viewFile,
}) {
  return (
    <aside
      className={`fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0 ${
        sidebarOpen ? "translate-x-0" : "-translate-x-full"
      }`}
    >
      <div className="flex flex-col h-full">
        <div className="flex items-center justify-between p-6 border-b border-gray-200">
          <div className="flex items-center space-x-2">
            <div className="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
              <FileText className="w-5 h-5 text-white" />
            </div>
            <span className="text-xl font-bold text-gray-900">DocManager</span>
          </div>
          <Button variant="ghost" size="icon" onClick={() => setSidebarOpen(false)} className="lg:hidden">
            <X className="w-5 h-5" />
          </Button>
        </div>

        <ScrollArea className="flex-1">
          <div className="p-4 space-y-2">
            <Button
              variant={currentView === "dashboard" ? "secondary" : "ghost"}
              className={`w-full justify-start ${
                currentView === "dashboard" ? "bg-indigo-100 text-indigo-700 hover:bg-indigo-200" : ""
              }`}
              onClick={() => setCurrentView("dashboard")}
            >
              <LayoutDashboard className="w-5 h-5 mr-3" />
              Dashboard
            </Button>

            <Button
              variant={currentView === "upload" ? "secondary" : "ghost"}
              className={`w-full justify-start ${
                currentView === "upload" ? "bg-indigo-100 text-indigo-700 hover:bg-indigo-200" : ""
              }`}
              onClick={() => setCurrentView("upload")}
            >
              <Upload className="w-5 h-5 mr-3" />
              Upload Document
            </Button>
          </div>

          <div className="p-4 border-t border-gray-200">
            <div className="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Recent Documents</div>
            <div className="space-y-1">
              {recentDocuments.slice(0, 5).map((doc) => (
                <Button
                  key={doc.id}
                  variant="ghost"
                  className="w-full justify-start h-auto py-2"
                  onClick={() => viewFile(doc)}
                >
                  <FileText className="w-4 h-4 mr-3 text-gray-400" />
                  <div className="flex-1 min-w-0 text-left">
                    <div className="text-sm font-medium text-gray-900 truncate">
                      {doc.title.length > 20 ? doc.title.substring(0, 20) + "..." : doc.title}
                    </div>
                    <div className="text-xs text-gray-500">{new Date(doc.date).toLocaleDateString()}</div>
                  </div>
                </Button>
              ))}
            </div>
          </div>
        </ScrollArea>
      </div>
    </aside>
  )
}
