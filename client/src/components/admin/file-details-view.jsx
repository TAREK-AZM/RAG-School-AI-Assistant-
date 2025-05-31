"use client"

import { FileText, Calendar, Folder, HardDrive, Download, Edit, Trash2, ArrowLeft } from "lucide-react"
import { Button } from "../ui/button"
import { Card, CardContent, CardHeader, CardTitle } from "../ui/card"

export default function FileDetailsView({ selectedFile, setCurrentView }) {
  if (!selectedFile) return null

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-semibold text-gray-900 mb-2">File Details</h2>
          <p className="text-gray-600">View and manage document information</p>
        </div>
        <Button variant="outline" onClick={() => setCurrentView("dashboard")}>
          <ArrowLeft className="w-4 h-4 mr-2" />
          Back to Dashboard
        </Button>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>{selectedFile.title}</CardTitle>
        </CardHeader>
        <CardContent className="space-y-6">
          <div className="flex items-start space-x-4">
            <div className="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
              <FileText className="w-6 h-6" />
            </div>
            <div className="flex-1">
              <h3 className="text-xl font-semibold text-gray-900 mb-2">{selectedFile.title}</h3>
              <div className="flex flex-wrap gap-4 text-sm text-gray-600">
                <div className="flex items-center space-x-1">
                  <Calendar className="w-4 h-4" />
                  <span>{selectedFile.date}</span>
                </div>
                <div className="flex items-center space-x-1">
                  <Folder className="w-4 h-4" />
                  <span>{selectedFile.category}</span>
                </div>
                <div className="flex items-center space-x-1">
                  <HardDrive className="w-4 h-4" />
                  <span>{selectedFile.size}</span>
                </div>
              </div>
            </div>
          </div>

          <div className="flex flex-wrap gap-3">
            <Button className="bg-indigo-600 hover:bg-indigo-700">
              <Download className="w-4 h-4 mr-2" />
              Download
            </Button>
            <Button variant="outline">
              <Edit className="w-4 h-4 mr-2" />
              Edit
            </Button>
            <Button variant="outline" className="text-red-600 border-red-200 hover:bg-red-50 hover:text-red-700">
              <Trash2 className="w-4 h-4 mr-2" />
              Delete
            </Button>
          </div>

          <div className="bg-gray-100 rounded-lg p-8 text-center">
            <FileText className="w-12 h-12 text-gray-400 mx-auto mb-4" />
            <p className="text-gray-600">Preview not available</p>
          </div>

          <Card>
            <CardHeader>
              <CardTitle className="text-base">Document History</CardTitle>
            </CardHeader>
            <CardContent className="p-0">
              <div className="divide-y divide-gray-200">
                <div className="flex items-center justify-between p-4">
                  <div>
                    <div className="font-medium text-gray-900">Uploaded</div>
                    <div className="text-sm text-gray-500">by System</div>
                  </div>
                  <span className="text-sm text-gray-500">{selectedFile.date}</span>
                </div>
                <div className="flex items-center justify-between p-4">
                  <div>
                    <div className="font-medium text-gray-900">Processing Completed</div>
                    <div className="text-sm text-gray-500">Document indexed</div>
                  </div>
                  <span className="text-sm text-gray-500">Today, 10:32 AM</span>
                </div>
                <div className="flex items-center justify-between p-4">
                  <div>
                    <div className="font-medium text-gray-900">Viewed</div>
                    <div className="text-sm text-gray-500">by John Doe</div>
                  </div>
                  <span className="text-sm text-gray-500">Today, 11:15 AM</span>
                </div>
              </div>
            </CardContent>
          </Card>
        </CardContent>
      </Card>
    </div>
  )
}
