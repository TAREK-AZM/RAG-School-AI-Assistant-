"use client"

import { Button } from "../ui/button"
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "../ui/card"

export default function ChatWelcome({ exampleQuestions, onQuestionClick }) {
  return (
    <Card className="border-gray-200 shadow-sm">
      <CardHeader className="text-center">
        <CardTitle className="text-2xl font-semibold text-gray-900">Welcome to the AI Assistant</CardTitle>
        <CardDescription className="text-gray-600 max-w-2xl mx-auto">
          Ask me anything about your documents. I'll use the knowledge from your uploaded files to provide accurate and
          relevant answers.
        </CardDescription>
      </CardHeader>
      <CardContent>
        <div className="flex flex-wrap gap-3 justify-center">
          {exampleQuestions.map((question, index) => (
            <Button
              key={index}
              variant="outline"
              className="bg-gray-100 hover:bg-indigo-100 hover:text-indigo-700 text-gray-700 rounded-full text-sm border-gray-200 hover:border-indigo-200"
              onClick={() => onQuestionClick(question)}
            >
              {question}
            </Button>
          ))}
        </div>
      </CardContent>
    </Card>
  )
}
