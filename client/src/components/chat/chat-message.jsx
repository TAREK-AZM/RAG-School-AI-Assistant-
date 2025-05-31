"use client"

import { useState } from "react"
import { Button } from "../ui/button"
import { User, Bot, Info } from "lucide-react"
import { Card, CardContent } from "../ui/card"

export default function ChatMessage({ message, toggleSources }) {
  const [showSources, setShowSources] = useState(false)

  const handleToggleSources = () => {
    setShowSources(!showSources)
    toggleSources()
  }

  return (
    <div className={`flex gap-4 max-w-4xl ${message.type === "user" ? "ml-auto flex-row-reverse" : ""}`}>
      {/* Avatar */}
      <div
        className={`w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 ${
          message.type === "user" ? "bg-indigo-100 text-indigo-600" : "bg-gray-100 text-gray-600"
        }`}
      >
        {message.type === "user" ? <User className="w-5 h-5" /> : <Bot className="w-5 h-5" />}
      </div>

      {/* Message Content */}
      <div className="flex flex-col space-y-1">
        <div
          className={`px-4 py-3 rounded-lg shadow-sm ${
            message.type === "user"
              ? "bg-indigo-600 text-white rounded-tr-none"
              : "bg-white text-gray-900 rounded-tl-none border border-gray-200"
          }`}
        >
          {message.content}
        </div>

        <div className={`text-xs text-gray-500 ${message.type === "user" ? "text-right" : "text-left"}`}>
          {message.time}
        </div>

        {/* Sources */}
        {message.sources && message.sources.length > 0 && (
          <div className="mt-3">
            <Button
              variant="ghost"
              size="sm"
              className="flex items-center space-x-2 text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 p-0 h-auto"
              onClick={handleToggleSources}
            >
              <Info className="w-4 h-4" />
              <span>
                {showSources ? "Hide" : "View"} {message.sources.length} source
                {message.sources.length > 1 ? "s" : ""}
              </span>
            </Button>

            {showSources && (
              <div className="mt-3 space-y-3 bg-gray-50 rounded-lg p-3">
                {message.sources.map((source, index) => (
                  <Card key={index} className="bg-white border-gray-200">
                    <CardContent className="p-3">
                      <div className="font-medium text-gray-900 text-sm mb-1">{source.document_title}</div>
                      <div className="text-gray-600 text-sm leading-relaxed">{source.content}</div>
                    </CardContent>
                  </Card>
                ))}
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  )
}
