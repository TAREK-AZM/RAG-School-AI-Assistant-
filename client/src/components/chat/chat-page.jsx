"use client"

import { useState, useRef, useEffect } from "react"
import { Button } from "../ui/button"
import { Textarea } from "../ui/textarea"
import { Badge } from "../ui/badge"
import { Send, Trash2 } from "lucide-react"
import ChatWelcome from "./chat-welcome"
import ChatMessage from "./chat-message"
import ChatTypingIndicator from "./chat-typing-indicator"
import { API_CHAT_ENDPOINT } from "../../config"

export default function ChatPage() {
  const [messages, setMessages] = useState([])
  const [inputValue, setInputValue] = useState("")
  const [isTyping, setIsTyping] = useState(false)
  const [showWelcome, setShowWelcome] = useState(true)
  const chatBodyRef = useRef(null)

  const exampleQuestions = [
    "What are the main features of the system?",
    "Can you summarize the document about ENSAT?",
    "What is RAG and how does it work?",
  ]

  useEffect(() => {
    if (chatBodyRef.current) {
      chatBodyRef.current.scrollTop = chatBodyRef.current.scrollHeight
    }
  }, [messages, isTyping])

  const getCurrentTime = () => {
    const now = new Date()
    return now.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" })
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!inputValue.trim()) return

    const userMessage = {
      id: Date.now(),
      type: "user",
      content: inputValue,
      time: getCurrentTime(),
    }

    setMessages((prev) => [...prev, userMessage])
    setInputValue("")
    setShowWelcome(false)
    setIsTyping(true)

    try {
      // Send request to the chat API
      const response = await fetch(API_CHAT_ENDPOINT, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ question: inputValue }),
      })

      if (!response.ok) {
        throw new Error(`API request failed with status ${response.status}`)
      }

      const data = await response.json()

      const aiMessage = {
        id: Date.now() + 1,
        type: "ai",
        content: data.answer || "I'm sorry, I couldn't process your request.",
        time: getCurrentTime(),
        sources: data.sources || [],
      }

      setMessages((prev) => [...prev, aiMessage])
    } catch (error) {
      console.error("Error sending message:", error)

      // Add error message
      const errorMessage = {
        id: Date.now() + 1,
        type: "ai",
        content: "I'm sorry, I encountered an error while processing your question. Please try again later.",
        time: getCurrentTime(),
      }

      setMessages((prev) => [...prev, errorMessage])
    } finally {
      setIsTyping(false)
    }
  }

  const handleExampleClick = (question) => {
    setInputValue(question)
  }

  const clearChat = () => {
    setMessages([])
    setShowWelcome(true)
  }

  const toggleSources = (messageId) => {
    setMessages((prev) => prev.map((msg) => (msg.id === messageId ? { ...msg, showSources: !msg.showSources } : msg)))
  }

  return (
    <div className="max-w-6xl mx-auto flex flex-col h-[calc(100vh-104px)] p-4">
      {/* Header */}
      <div className="flex items-center justify-between py-4 border-b border-gray-200 mb-4">
        <div className="flex items-center space-x-3">
          <h1 className="text-2xl font-semibold text-gray-900">AI Assistant</h1>
          <Badge variant="secondary" className="bg-indigo-100 text-indigo-700 hover:bg-indigo-200">
            RAG-powered
          </Badge>
        </div>
        <Button variant="outline" size="sm" onClick={clearChat} className="flex items-center gap-2">
          <Trash2 className="w-4 h-4" />
          <span className="hidden sm:inline">Clear Chat</span>
        </Button>
      </div>

      {/* Chat Body */}
      <div ref={chatBodyRef} className="flex-1 overflow-y-auto space-y-6 mb-4 pr-2">
        {/* Welcome Message */}
        {showWelcome && <ChatWelcome exampleQuestions={exampleQuestions} onQuestionClick={handleExampleClick} />}

        {/* Messages */}
        {messages.map((message) => (
          <ChatMessage key={message.id} message={message} toggleSources={() => toggleSources(message.id)} />
        ))}

        {/* Typing Indicator */}
        {isTyping && <ChatTypingIndicator />}
      </div>

      {/* Input Form */}
      <form onSubmit={handleSubmit} className="border-t border-gray-200 pt-4">
        <div className="relative">
          <Textarea
            value={inputValue}
            onChange={(e) => setInputValue(e.target.value)}
            placeholder="Type your question here..."
            className="min-h-[60px] max-h-[150px] pr-12 resize-none"
            onKeyDown={(e) => {
              if (e.key === "Enter" && !e.shiftKey) {
                e.preventDefault()
                handleSubmit(e)
              }
            }}
          />
          <Button
            type="submit"
            disabled={!inputValue.trim()}
            size="icon"
            className="absolute right-3 bottom-3 h-10 w-10 rounded-full bg-indigo-600 hover:bg-indigo-700"
          >
            <Send className="h-4 w-4" />
          </Button>
        </div>
      </form>
    </div>
  )
}
