"use client"

import { FileText, Calendar, Folder, HardDrive, TrendingUp, Upload, Eye, Trash2 } from "lucide-react"
import { Button } from "../ui/button"
import { Card, CardContent, CardHeader, CardTitle } from "../ui/card"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "../ui/table"
import { Badge } from "../ui/badge"

export default function DashboardView({ stats, recentDocuments, setCurrentView, viewFile, deleteFile }) {
  const StatCard = ({ icon: Icon, title, value, description, trend }) => (
    <Card>
      <CardContent className="pt-6">
        <div className="flex items-center justify-between mb-4">
          <div className="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
            <Icon className="w-6 h-6" />
          </div>
          {trend && (
            <div className="flex items-center text-green-600 text-sm">
              <TrendingUp className="w-4 h-4 mr-1" />
              {trend}
            </div>
          )}
        </div>
        <div className="text-sm text-gray-600 mb-1">{title}</div>
        <div className="text-2xl font-semibold text-gray-900 mb-1">{value}</div>
        <div className="text-sm text-gray-500">{description}</div>
      </CardContent>
    </Card>
  )

  const StatusBadge = ({ status }) => {
    const variants = {
      completed: "bg-green-100 text-green-800 hover:bg-green-100",
      processing: "bg-yellow-100 text-yellow-800 hover:bg-yellow-100",
      failed: "bg-red-100 text-red-800 hover:bg-red-100",
    }

    return (
      <Badge variant="outline" className={variants[status]}>
        {status.charAt(0).toUpperCase() + status.slice(1)}
      </Badge>
    )
  }

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-2xl font-semibold text-gray-900 mb-2">Document Management Dashboard</h2>
        <p className="text-gray-600">Overview of your document management system</p>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatCard
          icon={FileText}
          title="Total Documents"
          value={stats.totalDocuments}
          description="12% increase this month"
          trend="12%"
        />
        <StatCard
          icon={Calendar}
          title="Uploaded Today"
          value={stats.uploadedToday}
          description="3 more than yesterday"
          trend="3"
        />
        <StatCard icon={Folder} title="Categories" value={stats.categories} description="Most used: Financial" />
        <StatCard icon={HardDrive} title="Storage Used" value={stats.storageUsed} description="of 5 GB (24%)" />
      </div>

      {/* Recent Documents Table */}
      <Card>
        <CardHeader className="flex flex-row items-center justify-between">
          <CardTitle>Recent Documents</CardTitle>
          <Button onClick={() => setCurrentView("upload")} className="bg-indigo-600 hover:bg-indigo-700">
            <Upload className="w-4 h-4 mr-2" />
            New Upload
          </Button>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead>Category</TableHead>
                <TableHead>Date</TableHead>
                <TableHead>Size</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {recentDocuments.map((doc) => (
                <TableRow key={doc.id}>
                  <TableCell>
                    <div className="flex items-center">
                      <FileText className="w-5 h-5 text-indigo-600 mr-3" />
                      <span className="font-medium">{doc.title}</span>
                    </div>
                  </TableCell>
                  <TableCell>{doc.category}</TableCell>
                  <TableCell>{new Date(doc.date).toLocaleString()}</TableCell>
                  <TableCell>{doc.size}</TableCell>
                  <TableCell>
                    <StatusBadge status={doc.status} />
                  </TableCell>
                  <TableCell>
                    <div className="flex space-x-2">
                      <Button variant="ghost" size="icon" onClick={() => viewFile(doc)}>
                        <Eye className="w-4 h-4 text-indigo-600" />
                      </Button>
                      <Button variant="ghost" size="icon" onClick={() => deleteFile(doc.id)}>
                        <Trash2 className="w-4 h-4 text-red-600" />
                      </Button>
                    </div>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  )
}
