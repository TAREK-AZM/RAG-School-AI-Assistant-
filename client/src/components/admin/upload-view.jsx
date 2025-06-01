"use client"

import { Upload } from "lucide-react"
import { Button } from "../ui/button"
import { Card, CardContent, CardHeader, CardTitle } from "../ui/card"
import { Input } from "../ui/input"
import { Label } from "../ui/label"
import { Progress } from "../ui/progress"
import { Alert, AlertTitle, AlertDescription } from "../ui/alert"

export default function UploadView({ handleFileUpload, isUploading, uploadProgress, uploadStatus }) {
  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-semibold text-gray-900 mb-2">Upload New Document</h2>
        <p className="text-gray-600">Add a new document to your collection</p>
      </div>

      <Card>
        <CardHeader className="flex flex-row items-center space-x-3">
          <Upload className="w-5 h-5 text-gray-600" />
          <CardTitle>Document Upload Form</CardTitle>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleFileUpload} encType="multipart/form-data" className="space-y-6">
            <div className="space-y-2">
              <Label htmlFor="title">Document Title</Label>
              <Input id="title" name="title" placeholder="Enter document title" required />
            </div>

            <div className="space-y-2">
              <Label htmlFor="category">Category (Optional)</Label>
              <Input id="category" name="category" placeholder="Enter category" />
            </div>

            <div className="space-y-2">
              <Label htmlFor="documents">Document Files</Label>
              <Input id="documents" name="documents" type="file" multiple accept=".pdf,.doc,.docx,.txt" required />
              <p className="text-sm text-gray-500 mt-2">Accepted formats: PDF, DOC, DOCX, TXT (Max: 10MB)</p>
            </div>

            {isUploading && (
              <div className="space-y-2">
                <Progress value={uploadProgress} className="h-2" />
                <div className="text-right text-sm text-gray-600">{uploadProgress}%</div>
              </div>
            )}

            <Button type="submit" disabled={isUploading} className="w-full bg-indigo-600 hover:bg-indigo-700">
              <Upload className="w-4 h-4 mr-2" />
              {isUploading ? "Uploading..." : "Upload Document"}
            </Button>
          </form>

          {uploadStatus && (
            <Alert
              className={`mt-6 ${
                uploadStatus.type === "success"
                  ? "bg-green-50 border-green-200 text-green-800"
                  : "bg-red-50 border-red-200 text-red-800"
              }`}
            >
              <AlertTitle className="font-semibold">{uploadStatus.message}</AlertTitle>
              {uploadStatus.document && (
                <AlertDescription>
                  <div className="mt-2 text-sm">
                    <div>
                      <strong>Title:</strong> {uploadStatus.document.title}
                    </div>
                    <div>
                      <strong>Category:</strong> {uploadStatus.document.category || "None"}
                    </div>
                    <div className="flex items-center mt-2">
                      <span className="mr-2">
                        <strong>Status:</strong>
                      </span>
                      <span className="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">
                        Processing...
                      </span>
                    </div>
                  </div>
                </AlertDescription>
              )}
            </Alert>
          )}
        </CardContent>
      </Card>
    </div>
  )
}
