"use client"

import { Menu } from "lucide-react"
import { Button } from "../ui/button"
import { Avatar, AvatarFallback, AvatarImage } from "../ui/avatar"

export default function AdminHeader({ currentView, setSidebarOpen }) {
  return (
    <header className="bg-white border-b border-gray-200 px-6 py-4">
      <div className="flex items-center justify-between">
        <div className="flex items-center space-x-4">
          <Button variant="ghost" size="icon" onClick={() => setSidebarOpen(true)} className="lg:hidden">
            <Menu className="w-5 h-5" />
          </Button>
          <h1 className="text-xl font-semibold text-gray-900">
            {currentView === "dashboard" ? "Dashboard" : currentView === "upload" ? "Upload Document" : "File Details"}
          </h1>
        </div>

        <div className="flex items-center space-x-3">
          <Avatar>
            <AvatarImage src="/placeholder.svg?height=40&width=40" alt="Admin" />
            <AvatarFallback>JD</AvatarFallback>
          </Avatar>
          <div className="hidden sm:block">
            <div className="text-sm font-medium text-gray-900">John Doe</div>
            <div className="text-xs text-gray-500">Administrator</div>
          </div>
        </div>
      </div>
    </header>
  )
}
